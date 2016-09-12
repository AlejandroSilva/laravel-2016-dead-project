// Librerias
import React from 'react'
import moment from 'moment'
moment.locale('es')
import api from '../../apiClient/v1'
// Componentes
import BlackBoxIGSemanal from './BlackBoxIGSemanal.js'
import TablaSemanal from './TablaSemanal.jsx'
import RowInventarioSemanal from './RowInventarioSemanal.jsx'
import SelectRange from '../shared/SelectRange.jsx'
import Modal from 'react-bootstrap/lib/Modal.js'
import { CalendarContainer } from '../programacionIG_calendario/CalendarContainer.jsx'
import cssModal from './modal.css'

const format = 'YYYY-MM-DD'

class ProgramacionIGSemanal extends React.Component {
    constructor(props) {
        super(props)
        this.blackbox = new BlackBoxIGSemanal()

        this.state = {
            idCliente: 0,
            // seleccionar el mes completo
            // fechaInicialSeleccionada: moment( moment().format('YYYY-MM-01') ),
            // fechaFinalSeleccionada: moment().endOf('month'),
            fechaInicialSeleccionada: moment(),
            fechaFinalSeleccionada: moment().endOf('week'),
            // Inventarios y Filtros
            filtros: {},
            inventariosFiltrados: [],
            opcionesLideres: props.lideres.map(usuario=>{
                return {valor: usuario.id, texto:`${usuario.nombre1} ${usuario.apellidoPaterno}`}
            }),
            opcionesSupervisores: props.supervisores.map(usuario=>{
                 return {valor: usuario.id, texto:`${usuario.nombre1} ${usuario.apellidoPaterno}`}
            }),
            captadoresDisponibles: props.captadores.map(usuario=>{
                return {valor: usuario.id, texto:`${usuario.nombre1} ${usuario.apellidoPaterno}`}
            })
        }

        // referencia a todos las entradas de fecha de los inventarios
        this.rows = []
    }
    componentWillMount(){
        // al montar el componente, seleccionar el primer mes
        this.buscarInventarios(this.state.fechaInicialSeleccionada.format(format), this.state.fechaFinalSeleccionada.format(format))
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
        }else if(index>=ultimoIndex){
            // al seleccionar "despues de la ultima", se selecciona el primero
            this.rows[ index%this.rows.length ].focusElemento(nombreElemento)
        }else{
            // no es ni el ultimo, ni el primero
            this.rows[index].focusElemento(nombreElemento)
        }
    }

    // Metodos de los hijos
    lideresDisponibles(idNomina){
        return api.nomina.lideresDisponibles(idNomina)
    }
    guardarInventario(idInventario, formInventario){
        api.inventario.actualizar(idInventario, formInventario)
            .then(inventarioActualizado=>{
                console.log('inventario actualizado correctamente')
                // actualizar los datos y el state de la app
                this.blackbox.actualizarInventario(inventarioActualizado)
                // actualizar los filtros, y la lista ordenada de locales
                this.setState( this.blackbox.getListaFiltrada() )        // {inventariosFiltrados: ...}
            })
    }
    guardarNomina(idNomina, datos){
        api.nomina.actualizar(idNomina, datos)
            .then(inventarioActualizado=>{
                console.log('nomina actualizada correctamente')
                // actualizar los datos y el state de la app
                this.blackbox.actualizarInventario(inventarioActualizado)
                // actualizar los filtros, y la lista ordenada de locales
                this.setState( this.blackbox.getListaFiltrada() )        // {inventariosFiltrados: ...}
            })
    }
    ordenarInventarios(evt){
        evt.preventDefault()
        this.blackbox.ordenarLista()
        this.setState( this.blackbox.getListaFiltrada() )
    }

    // Select de Cliente seleccionado
    onSelectClienteChanged(evt){
        this.setState({
            idCliente: evt.target.value
        }, ()=>{
            this.buscarInventarios()
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
            this.buscarInventarios(fechaInicio, fechaFinal)
        })
    }

    // Todo: recibir el idCliente desde los metodos que lo llamen
    buscarInventarios(){
        let idCliente = this.state.idCliente
        let fechaInicio = this.state.fechaInicialSeleccionada.format(format)
        let fechaFin = this.state.fechaFinalSeleccionada.format(format)

        api.inventario.buscar2(fechaInicio, fechaFin, idCliente)
            .then(inventarios=>{
                //console.log(`inventarios del rango ${fechaInicio} a ${fechaFin}, y cliente ${idCliente}`, inventarios)
                this.blackbox.reset()
                inventarios.forEach(inventario=>this.blackbox.add(inventario))

                this.blackbox.ordenarLista()
                this.setState( this.blackbox.getListaFiltrada() )        // {inventariosFiltrados: ...}
            })
    }
    actualizarFiltro(nombreFiltro, filtro){
        this.blackbox.reemplazarFiltro(nombreFiltro, filtro)
        // actualizar los filtros, y la lista ordenada de locales
        this.setState(this.blackbox.getListaFiltrada())
    }
    nomina_agregarCaptador(idNomina, idCaptador){
        api.nomina.agregarCaptador(idNomina, idCaptador)
            .then(inventarioActualizado=>{
                this.blackbox.actualizarInventario(inventarioActualizado)
                this.setState( this.blackbox.getListaFiltrada() )
            })
            .catch(error=>{
                console.log('error al agregar el captador', error)
            })
    }
    nomina_quitarCaptador(idNomina, idCaptador){
        api.nomina.quitarCaptador(idNomina, idCaptador)
            .then(inventarioActualizado=>{
                this.blackbox.actualizarInventario(inventarioActualizado)
                this.setState( this.blackbox.getListaFiltrada() )
            })
            .catch(error=>{
                console.log('error al quitar el captador', error)
            })
    }
    nomina_cambiarAsignadosCaptador(idNomina, idCaptador, asignados){
        //console.log('nomina_cambiarAsignadosCaptador', idNomina, idCaptador, asignados)

        api.nomina.cambiarAsignadosCaptador(idNomina, idCaptador, {asignados})
            .then(inventarioActualizado=>{
                this.blackbox.actualizarInventario(inventarioActualizado)
                this.setState( this.blackbox.getListaFiltrada() )
            })
            .catch(error=>{
                console.log('error al asignar operadores al captador', error)
            })
    }

    render(){
        let fechaInicial = this.state.fechaInicialSeleccionada.format(format)
        let fechaFinal = this.state.fechaFinalSeleccionada.format(format)

        return(
            <div>
                <h1>Programación semanal IG</h1>
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
                                //return <option key={index} value={cliente.idCliente}>{"asdas" + cliente.nombre}</option
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
                   href={`/pdf/inventarios/${fechaInicial}/al/${fechaFinal}/cliente/${this.state.idCliente}`}>
                    Exportar a Excel
                </a>
                
                <BotonVistaAlternativa />
                
                <TablaSemanal
                    ordenarInventarios={this.ordenarInventarios.bind(this)}
                    // Filtros
                    filtros={this.state.filtros}
                    actualizarFiltro={this.actualizarFiltro.bind(this)}
                >
                    {
                        //true
                        this.state.inventariosFiltrados.length===0
                        ? <tr><td colSpan="21" style={{textAlign: 'center'}}><b>No hay inventarios para mostrar en este periodo.</b></td></tr>
                        : this.state.inventariosFiltrados.map((inventario, index)=>{
                        let mostrarSeparador = false
                        let sgteInventario = this.state.inventariosFiltrados[index+1]
                        if(sgteInventario)
                            mostrarSeparador = inventario.inv_fechaProgramada!==sgteInventario.inv_fechaProgramada
                        return <RowInventarioSemanal
                            // Propiedades
                            puedeModificar={this.props.puedeModificar}
                            key={inventario.inv_idInventario}
                            index={index}
                            ref={ref=>this.rows[index]=ref}
                            inventario={inventario}
                            opcionesLideres={this.state.opcionesLideres}
                            opcionesSupervisores={this.state.opcionesSupervisores}
                            captadoresDisponibles={this.state.captadoresDisponibles}
                            mostrarSeparador={mostrarSeparador}
                            // Metodos
                            lideresDisponibles={this.lideresDisponibles.bind(this)}
                            guardarInventario={this.guardarInventario.bind(this)}
                            focusRow={this.focusRow.bind(this)}
                            // Metodos para modificar nomina
                            guardarNomina={this.guardarNomina.bind(this)}
                            agregarCaptador={this.nomina_agregarCaptador.bind(this)}
                            quitarCaptador={this.nomina_quitarCaptador.bind(this)}
                            cambiarAsignados={this.nomina_cambiarAsignadosCaptador.bind(this)}
                        />
                    })}
                </TablaSemanal>
            </div>
        )
    }
}

