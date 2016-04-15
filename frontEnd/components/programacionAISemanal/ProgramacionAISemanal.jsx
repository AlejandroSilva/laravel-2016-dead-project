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
        this.blackboxSemanal = new BlackBoxSemanal()

        // mostrar en el selector, los proximos 12 meses
        // let meses = []
        // for (let desface = 0; desface < 12; desface++) {
        //     let mes = moment().add(desface, 'month')
        //     meses.push({
        //         valor: mes.format('YYYY-MM-00'),
        //         texto: mes.format('MMMM  YYYY')
        //     })
        // }

        this.state = {
//            meses,
//            semanas: [],
            idCliente: 2,       // Seleccionar FCV por defecto
            numeroLocal: '',
//            mesSeleccionado: '',
//            semanaSeleccionada: '',
            fechaInicialSeleccionada: moment(),
            fechaFinalSeleccionada: moment().add(1, 'month'),
            // Auditorias y Filtros
            filtroLocales: [],
            filtroRegiones: [],
            filtroComunas: [],
            filtroAuditores: [],
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
        // seleccionar "antes de la primera"
        if(index<0)
            index = ultimoIndex
        if(index>ultimoIndex)
            index = index%this.rows.length
       
        let nextRow = this.rows[index]
        nextRow.focusElemento(nombreElemento)
    }

    ordenarAuditorias(){
        this.blackboxSemanal.ordenarLista()
        this.setState( this.blackboxSemanal.getListaFiltrada() )
    }

    // Select de Cliente seleccionado
    onSelectClienteChanged(evt){
        this.setState({
            idCliente: evt.target.value
        }, ()=>{
            this.buscarAuditorias()
        })
    }
    // Select de Semana seleccionado
    // onSelectMesChanged(evt){
    //     this.seleccionarMes(evt.target.value)
    //     // this.setState({
    //     //     mesSeleccionado: evt.target.value
    //     // }, ()=>{
    //     //     // tdo generar las semanas
    //     // })
    // }
    // Select de Semana seleccionado
    // onSelectSemanaChanged(evt){
    //     this.seleccionarSemana(evt.target.value)
    // }
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
    // seleccionarSemana(fechaInicio, fechaFin){
    //     this.buscarAuditorias(fechaInicio, fechaFin)
    // }
    // seleccionarMes(mesSeleccionado){
    //     // console.log('mes seleccionado ', mes)
    //     // al seleccionar un mes, se deben generar sus semanas correspondientes
    //     const [anno, mes, dia]= mesSeleccionado.split('-')
    //     let primerDia = moment(`${anno}-${mes}`)
    //     let totalDiasMes = primerDia.daysInMonth()
    //     let ultimoDia = moment(`${anno}-${mes}-${totalDiasMes}`)
    //
    //     // // lunes y domingo de la semana del primer inventario
    //     let lunes = moment(primerDia).isoWeekday(1).day(1)
    //     let domingo = moment(primerDia).isoWeekday(1).day(7)
    //
    //     let semanas = []
    //     while(lunes<=ultimoDia){
    //         //console.log(`semana del ${lunes.format(format)} al ${domingo.format(format)}`)
    //         semanas.push({
    //             value: `${lunes.format(format)}/${domingo.format(format)}`,
    //             texto: `${lunes.format('DD MMMM')} - ${domingo.format('DD MMMM')}`
    //         })
    //         lunes.add(1, 'w')
    //         domingo.add(1, 'w')
    //     }
    //     this.setState({
    //         mesSeleccionado: mesSeleccionado,
    //         semanas: semanas
    //     }, ()=>{
    //         this.seleccionarSemana(semanas[0].value)
    //     })
    // }
    // seleccionarSemana(semanaSeleccionada){
    //     let [fechaInicio, fechaFin] = semanaSeleccionada.split('/')
    //     let momentFechaInicio = moment(fechaInicio)
    //     let momentFechaFin = moment(fechaFin)
    //
    //     console.log(semanaSeleccionada)
    //     this.setState({
    //         semanaSeleccionada: semanaSeleccionada,
    //         fechaInicialSeleccionada: momentFechaInicio,
    //         fechaFinalSeleccionada: momentFechaFin
    //     }, ()=>{
    //         this.buscarAuditorias()
    //     })
    // }

    onBuscarPorNumeroLocal(evt){
        //let numero = evt.target.value
    }

    // Filtros
    actualizarFiltro(nombreFiltro, filtro){
        this.blackboxSemanal.reemplazarFiltro(nombreFiltro, filtro)
        // actualizar los filtros, y la lista ordenada de locales
        this.setState(this.blackboxSemanal.getListaFiltrada())
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
                this.blackboxSemanal.reset()
                auditorias.forEach(auditoria=>this.blackboxSemanal.add(auditoria))

                this.blackboxSemanal.ordenarLista()
                this.setState( this.blackboxSemanal.getListaFiltrada() )        // {auditoriasFiltradas: ...}
            })
    }
    guardarAuditoria(idAuditoria, formInventario){
        if(this.props.puedeModificar!==true)
            return alert("No tiene los permisos para modificar la Auditoria")

        api.auditoria.actualizar(idAuditoria, formInventario)
            .then(auditoriaActualizada=>{
                console.log('auditoria actualizada correctamente')
                // actualizar los datos y el state de la app
                this.blackboxSemanal.actualizarAuditoria(auditoriaActualizada)
                // actualizar los filtros, y la lista ordenada de locales
                this.setState( this.blackboxSemanal.getListaFiltrada() )        // {auditoriasFiltradas: ...}
            })
    }
    eliminarAuditoria(auditoria){
        if(this.props.puedeModificar!==true)
            return alert("No tiene los permisos para eliminar la Auditoria")

        console.log('eliminando ', auditoria)
        api.auditoria.eliminar(auditoria.idAuditoria)
            .then((resp)=>{
                this.blackboxSemanal.remove(auditoria.idAuditoria)
                // actualizar los filtros, y la lista ordenada de locales
                this.setState(this.blackboxSemanal.getListaFiltrada())
            })
            .catch(error=>console.error(error))
    }

    render(){
        return(
            <div>
                <h1>Programación Semanal AI</h1>
                <div className="row">
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

                    {/* SELECTOR DE MES */}
                    {/*<div className={'col-sm-2 form-group '}>
                        <label className="control-label" htmlFor="selectMes">Mes</label>
                        <select className="form-control"  name="selectMes"
                                value={this.state.mesSeleccionado}
                                //ref={ref=>this.inputIdCliente=ref}
                                onChange={this.onSelectMesChanged.bind(this)}
                        >
                            {this.state.meses.map((mes,i)=>{
                                return <option key={i} value={mes.valor}>{mes.texto}</option>
                            })}
                        </select>
                    </div>
                    */}
                    {/* SELECTOR DE SEMANA */}
                    {/*
                    <div className={'col-sm-3 form-group '}>
                        <label className="control-label" htmlFor="selectSemana">Semana</label>
                        <select className="form-control"  name="selectSemana"
                                value={this.state.semanaSeleccionada}
                                //ref={ref=>this.inputIdCliente=ref}
                                onChange={this.onSelectSemanaChanged.bind(this)}>
//**** <option key={0} value=""></option> cuelga la app
                            {this.state.semanas.length===0?
                                null
                                :
                                this.state.semanas.map((semana, index)=>
                                    <option key={index} value={semana.value}>{semana.texto}</option>
                                )
                            }
                        </select>
                    </div>
                    */}
                    {/* SELECTOR UN RANGO DE FECHAS */}
                    <div className={'col-sm-4 form-group '}>
                        <label className="control-label" htmlFor="selectSemana">Rango de Fecha</label>
                        <SelectRange
                            startDateSelected = {this.state.fechaInicialSeleccionada}
                            endDateSelected = {this.state.fechaFinalSeleccionada}
                            onInit={()=>{console.log("on init")}}
                            onRangeSelected = {this.onSelectRangoChanged.bind(this)}
                        />
                    </div>
                    {/* SELECTOR DE NUMERO DE LCOAL */}
                    <div className={'col-sm-2 form-group '}>
                        <label className="control-label" htmlFor="selectSemana">Numero de Local</label>
                        <input type="number"className='form-control'
                               disabled
                        />
                    </div>
                    {/* BOTON BUSCAR */}
                    <div className={'col-sm-2 form-group '}>
                        <label className="control-label">{'\u00A0'}</label>
                        <button className='form-control btn btn-primary'
                                onClick={this.onBuscarPorNumeroLocal.bind(this)}
                                disabled
                        >Buscar</button>
                    </div>
                </div>

                <TablaAuditoriaSemanal
                    ordenarAuditorias={this.ordenarAuditorias.bind(this)}
                    filtroLocales={this.state.filtroLocales}
                    filtroRegiones={this.state.filtroRegiones}
                    filtroComunas={this.state.filtroComunas}
                    filtroAuditores={this.state.filtroAuditores}
                    actualizarFiltro={this.actualizarFiltro.bind(this)}>

                    {this.state.auditoriasFiltradas.length===0
                        ? <tr><td colSpan="15" style={{textAlign: 'center'}}><b>No hay inventarios para mostrar en este periodo.</b></td></tr>
                        : this.state.auditoriasFiltradas.map((auditoria, index)=>{
                            let mostrarSeparador = false
                            let sgteInventario = this.state.auditoriasFiltradas[index+1]
                            if(sgteInventario)
                                mostrarSeparador = auditoria.fechaProgramada!==sgteInventario.fechaProgramada
                            return <RowAuditoriaSemanal
                                // Propiedades
                                puedeModificar={this.props.puedeModificar}
                                key={index}
                                index={index}
                                ref={ref=>this.rows[index]=ref}
                                auditoria={auditoria}
                                // lideres={this.props.lideres}
                                // supervisores={this.props.supervisores}
                                // captadores={this.props.captadores}
                                mostrarSeparador={mostrarSeparador}
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
    clientes: React.PropTypes.array.isRequired,
    auditores: React.PropTypes.array.isRequired
}

export default ProgramacionAISemanal