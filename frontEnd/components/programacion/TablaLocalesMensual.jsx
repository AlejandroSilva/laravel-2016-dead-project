import React from 'react'
let PropTypes = React.PropTypes
import api from '../../apiClient/v1'

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
            <table className="table table-bordered table-condensed">
                <thead><tr>
                    <th><div id=""></div></th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    {/*
                    <th className="dropdown">
                        <select className="form-control" name="cliente">
                            <option value="-1" disabled>--</option>
                        </select>
                    </th>*/}
                {/*
                 <a className="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"> Cliente<span className="caret"></span> </a>
                 <ul className="dropdown-menu">
                 <li><a href="#">Action</a></li>
                 <li><a href="#">Another action</a></li>
                 <li><a href="#">Something else here</a></li>
                 <li role="separator" className="divider"></li>
                 <li><a href="#">Separated link</a></li>
                 </ul>
                 */}
                    <th>CECO</th>
                    <th>Local</th>
                    <th>Zona SEI</th>
                    <th>Región</th>
                    <th>Ciudad</th>
                    <th>Stock</th>
                    <th>Dotación</th>
                    <th>Jornada</th>
                    <th>Opciones</th>
                </tr></thead>
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
                            {/*<div>
                                <p style={{width:'50px', margin:'0 0 2px 0', display:"inline-block"}}>Dia</p>
                                <p style={{width:'40px', margin:'0 0 2px 0', display:"inline-block"}}>Mes</p>
                                <p style={{width:'55px', margin:'0 0 2px 0', display:"inline-block"}}>Año</p>
                            </div>
                            <div>*/}
                                <input style={{width: '50px', marginRight:'2px'}} type="number"/>
                                <input style={{width: '40px', marginRight:'2px'}} type="number" defaultValue={local.mesProgramado} disabled/>
                                <input style={{width: '55px', marginRight:'2px'}} type="number" defaultValue={local.annoProgramado} disabled/>
                            {/*</div>*/}
                        </td>
                        <td style={{verticalAlign: 'middle'}}>
                            {/* Cliente*/}
                            <p><small>{local.cliente? local.cliente.nombre : '...'}</small></p>
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
                            <input style={{width: '30px', margin:'0 0 2px 0'}} type="text" defaultValue="99" disabled/>
                            <input style={{width: '45px', margin:'0 0 2px 0'}} type="number"/>
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
                </tbody>
            </table>
        )
    }
}

TablaLocalesMensual.protoTypes = {
    //localesAgregados: PropTypes.array.required
}
export default TablaLocalesMensual

//separar zona
//region (solo el numero) => agregar el numero en la BD
//ciudad
//
//agregar el boton confirmar