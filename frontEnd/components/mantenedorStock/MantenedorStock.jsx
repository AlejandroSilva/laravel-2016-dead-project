// Librerias
import React from 'react'
import ReactDOM from 'react-dom'
import api from '../../apiClient/v1.js'
let PropTypes = React.PropTypes
// Componentes
import ReactNotify from 'react-notify'
import * as ReactNotifyCSS from '../shared/ReactNotify.css'
//import * as css from './MantenedorStock.css'

export class MantenedorStock extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            error: '',
            resultado: []
        }
    }

    onFormSubmit(evt){
        evt.preventDefault();
        // limpiar la tabla y los errores
        this.setState({error:'', resultado: []})

        // hacer una llamada con los elementos que se han adjuntado
        let idCliente = this.refSelectCliente.value
        let archivoExcel = ReactDOM.findDOMNode(this.refInputArchivo).files[0]
        api.local.enviarArchivoStock(idCliente, archivoExcel)
            .then(datos=>{
                // construir la tabla con el resultado
                this.setState({resultado: datos})
            })
            .catch(err=>{
                let data = err.data
                if(data.error){
                    // mostrar el error
                    this.setState({error: data.error})
                    this.refs.notificator.error("Error", data.error, 4*1000);
                }else{
                    // un error no controlado, seguramente un 500, mostrar un error generico
                    this.refs.notificator.error("Error", "Ocurrio un error al cargar el archivo", 4*1000);
                    console.error(err)
                }
            })
    }

    render(){
        return (
            <div className="container">
                <ReactNotify ref='notificator' className={ReactNotifyCSS}/>

                <h2>Actualizar Stock de Locales</h2>
                <p>Al recibir el documento, se actualizara el stock de todos los locales, junto al stock de los inventarios pendientes, y a las nominas asociadas a estos.</p>

                <div className="row">
                    <div className="col-md-8 col-md-offset-1">
                        <form className="form" onSubmit={this.onFormSubmit.bind(this)}>
                            <div className="form-group">
                                <label>Cliente</label>
                                <select className="form-control"
                                        defaultValue="0"
                                        ref={ref=>this.refSelectCliente=ref}
                                >
                                    <option key="0" value="0" disabled={true}>Seleccione</option>
                                    {this.props.clientes.map(cliente=>
                                        <option key={cliente.idCliente} value={cliente.idCliente}>{cliente.nombreCorto}</option>
                                    )}
                                </select>
                            </div>
                            <div className="form-group">
                                <label>Documento</label>
                                <input type="file" className="form-control"
                                       ref={ref=>this.refInputArchivo=ref}
                                />
                            </div>
                            <div className="form-group">
                                <button className="btn btn-primary btn-block">Actualizar stock y Dotación</button>
                                {/* Mostrar mensaje de error si corresponde */}
                                {this.state.error?
                                    <div className="alert alert-danger"><b>Error</b> {this.state.error}</div> : null
                                }
                            </div>
                        </form>
                    </div>

                    <div className="col-md-2 col-md-offset-1">
                        <a className="btn btn-primary btn-sm" href='/actualizarStock/plantilla-stock.xlsx'>
                            Descargar plantilla
                        </a>
                    </div>
                </div>

                {/* Tabla con el resultado de actualizar el archivo */}
                {this.state.resultado.length>0?
                    <table className="table table-striped table-bordered table-hover table-condensed">
                        <thead>
                            <tr>
                                <th>Local</th>
                                <th>Inventario</th>
                                <th>Resultado</th>
                            </tr>
                        </thead>
                        <tbody>
                            {this.state.resultado.map((local, index)=>{
                                // construir el row con el resultado del local
                                let localTR = <tr key={index} className={local.error? 'danger' : ''}>
                                    <td>{local.cliente} - {local.local}</td>
                                    <td></td>
                                    <td>{local.error? local.error : local.estado}</td>
                                </tr>
                                if(local.inventarios.length==0)
                                    return localTR

                                // construir el row con el resultado de los inventarios asociados al local
                                let inventariosTR = local.inventarios.map((inv, index2)=>
                                    <tr key={index+'--'+index2} className={inv.error? 'success' : ''}>
                                        <td></td>
                                        <td>{inv.fechaProgramada}</td>
                                        <td>{inv.error? inv.error : inv.estado}</td>
                                    </tr>
                                )
                                return [localTR, ...inventariosTR]
                            })}
                        </tbody>
                    </table>
                    :
                       null
                }
            </div>
        )
    }
}

MantenedorStock.propTypes = {
    clientes: PropTypes.arrayOf(PropTypes.object).isRequired
}