ProgramacionIGSemanal.propTypes = {
    puedeModificar: React.PropTypes.bool.isRequired,
    clientes: React.PropTypes.array.isRequired,
    captadores: React.PropTypes.array.isRequired,
    supervisores: React.PropTypes.array.isRequired, // se recibe pero se ignoran por ahora
    lideres: React.PropTypes.array.isRequired
}
ProgramacionIGSemanal.defaultProps = {
    puedeModificar: false
}
export default ProgramacionIGSemanal

/* ****************************************************************************************************************** */

class BotonVistaAlternativa extends React.Component {
    constructor(props){
        super(props)
        this.state = {
            modalVisible: false
        }

        // mostrar en el selector, los proximos 12 meses
        this.meses = []
        for (let desface = 0; desface < 12; desface++) {
            let mes = moment().add(desface, 'month')
            this.meses.push({
                anno: mes.year(),
                month: mes.month(),
                valor: mes.format('YYYY-MM-01'),    // debe comenzar con el dia 1, o si no el webservice toma el mes anterior
                texto: mes.format('MMMM YYYY')
            })
        }

        this.showModal = ()=>{
            this.setState({modalVisible: true})
        }
        this.hideModal =()=>{
            this.setState({modalVisible: false})
        }
    }
    render(){
        return <div>
            <a className="btn btn-primary btn-xs pull-right"
               onClick={this.showModal}>
                Vista alternativa
            </a>
            <Modal
                show={this.state.modalVisible}
                onHide={this.hideModal}
                dialogClassName={cssModal.modalAmplio}>
                <Modal.Header closeButton>
                    <Modal.Title>Programación IG Mensual</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <CalendarContainer
                        meses={this.meses}
                    />
                </Modal.Body>
            </Modal>
        </div>
    }
}