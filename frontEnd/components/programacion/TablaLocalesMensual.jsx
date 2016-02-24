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
    alertar(obj){
        console.log(obj)
    }
    componentWillReceiveProps(nextProps){
        // cuando se recibe una nueva propiedad, se verifica si los locales agregados
        // han cambiando, esto ocurre cuando se hace click en "agregar locales"
        let hanCambiado = this.props.localesAgregados!==nextProps.localesAgregados
        if(hanCambiado){
            this.actualizarLocales(nextProps.localesAgregados)
        }
    }
    actualizarLocales(localesAgregados){
        // de la lista de locales, obtener solo los nuevos (que no estan en state.locales)
        let localesNuevos = localesAgregados.filter(localAgregado=>{
            // si no esta en la lista, entrega undefined
            let esNuevo = this.state.locales.find(local=>local.idLocal===localAgregado.idLocal)===undefined
            return esNuevo
        })
        if(localesNuevos.length===0) return

        // por cada nuevo local, se debe pedir la informacion completa y ser agregado en la lista de localesPorInventariar
        let locales = this.state.locales
        localesNuevos.forEach(nuevoLocal=>{
            //ToDO: pedir todos los datos por json
            this.obtenerDatosLocal(nuevoLocal.idLocal)
            locales.push(nuevoLocal)
        })

        //console.log("locales nuevos: ", localesNuevos)
        this.setState({
            locales
        })
    }
    obtenerDatosLocal(idLocal){
        api.locales.getVerbose(idLocal)
            .then(informacionLocal=>{
                console.log(informacionLocal)
                this.setState({
                    // actualizar los datos de la lista con la informacion obtenida por el api
                    locales: this.state.locales.map(local=>{
                        if(local.idLocal===informacionLocal.idLocal)
                            return informacionLocal
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
                    <th>Local</th>
                    {/*<th>zona</th>
                    <th>region</th>*/}
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
                        <td style={{verticalAlign: 'middle'}}>
                            {/* Fecha */}
                            <div>
                                <p style={{width:'50px', margin:'0 0 2px 0', display:"inline-block"}}>Dia</p>
                                <p style={{width:'50px', margin:'0 0 2px 0', display:"inline-block"}}>Mes</p>
                                <p style={{width:'70px', margin:'0 0 2px 0', display:"inline-block"}}>Año</p>
                            </div>
                            <div>
                                <input style={{width: '50px', marginRight:'2px'}} type="number"/>
                                <input style={{width: '50px', marginRight:'2px'}} type="number" defaultValue="2" disabled/>
                                <input style={{width: '70px', marginRight:'2px'}} type="number" defaultValue="2016" disabled/>
                            </div>
                        </td>
                        <td style={{verticalAlign: 'middle'}}>
                            {/* Cliente*/}
                            <p>{local.cliente? local.cliente.nombre : '...'}</p>
                        </td>
                        <td style={{verticalAlign: 'middle'}}>
                            {/* Local */}
                            <p><b>{local.nombre? local.nombre : '...'}</b></p>
                        </td>

                        <td style={{verticalAlign: 'middle'}}>
                            {/* Zona / Region / Comuna */}
                            <p style={{margin:0}}><small>{zona.nombre? zona.nombre : '...'}</small></p>
                            <p style={{margin:0}}><small>{region.nombre? region.nombre : '...'}</small></p>
                            <p style={{margin:0}}><b>{comuna.nombre? comuna.nombre : '...'}</b></p>
                        </td>
                        <td style={{verticalAlign: 'middle'}}>
                            {/* Stock */}
                            {local.stock? local.stock : '...'}
                        </td>
                        <td style={{verticalAlign: 'middle'}}>
                            {/* Dotación */}
                            <input style={{width: '50px', margin:'0 0 2px 0'}} type="number" defaultValue="99" disabled/>
                            <input style={{width: '50px', margin:'0 0 2px 0'}} type="number"/>
                        </td>
                        <td style={{verticalAlign: 'middle'}}>
                            {/* Jornada */}
                            {local.jornada? local.jornada.nombre : '(...jornada)'}
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
    localesAgregados: PropTypes.array.required
}
export default TablaLocalesMensual