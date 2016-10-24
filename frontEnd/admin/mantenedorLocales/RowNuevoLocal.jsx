// Librerias
import React from 'react'
import api from '../../apiClient/v1'
// Componentes
import Tooltip from 'rc-tooltip'
// Estilos
import classNames from 'classnames/bind'
import * as cssTabla from './TablaLocales.css'
import * as cssRow from './RowNuevoLocal.css'
let cx = classNames.bind(cssTabla)

// Componentes
export class RowNuevoLocal extends React.Component{
    constructor(props){
        super(props)
        this.datosDefault = {
            idCliente: 1,
            cutComuna: "1101",    // Iquique
            direccion: '',
            fechaStock: '',
            horaApertura: '',
            horaCierre: '',
            emailContacto: '',
            telefono1: '',
            telefono2: '',
            idFormatoLocal: "1",
            idJornadaSugerida: "3",
            nombre: '',
            numero: '',
            stock: ''
        }
        this.state = {
            datos: Object.assign({}, this.datosDefault),
            errores: {
                // cutComuna: [],
                // direccion: [],
                // fechaStock: [],
                // horaApertura: [],
                // horaCierre: [],
                // idCliente: [],
                // idFormatoLocal: [],
                // idJornadaSugerida: [],
                // nombre: [],
                // numero: [],
                // stock: []
            }
        }
    }
    onCrear(){
        api.local.nuevo(this.state.datos)
            .then(nuevoLocal=>{
                // informamos que agregamos un nuevo local
                this.props.agregarLocal(nuevoLocal)
                // y se deja el formulario limpio
                this.setState({
                    datos: Object.assign({}, this.datosDefault),
                    errores: {}
                })
            })
            .catch(err=>{
                // todo, que pasa con un erro 500, que no tenga err.data?
                let errores = err.data
                // console.log("err", errores)
                this.setState({errores})
            })
    }

    onCancelar(){
        // no se crea el formulario, se vuelve a dejar en limpio todo
        this.setState({ datos: Object.assign({}, this.datosDefault)})
    }

