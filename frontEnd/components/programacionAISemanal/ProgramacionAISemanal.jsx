// Librerias
import React from 'react'
import moment from 'moment'
moment.locale('es')
import api from '../../apiClient/v1'
// Componentes
import BlackBoxSemanal from './BlackBoxSemanal'
import TablaAuditoriaSemanal from './TablaAuditoriaSemanal.jsx'
import RowAuditoriaSemanal from './RowAuditoriaSemanal.jsx'
import SelectRange from '../shared/SelectRange.jsx'

const format = 'YYYY-MM-DD'

class ProgramacionAISemanal extends React.Component {
    constructor(props) {
        super(props)
        this.blackbox = new BlackBoxSemanal()

        this.state = {
            idCliente: 2,       // Seleccionar FCV por defecto
            numeroLocal: '',
            fechaInicialSeleccionada: moment( moment().format('YYYY-MM-01') ),
            fechaFinalSeleccionada: moment().endOf('week'),
            // Auditorias y Filtros
            filtros: {},
            auditoriasFiltradas: []
        }

        // referencia a todos las entradas de fecha de los inventarios
        this.rows = []
    }
    componentWillMount(){
        // // al montar el componente, seleccionar el primer mes
        // this.seleccionarMes(this.state.meses[0].valor)
        this.buscarAuditorias()
    }
    componentWillReceiveProps(nextProps){
        // cuando se reciben nuevos elementos, se generand posiciones "vacias" en el arreglo de rows
        this.rows = this.rows.filter(input=>input!==null)
    }

    focusRow(index, nombreElemento){
        let ultimoIndex = this.rows.length-1
        if(index<0){
            // al seleccionar "antes de la primera", se seleciona el ultimo
            this.rows[ultimoIndex].focusElemento(nombreElemento)
        }else if(index>ultimoIndex){
            // al seleccionar "despues de la ultima", se selecciona el primero
            this.rows[ index%this.rows.length ].focusElemento(nombreElemento)
        }else{
            // no es ni el ultimo, ni el primero
            this.rows[index].focusElemento(nombreElemento)
        }
    }
    
    // Select de Cliente seleccionado
    onSelectClienteChanged(evt){
        this.setState({
            idCliente: evt.target.value
        }, ()=>{
            this.buscarAuditorias()
        })
    }
    onSelectRangoChanged(momentumFechaInicio, momentumFechaFinal){
        this.setState({
            fechaInicialSeleccionada: momentumFechaInicio,
            fechaFinalSeleccionada: momentumFechaFinal
        }, ()=>{
            let fechaInicio = this.state.fechaInicialSeleccionada.format('YYYY-MM-DD')
            let fechaFinal  = this.state.fechaFinalSeleccionada.format('YYYY-MM-DD')
            console.log(`rango seleccionado ${fechaInicio} al ${fechaFinal}`)
            // se llama al metodo seleccionar semana, que hace lo mismo
            this.buscarAuditorias(fechaInicio, fechaFinal)
        })
    }

    // Filtros
    actualizarFiltro(nombreFiltro, filtro){
        this.blackbox.reemplazarFiltro(nombreFiltro, filtro)
        // actualizar los filtros, y la lista ordenada de locales
        this.setState(this.blackbox.getListaFiltrada())
    }
    ordenarAuditorias(evt){
        evt.preventDefault()
        this.blackbox.ordenarLista()
        this.setState( this.blackbox.getListaFiltrada() )
    }

    // Llamadas al API
    // Todo: recibir el idCliente desde los metodos que lo llamen
    buscarAuditorias(){
        let idCliente = this.state.idCliente
        let fechaInicio = this.state.fechaInicialSeleccionada.format(format)
        let fechaFin = this.state.fechaFinalSeleccionada.format(format)

        api.auditoria.getPorRangoYCliente(fechaInicio, fechaFin, idCliente)
            .then(auditorias=>{
                console.log(`auditorias del rango ${fechaInicio} a ${fechaFin}, y cliente ${idCliente}`, auditorias)
                this.blackbox.reset()
                auditorias.forEach(auditoria=>this.blackbox.add(auditoria))

                this.blackbox.ordenarLista()
                this.setState( this.blackbox.getListaFiltrada() )        // {auditoriasFiltradas: ...}
            })
    }
    guardarAuditoria(idAuditoria, formInventario){
        api.auditoria.actualizar(idAuditoria, formInventario)
            .then(auditoriaActualizada=>{
                console.log('auditoria actualizada correctamente')
                // actualizar los datos y el state de la app
                this.blackbox.actualizarAuditoria(auditoriaActualizada)
                // actualizar los filtros, y la lista ordenada de locales
                this.setState( this.blackbox.getListaFiltrada() )        // {auditoriasFiltradas: ...}
            })
    }
    eliminarAuditoria(auditoria){
        if(this.props.puedeModificar!==true)
            return alert("No tiene los permisos para eliminar la Auditoria")

        console.log('eliminando ', auditoria)
        api.auditoria.eliminar(auditoria.idAuditoria)
            .then((resp)=>{
                this.blackbox.remove(auditoria.idAuditoria)
                // actualizar los filtros, y la lista ordenada de locales
                this.setState(this.blackbox.getListaFiltrada())
            })
            .catch(error=>console.error(error))
    }

