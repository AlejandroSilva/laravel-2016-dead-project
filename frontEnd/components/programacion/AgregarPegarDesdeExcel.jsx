//// Libs
//import React from 'react'
//let PropTypes = React.PropTypes
//
//
//class AgregarPegarDesdeExcel extends React.Component {
//    testOnPaste(event){
//        event.preventDefault()
//        // cuando se pegue un elemento, separar separar las filas por el "line feed" '\n'
//        console.log(event.clipboardData.items[0].type)
//        event.clipboardData.items[0].getAsString(texto=>{
//            // separar cada una de las filas '\n'
//            let rows = texto.trim().split('\n')
//            // quitar las filas vacias, y separar sus valores por el caracter tabulador
//            rows = rows.filter(row=>row!=='')
//            let celdas = rows.map(row=>row.trim().split('\t'))
//            console.log(celdas)
//
//            // Agregar los locales
//            celdas.forEach(row=>{
//
//                // buscar el cliente por nombre o nombreCorto, en la primera y la segunda celda
//                let cliente = this.props.clientes.find(cliente=>{
//                    return cliente.nombre===row[0]
//                        || cliente.nombreCorto===row[0]
//                        || cliente.nombre===row[1]
//                        || cliente.nombreCorto===row[1]
//                })
//
//                if(cliente){
//                    // buscar el local
//                    let local = cliente.locales.find(local=> (local.numero==row[0] || local.numero==row[1]) )
//                    if(local){
//                        // TODO: FIX MES AÑO
//                        //let localCreado = this.tablaLocalesMensual.agregarLocal(local, this.state.inputMesAnno)
//                        let localCreado = this.props.onFormSubmit(local, '03-2016')
//                        //console.log(local)
//                    }else{
//                        console.log(`El cliente ${cliente.nombreCorto} no tiene el local ${row[0]}||${row[1]}`)
//                    }
//                }else{
//                    console.log(`cliente ${row[0]} no encontrado`)
//                }
//            })
//        })
//    }
//    render(){
//        return (
//            <div>
//                <div className="row">
//                    <h4 className="page-header" style={{marginTop: '1em'}}>Agregar locales a la programación:</h4>
//                </div>
//
//
//                <div className="row">
//                    <div className="col-sm-offset-1 col-sm-3">
//                        <p>Abra Excel, seleccione los datos de la columna <b>Cliente</b> y <b>CECO</b>, <b>COPIE</b> el contendo y finalmente <b>PEGUELO</b> en esta tabla:</p>
//                    </div>
//                    <div className="col-sm-offset-0 col-sm-6">
//
//                        <div className="form-horizontal">
//                            {/* Instrucciones */}
//                            <div className="form-group">
//                                <label className="col-sm-2 control-label" htmlFor="fechaProgramada">Mes:</label>
//                                <div className="col-sm-10">
//                                    <select className="form-control" name="fechaProgramada" ref={ref=>this.inputMesAnno=ref}>
//                                        {this.props.meses.map((mes,i)=>{
//                                            return <option key={i} value={mes.valor}>{mes.texto}</option>
//                                        })}
//                                    </select>
//                                </div>
//                            </div>
//
//                            <div className="form-group">
//                                <label className="col-sm-2 control-label" htmlFor="fechaProgramada">Pegar datos:</label>
//                                <div className="col-sm-10">
//                                    <table className="table table-bordered table-condensed">
//                                        <thead>
//                                            <tr><th>Cliente</th><th>CECO</th></tr>
//                                        </thead>
//                                        <tbody>
//                                        <tr>
//                                            <td style={style.tableCell}><input type="text" style={style.tableCellInput} onPaste={this.testOnPaste.bind(this)}/></td>
//                                            <td style={style.tableCell}><input type="text" style={style.tableCellInput} onPaste={this.testOnPaste.bind(this)}/></td>
//                                        </tr>
//                                        <tr>
//                                            <td style={style.tableCell}><input type="text" style={style.tableCellInput} onPaste={this.testOnPaste.bind(this)}/></td>
//                                            <td style={style.tableCell}><input type="text" style={style.tableCellInput} onPaste={this.testOnPaste.bind(this)}/></td>
//                                        </tr>
//                                        </tbody>
//                                    </table>
//                                </div>
//                            </div>
//
//                        </div>
//                    </div>
//                </div>
//
//            </div>
//        )
//    }
//}
//
//export default AgregarPegarDesdeExcel