    render(){
        return <tr>
            <td className={cx('id')}></td>

            {/* Cliente */}
            <td className={cx('cliente-td')}>
                <Tooltip placement="bottom" trigger={[]} destroyTooltipOnHide={true}
                         visible={this.state.errores.idCliente!=null}
                         overlay={this.state.errores.idCliente?
                            <span>{this.state.errores.idCliente.join('. ')}</span> : <span/>
                        }>
                    <select
                        value={this.state.datos.idCliente}
                        onChange={(evt)=>{
                            this.setState({ datos: Object.assign(this.state.datos, {idCliente: evt.target.value}) })
                        }}>
                            {this.props.clientes.map((cliente, index)=>
                                <option key={index} value={cliente.idCliente}>{cliente.nombreCorto}</option>
                            )}
                    </select>
                </Tooltip>
            </td>

            {/* Numero de Local */}
            <td className={cx('numero-td')}>
                <Tooltip placement="bottom" trigger={[]} destroyTooltipOnHide={true}
                         visible={this.state.errores.numero!=null}
                         overlay={this.state.errores.numero?
                            <span>{this.state.errores.numero.join('. ')}</span> : <span/>
                        }>
                    <input type="text"
                           className={this.state.errores.numero? cssRow.inputInvalid : ''}
                           value={this.state.datos.numero}
                           onChange={(evt)=>{
                                this.setState({ datos: Object.assign(this.state.datos, {numero: evt.target.value}) })
                           }}
                    />
                </Tooltip>
            </td>

            {/* Nombre de Local */}
            <td className={cx('nombre-td')}>
                <Tooltip placement="bottom" trigger={[]} destroyTooltipOnHide={true}
                         visible={this.state.errores.nombre!=null}
                         overlay={this.state.errores.nombre?
                            <span>{this.state.errores.nombre.join('. ')}</span> : <span/>
                        }>
                    <input type="text"
                           className={cssTabla.nombre_input +' '+ (this.state.errores.nombre?cssRow.inputInvalid:'')}
                           value={this.state.datos.nombre}
                           onChange={(evt)=>{
                                this.setState({ datos: Object.assign(this.state.datos, {nombre: evt.target.value})})
                           }}
                    />
                </Tooltip>
            </td>

            {/* Formato de Local */}
            <td className={cx('formatoLocal-td')}>
                <Tooltip placement="bottom" trigger={[]} destroyTooltipOnHide={true}
                         visible={this.state.errores.idFormatoLocal!=null}
                         overlay={this.state.errores.idFormatoLocal?
                            <span>{this.state.errores.idFormatoLocal.join('. ')}</span> : <span/>
                        }>
                    <select
                        className={cssTabla.formatoLocal_select}
                        value={this.state.datos.idFormatoLocal}
                        onChange={(evt)=>{
                            this.setState({ datos: Object.assign(this.state.datos, {idFormatoLocal: evt.target.value}) })
                        }}>
                        {this.props.formatoLocales.map((formato, index)=>
                            <option key={index} value={formato.idFormatoLocal}>{formato.nombre}</option>
                        )}
                    </select>
                </Tooltip>
            </td>

            {/* Jornada */}
            <td className={cx('jornada-td')}>
                <Tooltip placement="bottom" trigger={[]} destroyTooltipOnHide={true}
                         visible={this.state.errores.idJornadaSugerida!=null}
                         overlay={this.state.errores.idJornadaSugerida?
                            <span>{this.state.errores.idJornadaSugerida.join('. ')}</span> : <span/>
                        }>
                    <select
                        value={this.state.datos.idJornadaSugerida}
                        className={cssTabla.jornada_select}
                        onChange={(evt)=>{
                            this.setState({datos: Object.assign(this.state.datos, {idJornadaSugerida: evt.target.value})})
                        }}>
                        {this.props.jornadas.map((jornada, index)=>
                            <option key={index} value={jornada.idJornada}>{jornada.nombre}</option>
                        )}
                    </select>
                </Tooltip>
            </td>

            {/* Hora de Apertura */}
            <td className={cx('horaApertura-td')}>
                <Tooltip placement="bottom" trigger={[]} destroyTooltipOnHide={true}
                         visible={this.state.errores.horaApertura!=null}
                         overlay={this.state.errores.horaApertura?
                            <span>{this.state.errores.horaApertura.join('. ')}</span> : <span/>
                        }>
                    <input type="time"
                           className={this.state.errores.horaApertura? cssRow.inputInvalid : ''}
                           value={this.state.datos.horaApertura}
                           onChange={(evt)=>{
                                this.setState({ datos: Object.assign(this.state.datos, {horaApertura: evt.target.value}) })
                           }}
                    />
                </Tooltip>
            </td>

            {/* Hora de Cierre */}
            <td className={cx('horaCierre-td')}>
                <Tooltip placement="bottom" trigger={[]} destroyTooltipOnHide={true}
                         visible={this.state.errores.horaCierre!=null}
                         overlay={this.state.errores.horaCierre?
                            <span>{this.state.errores.horaCierre.join('. ')}</span> : <span/>
                        }>
                    <input type="time"
                           className={this.state.errores.horaCierre? cssRow.inputInvalid : ''}
                           value={this.state.datos.horaCierre}
                           onChange={(evt)=>{
                                this.setState({ datos: Object.assign(this.state.datos, {horaCierre: evt.target.value}) })
                           }}
                    />
                </Tooltip>
            </td>

            {/* Email Contacto */}
            <td className={cx('emailContacto-td')}>
                <input type="email"
                       className={cssTabla.emailContacto_input}
                       value={this.state.datos.emailContacto}
                       onChange={(evt)=>{
                            this.setState({ datos: Object.assign(this.state.datos, {emailContacto: evt.target.value}) })
                       }}
                />
            </td>

            {/* Telefono1 */}
            <td className={cx('telefono1-td')}>
                <Tooltip placement="bottom" trigger={[]} destroyTooltipOnHide={true}
                         visible={this.state.errores.telefono1!=null}
                         overlay={this.state.errores.telefono1?
                            <span>{this.state.errores.telefono1.join('. ')}</span> : <span/>
                        }>
                    <input type="text"
                           className={cssTabla.telefono1_input}
                           value={this.state.datos.telefono1}
                           onChange={(evt)=>{
                                this.setState({ datos: Object.assign(this.state.datos, {telefono1: evt.target.value}) })
                           }}
                    />
                </Tooltip>
            </td>

            {/* Telefono2 */}
            <td className={cx('telefono2-td')}>
                <Tooltip placement="bottom" trigger={[]} destroyTooltipOnHide={true}
                         visible={this.state.errores.telefono2!=null}
                         overlay={this.state.errores.telefono2?
                            <span>{this.state.errores.telefono2.join('. ')}</span> : <span/>
                        }>
                    <input type="text"
                           className={cssTabla.telefono2}
                           value={this.state.datos.telefono2}
                           onChange={(evt)=>{
                                this.setState({ datos: Object.assign(this.state.datos, {telefono2: evt.target.value}) })
                           }}
                    />
                </Tooltip>
            </td>

            {/* Stock */}
            <td className={cx('stock-td')}>
                <Tooltip placement="bottom" trigger={[]} destroyTooltipOnHide={true}
                         visible={this.state.errores.stock!=null}
                         overlay={this.state.errores.stock?
                            <span>{this.state.errores.stock.join('. ')}</span> : <span/>
                        }>
                    <input type="text"
                           className={this.state.errores.stock? cssRow.inputInvalid : ''}
                           value={this.state.datos.stock}
                           onChange={(evt)=>{
                                let datos = Object.assign(this.state.datos, {stock: evt.target.value} )
                                this.setState({datos})
                           }}
                    />
                </Tooltip>
            </td>

            {/* Fecha Stock */}
            <td className={cx('fechaStock-td')}>
                <Tooltip placement="bottom" trigger={[]} destroyTooltipOnHide={true}
                         visible={this.state.errores.fechaStock!=null}
                         overlay={this.state.errores.fechaStock?
                            <span>{this.state.errores.fechaStock.join('. ')}</span> : <span/>
                        }>
                    <input type="date"
                           className={this.state.errores.fechaStock? cssRow.inputInvalid : ''}
                           value={this.state.datos.fechaStock}
                           onChange={(evt)=>{
                                let datos = Object.assign(this.state.datos, {fechaStock: evt.target.value} )
                                this.setState({datos})
                           }}
                    />
                </Tooltip>
            </td>

            {/* Comuna */}
            <td className={cx('comuna-td')}>
                <Tooltip placement="bottom" trigger={[]} destroyTooltipOnHide={true}
                         visible={this.state.errores.cutComuna!=null}
                         overlay={this.state.errores.cutComuna?
                            <span>{this.state.errores.cutComuna.join('. ')}</span> : <span/>
                        }>
                    <select
                        value={this.state.datos.cutComuna}
                        className={cssTabla.comuna_select}
                        onChange={(evt)=>{
                            this.setState({ datos: Object.assign(this.state.datos, {cutComuna: evt.target.value}) })
                        }}>
                            {this.props.comunas.map((comuna, index)=>
                                <option key={index} value={comuna.cutComuna}>{comuna.nombre}</option>
                            )}
                    </select>
                </Tooltip>
            </td>

            {/* Dirección */}
            <td className={cx('direccion-td')}>
                <Tooltip placement="bottom" trigger={[]} destroyTooltipOnHide={true}
                         visible={this.state.errores.direccion!=null}
                         overlay={this.state.errores.direccion?
                            <span>{this.state.errores.direccion.join('. ')}</span> : <span/>
                        }>
                    <input type="text"
                           className={cssTabla.direccion_input +' '+ (this.state.errores.direccion?cssRow.inputInvalid:'')}
                           value={this.state.datos.direccion}
                           onChange={(evt)=>{
                                this.setState({ datos: Object.assign(this.state.datos, {direccion: evt.target.value}) })
                           }}
                    />
                </Tooltip>
            </td>

            {/* Opciones */}
            <td className={cx('opciones-td')}>
                <button className="btn btn-xs btn-primary"
                        onClick={this.onCrear.bind(this)}
                >Agregar</button>
                <button className="btn btn-xs btn-danger"
                        onClick={this.onCancelar.bind(this)}
                        disabled={true}
                >X</button>
            </td>
        </tr>
    }
}

RowNuevoLocal.propTypes = {
    // Objetos
    clientes: React.PropTypes.array.isRequired,
    jornadas: React.PropTypes.array.isRequired,
    formatoLocales: React.PropTypes.array.isRequired,
    comunas: React.PropTypes.array.isRequired,
    // Metodos
    agregarLocal: React.PropTypes.func.isRequired,
    mostrarError: React.PropTypes.func.isRequired
}