    render(){
        let fechaInicial = this.state.fechaInicialSeleccionada.format(format)
        let fechaFinal = this.state.fechaFinalSeleccionada.format(format)
        return(
            <div>
                <h1>Programación Semanal AI</h1>
                <div>
                    {/* SELECTOR DE CLIENTE */}
                    <div className={'col-sm-2 form-group '}>
                        <label className="control-label" htmlFor="selectCliente">Cliente</label>
                        <select className="form-control"  name="selectCliente"
                                ref={ref=>this.inputIdCliente=ref}
                                value={this.state.idCliente}
                                onChange={this.onSelectClienteChanged.bind(this)}>
                            <option value="0">Todos</option>
                            {this.props.clientes.map((cliente, index)=>{
                                return <option key={index} value={cliente.idCliente}>{`${cliente.nombreCorto} - ${cliente.nombre}`}</option>
                            })}
                        </select>
                    </div>
                    {/* SELECTOR UN RANGO DE FECHAS */}
                    <div className={'col-sm-3 form-group '}>
                        <label className="control-label" htmlFor="selectSemana">Rango de Fecha</label>
                        <SelectRange
                            startDateSelected = {this.state.fechaInicialSeleccionada}
                            endDateSelected = {this.state.fechaFinalSeleccionada}
                            onInit={()=>{console.log("on init")}}
                            onRangeSelected = {this.onSelectRangoChanged.bind(this)}
                        />
                    </div>
                </div>

                <a className="btn btn-success btn-xs pull-right"
                   href={`/pdf/auditorias/${fechaInicial}/al/${fechaFinal}/cliente/${this.state.idCliente}`}
                >Exportar a Excel</a>

                <TablaAuditoriaSemanal
                    ordenarAuditorias={this.ordenarAuditorias.bind(this)}
                    filtros={this.state.filtros}
                    actualizarFiltro={this.actualizarFiltro.bind(this)}>

                    {this.state.auditoriasFiltradas.length===0
                        ? <tr><td colSpan="15" style={{textAlign: 'center'}}><b>No hay auditorias para mostrar en este periodo.</b></td></tr>
                        : this.state.auditoriasFiltradas.map((auditoria, index)=>{
                            let mostrarSeparador = false
                            let sgteInventario = this.state.auditoriasFiltradas[index+1]
                            if(sgteInventario)
                                mostrarSeparador = auditoria.aud_fechaProgramada!==sgteInventario.aud_fechaProgramada
                            return <RowAuditoriaSemanal
                                // Propiedades
                                puedeModificar={this.props.puedeModificar}
                                puedeRevisar={this.props.puedeRevisar}
                                key={auditoria.aud_idAuditoria}
                                index={index}
                                ref={ref=>this.rows[index]=ref}
                                mostrarSeparador={mostrarSeparador}
                                auditoria={auditoria}
                                auditores={this.props.auditores}
                                // Metodos
                                guardarAuditoria={this.guardarAuditoria.bind(this)}
                                eliminarAuditoria={this.eliminarAuditoria.bind(this)}
                                focusRow={this.focusRow.bind(this)}
                            />
                        })
                    }
                </TablaAuditoriaSemanal>
            </div>
        )
    }
}

ProgramacionAISemanal.propTypes = {
    puedeModificar: React.PropTypes.bool.isRequired,
    puedeRevisar: React.PropTypes.bool.isRequired,
    clientes: React.PropTypes.array.isRequired,
    auditores: React.PropTypes.array.isRequired
}

export default ProgramacionAISemanal