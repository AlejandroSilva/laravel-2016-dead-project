import React from 'react'
let PropTypes = React.PropTypes
import api from '../../apiClient/v1'
import Sticky from '../shared/react-sticky/sticky.js'
import StickyContainer from '../shared/react-sticky/container.js'

// Styles
import sharedStyles from '../shared/shared.css'
import styles from './TablaLocalesMensual.css'

class TablaLocalesMensual extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            locales: [],
        }
        this.inputFecha = []
        this.inputFechaOnKeyDown = this.inputFechaOnKeyDown.bind(this)
    }
    agregarLocal(nuevoLocal, mesAnno){
        let [mes, anno] = mesAnno.split('-')
        let locales = this.state.locales

        let localNoExiste = this.state.locales.find(local=>local.idLocal===nuevoLocal.idLocal)===undefined
        if( localNoExiste ){
            // buscar asincronicamente la informacion completa al servidor
            this.obtenerDatosLocal(nuevoLocal.idLocal)
            nuevoLocal.mesProgramado = mes
            nuevoLocal.annoProgramado = anno

            // actualizar la lista de locales
            locales.push(nuevoLocal)
            // actualizar la lista con los nuevos
            this.setState({
                locales: locales
            })
        }
        return localNoExiste
    }
    obtenerDatosLocal(idLocal){
        api.locales.getVerbose(idLocal)
            .then(informacionLocal=>{
                this.setState({
                    // actualizar los datos de la lista con la informacion obtenida por el api
                    locales: this.state.locales.map(local=>{
                        if(local.idLocal===informacionLocal.idLocal)
                            // mezclar los objetos
                            return Object.assign(local, informacionLocal)
                        else
                            return local
                    })
                })
            })
            .catch(error=>console.error(`error al obtener los datos de ${idLocal}`, error))
    }

    inputFechaOnKeyDown(evt){

        if((evt.keyCode===9 && evt.shiftKey===false) || evt.keyCode===40 || evt.keyCode===13){
            // 9 = tab, flechaAbajo = 40,  13 = enter
            // seleccionar el proximo elemento
            evt.preventDefault()
            let index = this.inputFecha.findIndex(input=>input===evt.target)
            let nextIndex = (index+1)%this.inputFecha.length
            let nextInput = this.inputFecha[nextIndex]
            nextInput.focus()
        }else if((evt.keyCode===9 && evt.shiftKey===true) || evt.keyCode===38){
            // flechaArriba = 38, shift+tab
            // seleccionar el elemento anterior
            evt.preventDefault()
            let index = this.inputFecha.findIndex(input=>input===evt.target)
            let prevIndex = index===0? this.inputFecha.length-1 : index-1
            let prevInput = this.inputFecha[prevIndex]
            prevInput.focus()
        }else{
            //console.log(evt.keyCode)
        }
    }
    render(){
        return (
            <div>
                {/* Table */}
                <StickyContainer type={React.DOM.table}  className="table table-bordered table-condensed">
                    <thead>
                        {/* TR que se pega al top de la pagina */}
                        <Sticky
                            topOffset={-50}
                            type={React.DOM.tr}
                            stickyStyle={{top: '50px'}}
                        >
                            <th className={styles.thCorrelativo}>#</th>
                            <th className={styles.thFecha}>Fecha</th>
                            <th className={styles.thCliente}>Cliente</th>
                            <th className={styles.thCeco}>Ceco</th>
                            <th className={styles.thLocal}>Local</th>
                            <th className={styles.thZonaSei}>Zona SEI</th>
                            <th className={styles.thRegion}>Región</th>
                            <th className={styles.thComuna}>Comuna</th>
                            <th className={styles.thStock}>Stock</th>
                            <th className={styles.thDotacion}>Dotación</th>
                            <th className={styles.thJornada}>Jornada</th>
                            <th className={styles.thOpciones}>Opciones</th>
                        </Sticky>
                    </thead>
                    <tbody>
                    {this.state.locales.map((local, index)=>{
                        let direccion = local.direccion || {}
                        let comuna = direccion.comuna || {}
                        let provincia = comuna.provincia || {}
                        let region = provincia.region || {}
                        let zona = region.zona || {}

                        return <tr key={index}>
                            <td className={styles.tdCorrelativo}>
                                {/* Correlativo */}
                                {index}
                            </td>
                            <td className={styles.tdFecha}>
                                {/* Fecha */}
                                <input className={styles.inputDia} type="number" min="0" max="31"
                                       ref={ref=>this.inputFecha[index]=ref}
                                       onBlur={input=>{
                                        console.log("lost focus, guardando")
                                       }}
                                       onKeyDown={this.inputFechaOnKeyDown}
                                />
                                <input className={styles.inputMes} type="number" defaultValue={local.mesProgramado} disabled/>
                                <input className={styles.inputAnno} type="number" defaultValue={local.annoProgramado} disabled/>
                            </td>
                            <td className={styles.tdCliente}>
                                {/* Cliente*/}
                                <p><small>{local.cliente? local.cliente.nombreCorto : '...'}</small></p>
                            </td>
                            <td className={styles.tdCeco}>
                                {/* CECO */}
                                <p><small><b>{local.numero? local.numero : '...'}</b></small></p>
                            </td>
                            <td className={styles.tdLocal}>
                                {/* Local */}
                                <p><small><b>{local.nombre? local.nombre : '...'}</b></small></p>
                            </td>
                            <td className={styles.tdZonaSei}>
                                {/* Zona */}
                                <p style={{margin:0}}><small>{zona.nombre? zona.nombre : '...'}</small></p>
                            </td>
                            <td className={styles.tdRegion}>
                                {/* Region*/}
                                <p style={{margin:0}}><small>{region.nombreCorto? region.nombreCorto : '...'}</small></p>
                            </td>
                            <td className={styles.tdComuna}>
                                {/* Comuna */}
                                <p style={{margin:0}}><b><small>{comuna.nombre? comuna.nombre : '...'}</small></b></p>
                            </td>
                            <td className={styles.tdStock}>
                                {/* Stock */}
                                <p><small>{local.stock? local.stock : '...'}</small></p>
                            </td>
                            <td className={styles.tdDotacion}>
                                {/* Dotación */}
                                <input className={styles.inputDotacionSugerida} type="text" defaultValue="99" disabled/>
                                <input className={styles.inputDotacionIngresada} type="number" tabIndex="-1"/>
                            </td>
                            <td className={styles.tdJornada}>
                                {/* Jornada */}
                                <p><small>{local.jornada? local.jornada.nombre : '(...jornada)'}</small></p>
                            </td>
                            <td className={styles.tdOpciones}>
                                {/* Opciones    */}
                                <button className="btn btn-sm btn-success" tabIndex="-1">Guardar</button>
                                <button className="btn btn-sm btn-primary   " tabIndex="-1">Editar local</button>
                            </td>
                        </tr>
                    })}
                    {/*
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                        <tr><td>1</td><td>12/12/12</td><td>99</td><td>cco</td><td>local1</td><td>zona 1</td><td>reg 1</td><td>ciudad 1</td><td>stock</td><td>dotacion</td><td>jornada </td><td>opciones</td></tr>
                     */}
                    </tbody>
                </StickyContainer>
            </div>
        )
    }
}

TablaLocalesMensual.protoTypes = {
    //localesAgregados: PropTypes.array.required
}
export default TablaLocalesMensual


//ToDo: agregar el boton confirmar