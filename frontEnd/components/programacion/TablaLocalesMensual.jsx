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
            locales: []
        }
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
    render(){
        return (
            <div>

                <StickyContainer
                    type={React.DOM.table}
                    className="table table-bordered table-condensed"
                >

                        <thead>
                            <Sticky
                                topOffset={-50}
                                type={React.DOM.tr}
                                //stickyClass={styles.stickyClass}
                                stickyStyle={{top: '50px'}}
                                //stickyContainerClass="web"
                            >
                                <th><div id=""></div></th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>CECO</th>
                                <th>Local</th>
                                <th>Zona SEI</th>
                                <th>Región</th>
                                <th>Comuna</th>
                                <th>Stock</th>
                                <th>Dotación</th>
                                <th>Jornada</th>
                                <th>Opciones</th>
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
                            <td style={{verticalAlign: 'middle'}}>
                                {index}
                            </td>
                            <td style={{verticalAlign: 'middle', minWidth:'162px'}}>
                                {/* Fecha */}
                                <input className={styles.inputDia} type="number" min="0" max="31"
                                       onBlur={input=>{
                                        console.log("lost focus")
                                       }}
                                       onKeyDown={evt=>{
                                        console.log("down")
                                       }}
                                />
                                <input className={styles.inputMes} type="number" defaultValue={local.mesProgramado} disabled/>
                                <input className={styles.inputAnno} type="number" defaultValue={local.annoProgramado} disabled/>
                            </td>
                            <td style={{verticalAlign: 'middle'}}>
                                {/* Cliente*/}
                                <p><small>{local.cliente? local.cliente.nombreCorto : '...'}</small></p>
                            </td>
                            <td style={{verticalAlign: 'middle'}}>
                                {/* Numero */}
                                <p><small><b>{local.numero? local.numero : '...'}</b></small></p>
                            </td>
                            <td style={{verticalAlign: 'middle'}}>
                                {/* Local */}
                                <p><small><b>{local.nombre? local.nombre : '...'}</b></small></p>
                            </td>
                            <td style={{verticalAlign: 'middle'}}>
                                {/* Zona */}
                                <p style={{margin:0}}><small>{zona.nombre? zona.nombre : '...'}</small></p>
                            </td>
                            <td style={{verticalAlign: 'middle'}}>
                                {/* Region*/}
                                <p style={{margin:0}}><small>{region.nombreCorto? region.nombreCorto : '...'}</small></p>
                            </td>
                            <td style={{verticalAlign: 'middle'}}>
                                {/* Comuna */}
                                <p style={{margin:0}}><b><small>{comuna.nombre? comuna.nombre : '...'}</small></b></p>
                            </td>
                            <td style={{verticalAlign: 'middle'}}>
                                {/* Stock */}
                                <p><small>{local.stock? local.stock : '...'}</small></p>
                            </td>
                            <td style={{verticalAlign: 'middle'}}>
                                {/* Dotación */}
                                <input className={styles.inputDotacionSugerida} type="text" defaultValue="99" disabled/>
                                <input className={styles.inputDotacionIngresada} type="number" tabIndex="-1"/>
                            </td>
                            <td style={{verticalAlign: 'middle'}}>
                                {/* Jornada */}
                                <p><small>{local.jornada? local.jornada.nombre : '(...jornada)'}</small></p>
                            </td>
                            <td style={{verticalAlign: 'middle'}}>
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

                {/*<ul>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                    <li>"mensaje"</li>
                </ul>*/}
            </div>
        )
    }
}

TablaLocalesMensual.protoTypes = {
    //localesAgregados: PropTypes.array.required
}
export default TablaLocalesMensual


//ToDo: agregar el boton confirmar