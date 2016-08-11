// Librerias
import React from 'react'
import ReactDOM from 'react-dom'
import api from '../../apiClient/v1.js'
let PropTypes = React.PropTypes
// Componentes
import ReactNotify from 'react-notify'
import * as ReactNotifyCSS from '../../shared/ReactNotify/ReactNotify.css'

export class MantenedorStock extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            error: '',
            resultado: [],
            idCliente: 0,
            modoIngreso: 'pegar', // 'archivo'/'pegar',
            realizandoPeticion: false
        }
    }

    onSelectCliente(evt){
        let idCliente = evt.target.value
        this.setState({idCliente})
    }
    setModoIngreso(modo){
        if(modo===this.state.modoIngreso) return

        this.setState({
            modoIngreso: modo,
            resultado: [],
            error: ''
        })
    }

    onFormSubmit(evt){
        evt.preventDefault();
        // limpiar la tabla y los errores
        this.setState({
            realizandoPeticion: true,
            resultado: [],
            error: ''
        })

        // hacer una llamada con los elementos que se han adjuntado
        let archivoExcel = ReactDOM.findDOMNode(this.refInputArchivo).files[0]
        api.local.enviarArchivoStock(this.state.idCliente, archivoExcel)
            .then(datos=>{
                // construir la tabla con el resultado
                this.setState({
                    realizandoPeticion: false,
                    resultado: datos
                })
            })
            .catch(err=>{
                let data = err.data
                let mensajeError = data.error? data.error : "Ocurrio un error al cargar el archivo"

                this.setState({
                    realizandoPeticion: false,
                    error: mensajeError
                })
                this.refs.notificator.error("Error", mensajeError, 4*1000);
                console.log(mensajeError)
            })
    }

    onPaste(evt){
        // limpiar el cuadro de texto y bloquearlo mientras se procesan los campos
        this.refInputPaste.text = ''
        this.setState({
            resultado: [],
            realizandoPeticion: true
        })

        evt.preventDefault()
        evt.clipboardData.items[0].getAsString(texto=>{
            // separar cada una de las filas '\n'
            let rows_texto = texto.trim().split('\n')
            // quitar las filas vacias, y separar sus valores por el caracter tabulador
            rows_texto = rows_texto.filter(row=>row!=='')

            let rows_array = rows_texto.map(row=>{
                // convertir el texto, en array, separandolo por el caracter TAB
                let _row = row.trim().split('\t')

                // verificar que tenga 2 columnas
                if(_row.length<2) return null

                // puede que las celdas tengan un numero con separador de miles (Ej: 10.500), se debe quitar los '.' de los strings
                return {
                    'numero': _row[0],
                    'stock': _row[1].replace('.', '')
                }
            }).filter(row=>row!=null)

            console.log('ROWS', rows_array)

            api.local.enviarPegarStock({
                idCliente: this.state.idCliente,
                datos: rows_array
            })
                .then(datos=>{
                    // construir la tabla con el resultado
                    this.setState({
                        realizandoPeticion: false,
                        resultado: datos,
                        error: ''
                    })
                })
                .catch(err=>{
                    let data = err.data
                    let mensajeError = data.error? data.error : "Ocurrio un error al cargar el archivo"

                    this.setState({
                        realizandoPeticion: false,
                        error: mensajeError
                    })
                    this.refs.notificator.error("Error", mensajeError, 4*1000);
                    console.log(mensajeError)
                })
        })
    }

    render(){
        return (
            <div className="container">
                <ReactNotify ref='notificator' className={ReactNotifyCSS}/>

                <h2>Actualizar Stock de Locales</h2>
                <p>Al recibir el documento, se actualizara el stock de todos los locales, junto al stock de los inventarios pendientes, y a las nominas asociadas a estos.</p>

                {/*  Layout: [espacio col-3] [cliente col-3] [formulario col-3] [espacio col-3]  */}

                {/* Menu superior para seleccionar el modo de ingreso de los datos*/}
                <div className="row" style={{marginBottom: '1em'}}>
                    <div className='col-sm-offset-2 col-sm-4'>
                        <button type="button" className={'btn btn-sm btn-default '+(this.state.modoIngreso==='pegar'? 'active':'')}
                                onClick={this.setModoIngreso.bind(this, 'pegar')}>
                            Pegar desde Excel
                        </button>
                        <button type="button" className={'btn btn-sm btn-default '+(this.state.modoIngreso==='archivo'? 'active':'')}
                                onClick={this.setModoIngreso.bind(this, 'archivo')}>
                            Archivo
                        </button>
                    </div>
                </div>

                {/* Formulario de envio de archivo */}
                <div className="row" style={{display: this.state.modoIngreso==='archivo'? '':'none'}}>
                    <form className="form-horizontal" onSubmit={this.onFormSubmit.bind(this)}>
                        {/* Selector de cliente */}
                        <div className="form-group">
                            <label className="control-label col-md-2">
                                Cliente
                            </label>
                            <div className="col-md-4">
                                <select className="form-control"
                                        value={this.state.idCliente}
                                        onChange={this.onSelectCliente.bind(this)}
                                        disabled={this.state.realizandoPeticion}
                                >
                                    <option key="0" value="0" disabled={true}>Seleccione</option>
                                    {this.props.clientes.map(cliente=>
                                        <option key={cliente.idCliente} value={cliente.idCliente}>{cliente.nombreCorto}</option>
                                    )}
                                </select>
                            </div>
                        </div>
                        {/* input para seleccionar archivo */}
                        <div className="form-group">
                            <label className="control-label col-md-2">
                                Documento Excel
                            </label>
                            <div className="col-md-4">
                                <p>Descargue la plantilla, complete los datos (CECO y Stock) en las columnas correspondientes, y suba el archivo.</p>
                            </div>
                        </div>
                        <div className="form-group">
                            <div className="col-md-offset-2 col-md-4">
                                <input type="file" className="form-control"
                                       ref={ref=>this.refInputArchivo=ref}
                                       disabled={this.state.realizandoPeticion}
                                />
                            </div>
                            <div className="col-md-1">
                                <a className="btn btn-default btn-sm" href='/actualizarStock/plantilla-stock.xlsx'>
                                    Descargar plantilla
                                </a>
                            </div>
                        </div>
                        {/* Boton para enviar el archivo */}
                        <div className="form-group">
                            <div className="col-md-offset-2 col-md-4">
                                <button className="btn btn-primary btn-block"
                                        disabled={this.state.realizandoPeticion}
                                >
                                    Actualizar stock y Dotación
                                </button>
                                {/* Mostrar mensaje de error si corresponde */}
                                {this.state.error?
                                    <div className="alert alert-danger"><b>Error</b> {this.state.error}</div> : null
                                }
                            </div>
                        </div>
                    </form>
                </div>

                {/* Formulario para pegar datos*/}
                <div className="row" style={{display: this.state.modoIngreso==='pegar'? '':'none'}}>
                    <form className="form-horizontal" onSubmit={this.onPaste.bind(this)}>
                        {/* Selector de cliente */}
                        <div className="form-group">
                            <label className="control-label col-md-2">
                                Cliente
                            </label>
                            <div className="col-md-4">
                                <select className="form-control"
                                        value={this.state.idCliente}
                                        onChange={this.onSelectCliente.bind(this)}
                                        disabled={this.state.realizandoPeticion}
                                >
                                    <option key="0" value="0" disabled={true}>Seleccione</option>
                                    {this.props.clientes.map(cliente=>
                                        <option key={cliente.idCliente} value={cliente.idCliente}>{cliente.nombreCorto}</option>
                                    )}
                                </select>
                            </div>
                        </div>

                        {/* Input para pegar los datos */}
                        <div className="form-group">
                            <label className="control-label col-md-2">
                                Pegar datos
                            </label>
                            <div className="col-md-4">
                                <p>Abra Excel, <b>seleccione dos columnas</b>: CECO y STOCK (en ese orden), <b>COPIE</b> el contendo y <b>PEGUELO</b> en el cuadro de texto.</p>

                                <input type="text" className="form-control"
                                       placeholder="Pegar aca los datos de Excel "
                                       ref={ref=>this.refInputPaste=ref}
                                       onPaste={this.onPaste.bind(this)}
                                       onKeyDown={(evt)=>{
                                            // solo permitir que se escriba CTRL+V (pegar texto)
                                            if( !(evt.ctrlKey && evt.keyCode==86) ) evt.preventDefault()
                                       }}
                                       disabled={this.state.realizandoPeticion}
                                />
                                {/* Mostrar mensaje de error si corresponde */}
                                {this.state.error?
                                    <div className="alert alert-danger"><b>Error</b> {this.state.error}</div> : null
                                }
                            </div>
                        </div>
                    </form>
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