// Librerias
import React from 'react'
import moment from 'moment'
moment.locale('es')
import api from '../../apiClient/v1'
// Componentes
import BlackBoxSemanal from './BlackBoxSemanal'
import TablaInventarios from './TablaSemanal.jsx'
import DateRangePicker from 'react-bootstrap-daterangepicker'
//var DateRangePicker = require('react-bootstrap-daterangepicker');

const format = 'YYYY-MM-DD'

class ProgramacionSemanal extends React.Component {
    constructor(props) {
        super(props)
        this.blackboxSemanal = new BlackBoxSemanal()
        
        let semanas = []

        // Calcular el rango de semanas disponibles para seleccionar
        if(this.props.primerInventario!=='' && this.props.ultimoInventario!==''){
            // moment con 'dia' == '00' da problemas, entonces se modifica si es necesario
            const [anno1, mes1, dia1]= this.props.primerInventario.split('-')
            const [anno2, mes2, dia2]= this.props.ultimoInventario.split('-')
            let fechaPrimerInventario = dia1!=='00'? moment(this.props.primerInventario) : moment(`${anno1}-${mes1}`)
            let fechaUltimoInventario = dia2!=='00'? moment(this.props.ultimoInventario) : moment(`${anno2}-${mes2}`)

            // lunes y domingo de la semana del primer inventario
            let lunes = moment(fechaPrimerInventario).isoWeekday(1).day(1)
            let domingo = moment(fechaPrimerInventario).isoWeekday(1).day(7)

            while(lunes<=fechaUltimoInventario){
                //console.log(`semana del ${lunes.format(format)} al ${domingo.format(format)}`)
                semanas.push({
                    value: `${lunes.format(format)}/${domingo.format(format)}`,
                    texto: `${lunes.format(format)} al ${domingo.format(format)}`
                })
                lunes.add(1, 'w')
                domingo.add(1, 'w')
            }
        }else{
            // no hay un primer inventario (¿no hay ningun inventario?)
            console.error('no hay inventarios en el sistema? no se detecto correctamente la fecha del primero de ellos')
        }
        this.state = {
            semanas,
            inventariosFiltrados: []
        }
        // seleccionar la primera semana
        if(semanas[0]){
            let [fechaInicio, fechaFin] = semanas[0].value.split('/')
            this.seleccionarSemana(fechaInicio, fechaFin)
        }
    }
    seleccionarSemana(fechaInicio, fechaFin){
        api.inventario.getPorRango(fechaInicio, fechaFin)
            .then(inventarios=>{
                console.log(`inventarios del rango ${fechaInicio} a ${fechaFin}: `, inventarios)
                this.blackboxSemanal.reset()
                inventarios.forEach(inventario=>this.blackboxSemanal.add(inventario))

                this.blackboxSemanal.ordenarLista()
                this.setState( this.blackboxSemanal.getListaFiltrada() )        // {inventariosFiltrados: ...}
            })
    }
    onChangeSelectSemana(evt){
        let semana = evt.target.value
        let [fechaInicio, fechaFin] = semana.split('/')
        this.seleccionarSemana(fechaInicio, fechaFin)
    }

    // Metodos de los hijos
    guardarInventario(idInventario, formInventario){
        api.inventario.actualizar(idInventario, formInventario)
            .then(inventarioActualizado=>{
                console.log('inventario actualizado correctamente')
                // actualizar los datos y el state de la app
                this.blackboxSemanal.actualizarInventario(inventarioActualizado)
                // actualizar los filtros, y la lista ordenada de locales
                this.setState( this.blackboxSemanal.getListaFiltrada() )        // {inventariosFiltrados: ...}
            })
    }
    guardarNomina(idNomina, datos){
        api.nomina.actualizar(idNomina, datos)
            .then(inventarioActualizado=>{
                console.log('nomina actualizada correctamente')
                // actualizar los datos y el state de la app
                this.blackboxSemanal.actualizarInventario(inventarioActualizado)
                // actualizar los filtros, y la lista ordenada de locales
                this.setState( this.blackboxSemanal.getListaFiltrada() )        // {inventariosFiltrados: ...}
            })
    }
    ordenarInventarios(){
        this.blackboxSemanal.ordenarLista()
        this.setState( this.blackboxSemanal.getListaFiltrada() )
    }

    render(){
        return(
            <div>
                <h1>Programación semanal</h1>
                <div className="row">
                    <div className="col-md-3">
                        <h4 className="page-header">Semana a programar</h4>
                        <select name="" id="" onChange={this.onChangeSelectSemana.bind(this)}>
                            {this.state.semanas.length===0?
                                <option key={0} value="-1">Sin inventarios</option>
                                :
                                this.state.semanas.map((semana, index)=>
                                    <option key={index} value={semana.value}>{semana.texto}</option>
                                )
                            }
                        </select>
                    </div>
                    <div className="col-md-3">
                        <h4 className="page-header">Rango de Fecha</h4>
                        {/*
                        <DateRangePicker
                            //startDate={moment('2016-03-06', format)} endDate={moment('2016-04-06', format)}>
                            startDate={'2016-03-06'} endDate={'2016-04-06'}>

                            <div>Click Me To Open Picker!</div>
                        </DateRangePicker>
                         */}
                    </div>

                </div>

                <TablaInventarios
                    lideres={window.laravelLideres}
                    supervisores={window.laravelSupervisores}
                    captadores={window.laravelCaptadores}
                    inventarios={this.state.inventariosFiltrados}
                    guardarInventario={this.guardarInventario.bind(this)}
                    guardarNomina={this.guardarNomina.bind(this)}
                    ordenarInventarios={this.ordenarInventarios.bind(this)}
                />
            </div>
        )
    }
}

ProgramacionSemanal.propTypes = {
    primerInventario: React.PropTypes.string.isRequired,
    ultimoInventario: React.PropTypes.string.isRequired
}

export default ProgramacionSemanal