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

const format = 'YYYY-MM-DD'

class ProgramacionIGSemanal extends React.Component {
    constructor(props) {
        super(props)
        this.blackbox = new BlackBoxIGSemanal()

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
            // meses,
            // semanas: [],
            idCliente: 0,
            // mesSeleccionado: '',
            // semanaSeleccionada: '',
            fechaInicialSeleccionada: moment(),
            // fechaFinalSeleccionada: moment().add(7, 'days'),
            fechaFinalSeleccionada: moment().add(1, 'month'),
            // Inventarios y Filtros
            filtros: {},
            inventariosFiltrados: []
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
        }else if(index>ultimoIndex){
            // al seleccionar "despues de la ultima", se selecciona el primero
            this.rows[ index%this.rows.length ].focusElemento(nombreElemento)
        }else{
            // no es ni el ultimo, ni el primero
            this.rows[index].focusElemento(nombreElemento)
        }
    }

    // Metodos de los hijos
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
    // seleccionarSemana(fechaInicio, fechaFin){
    //     this.buscarInventarios(fechaInicio, fechaFin)
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
    //         this.buscarInventarios()
    //     })
    // }

    // Todo: recibir el idCliente desde los metodos que lo llamen
    buscarInventarios(){
        let idCliente = this.state.idCliente
        let fechaInicio = this.state.fechaInicialSeleccionada.format(format)
        let fechaFin = this.state.fechaFinalSeleccionada.format(format)

        api.inventario.getPorRangoYCliente(fechaInicio, fechaFin, idCliente)
            .then(inventarios=>{
                console.log(`inventarios del rango ${fechaInicio} a ${fechaFin}, y cliente ${idCliente}`, inventarios)
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
    
    render(){
        let fechaInicial = this.state.fechaInicialSeleccionada.format(format)
        let fechaFinal = this.state.fechaFinalSeleccionada.format(format)

        return(
            <div>
                <h1>Programación semanal IG</h1>
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
                    </div>*/}

                    {/* SELECTOR DE SEMANA */}
                    {/*
                    <div className={'col-sm-3 form-group '}>
                        <label className="control-label" htmlFor="selectSemana">Semana</label>
                        <select className="form-control"  name="selectSemana"
                                value={this.state.semanaSeleccionada}
                                //ref={ref=>this.inputIdCliente=ref}
                                onChange={this.onSelectSemanaChanged.bind(this)}>
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
                    <div className={'col-sm-3 form-group '}>
                        <label className="control-label" htmlFor="selectSemana">Rango de Fecha</label>
                        <SelectRange
                            startDateSelected = {this.state.fechaInicialSeleccionada}
                            endDateSelected = {this.state.fechaFinalSeleccionada}
                            onInit={()=>{console.log("on init")}}
                            onRangeSelected = {this.onSelectRangoChanged.bind(this)}
                        />
                    </div>

                    {/* SELECTOR DE NUMERO DE LOCAL */}
                    <div className={'col-sm-2 form-group '}>
                        <label className="control-label" htmlFor="selectSemana">Numero de Local</label>
                        <input type="number"className='form-control'
                               disabled
                        />
                    </div>
                </div>
                
                <a className="btn btn-success btn-xs pull-right"
                   href={`/pdf/inventarios/${fechaInicial}/al/${fechaFinal}/cliente/${this.state.idCliente}`}
                   disabled
                >Exportar a Excel</a>
                
                <TablaSemanal
                    ordenarInventarios={this.ordenarInventarios.bind(this)}
                    // Filtros
                    filtros={this.state.filtros}
                    actualizarFiltro={this.actualizarFiltro.bind(this)}
                >
                    {this.state.inventariosFiltrados.length===0
                        ? <tr><td colSpan="16" style={{textAlign: 'center'}}><b>No hay inventarios para mostrar en este periodo.</b></td></tr>
                        : this.state.inventariosFiltrados.map((inventario, index)=>{
                        let mostrarSeparador = false
                        let sgteInventario = this.state.inventariosFiltrados[index+1]
                        if(sgteInventario)
                            mostrarSeparador = inventario.fechaProgramada!==sgteInventario.fechaProgramada
                        return <RowInventarioSemanal
                            // Propiedades
                            puedeModificar={this.props.puedeModificar}
                            key={index}
                            index={index}
                            ref={ref=>this.rows[index]=ref}
                            inventario={inventario}
                            lideres={this.props.lideres}
                            captadores={this.props.captadores}
                            mostrarSeparador={mostrarSeparador}
                            // Metodos
                            guardarInventario={this.guardarInventario.bind(this)}
                            guardarNomina={this.guardarNomina.bind(this)}
                            focusRow={this.focusRow.bind(this)}
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
    //supervisores: React.PropTypes.array.isRequired, // se recibe pero se ignoran por ahora
    lideres: React.PropTypes.array.isRequired
}
ProgramacionIGSemanal.defaultProps = {
    puedeModificar: false
}
export default ProgramacionIGSemanal