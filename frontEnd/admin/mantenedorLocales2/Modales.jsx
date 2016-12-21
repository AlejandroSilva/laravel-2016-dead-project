// Librerias
import React from 'react'
// Componentes
import Modal from 'react-bootstrap/lib/Modal.js'
// Style
import classNames from 'classnames/bind'
import * as css from './modal.css'
let cx = classNames.bind(css)

// function as a children FTW! :D
export class ModalTrigger extends React.Component{
    constructor(props){
        super(props)
        this.state = { visible: false }
        this.show  = ()=> this.setState({visible: true})
        this.hide  = ()=> this.setState({visible: false})
    }
    render(){
        return this.props.children(this.state.visible, this.show, this.hide)
    }
}

export class ModalAgregarLocal extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            local: {
                idCliente: this.props.idCliente,
                nombre: '',
                numero: '',
                idFormatoLocal: 4,
                idJornadaSugerida: 1,
                horaApertura: '00:00',
                horaCierre: '00:00',
                emailContacto: '',
                telefono1: '',
                telefono2: '',
                stock: 1,
                cutComuna: 7301,
                direccion: ''
            },
            error: {}
        }
        this.onInputChange = (key, evt)=>{
            let actualizado = {}
            actualizado[key] = evt.target.value
            this.setState({ local: Object.assign({}, this.state.local, actualizado)})
        }
        this.handleSubmit = (evt)=>{
            evt.preventDefault()
            evt.stopPropagation()
            this.setState({error: {}}, ()=>{
                this.props.agregarLocal(this.state.local)
                    .then(()=>
                        this.props.hideModal()
                    )
                    .catch(error=>{
                        this.setState({error: error.data})
                    })
            })
        }
    }
    render() {
        return (
            <Modal show={true} onHide={this.props.hideModal}>
                <Modal.Header closeButton>
                    <Modal.Title>Nuevo Local</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <form className="form-horizontal" action="" onSubmit={this.handleSubmit}>
                        {/* Numero */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Numero</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control" placeholder="CECO"
                                       value={this.state.local.numero} onChange={this.onInputChange.bind(this, 'numero')}
                                />
                                {this.state.error.numero && <span className={cx('form__error-message')}>{this.state.error.numero[0]}</span>}
                            </div>
                        </div>
                        {/* Nombre */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Nombre</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control" placeholder="Nombre de local"
                                       value={this.state.local.nombre} onChange={this.onInputChange.bind(this, 'nombre')}
                                />
                                {this.state.error.nombre && <span className={cx('form__error-message')}>{this.state.error.nombre[0]}</span>}
                            </div>
                        </div>
                        {/* Formato */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Formato</label>
                            <div className="col-sm-5">
                                <select className="form-control"
                                        value={this.state.local.idFormatoLocal}
                                        onChange={this.onInputChange.bind(this, 'idFormatoLocal')}
                                >
                                    {this.props.formatoLocales.map(formato=>
                                        <option key={formato.idFormatoLocal} value={formato.idFormatoLocal}>{formato.nombre}</option>
                                    )}
                                </select>
                            </div>
                        </div>
                        {/* Jornada */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Jornada</label>
                            <div className="col-sm-5">
                                <select className="form-control"
                                        value={this.state.local.idJornadaSugerida}
                                        onChange={this.onInputChange.bind(this, 'idJornadaSugerida')}
                                >
                                    {this.props.jornadas.map(jornada=>
                                        <option key={jornada.idJornada} value={jornada.idJornada}>{jornada.nombre}</option>
                                    )}
                                </select>
                            </div>
                        </div>
                        {/* Hora Apertura */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Hora Apertura</label>
                            <div className="col-sm-5">
                                <input type="time" className="form-control"
                                       value={this.state.local.horaApertura} onChange={this.onInputChange.bind(this, 'horaApertura')}
                                />
                                {this.state.error.horaApertura && <span className={cx('form__error-message')}>{this.state.error.horaApertura[0]}</span>}
                            </div>
                        </div>
                        {/* Hora Cierre */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Hora Cierre</label>
                            <div className="col-sm-5">
                                <input type="time" className="form-control"
                                       value={this.state.local.horaCierre} onChange={this.onInputChange.bind(this, 'horaCierre')}
                                />
                                {this.state.error.horaCierre && <span className={cx('form__error-message')}>{this.state.error.horaCierre[0]}</span>}
                            </div>
                        </div>
                        {/* Email */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Email</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control"
                                       value={this.state.local.emailContacto} onChange={this.onInputChange.bind(this, 'emailContacto')}
                                />
                                {this.state.error.emailContacto && <span className={cx('form__error-message')}>{this.state.error.emailContacto[0]}</span>}
                            </div>
                        </div>
                        {/* Telefono */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Telefonos</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control"
                                       value={this.state.local.telefono1} onChange={this.onInputChange.bind(this, 'telefono1')}
                                />
                                {this.state.error.telefono1 && <span className={cx('form__error-message')}>{this.state.error.telefono1[0]}</span>}
                            </div>
                            <div className="col-sm-5">
                                <input type="text" className="form-control"
                                       value={this.state.local.telefono2} onChange={this.onInputChange.bind(this, 'telefono2')}
                                />
                                {this.state.error.telefono2 && <span className={cx('form__error-message')}>{this.state.error.telefono2[0]}</span>}
                            </div>
                        </div>
                        {/* Stock */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Stock</label>
                            <div className="col-sm-5">
                                <input type="number" className="form-control"
                                       value={this.state.local.stock} onChange={this.onInputChange.bind(this, 'stock')}
                                />
                                {this.state.error.stock && <span className={cx('form__error-message')}>{this.state.error.stock[0]}</span>}
                            </div>
                        </div>
                        {/* Comuna */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Comuna</label>
                            <div className="col-sm-5">
                                <select className="form-control"
                                        value={this.state.local.cutComuna}
                                        onChange={this.onInputChange.bind(this, 'cutComuna')}
                                >
                                    {this.props.comunas.map(comuna=>
                                        <option key={comuna.cutComuna} value={comuna.cutComuna}>{comuna.nombre}</option>
                                    )}
                                </select>
                            </div>
                        </div>
                        {/* Dirección */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Dirección</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control"
                                       value={this.state.local.direccion} onChange={this.onInputChange.bind(this, 'direccion')}
                                />
                                {this.state.error.direccion && <span className={cx('form__error-message')}>{this.state.error.direccion[0]}</span>}
                            </div>
                        </div>

                        <div className={cx('form-group', 'form__control-label')}>
                            <a className={cx("btn btn-block btn-default", 'btn-50-perc')} href="#" onClick={this.props.hideModal}>Cancelar</a>
                            <button className={cx("btn btn-block btn-primary", 'btn-50-perc')} type="submit">Actualizar</button>
                        </div>
                    </form>
                </Modal.Body>
            </Modal>
        )
    }
}


export class ModalEditarLocal extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            local: this.props.local,
            error: {}
        }
        this.onInputChange = (key, evt)=>{
            let actualizado = {}
            actualizado[key] = evt.target.value
            this.setState({ local: Object.assign({}, this.state.local, actualizado)})
        }
        this.handleSubmit = (evt)=>{
            evt.preventDefault()
            evt.stopPropagation()
            this.setState({error: {}}, ()=>{
                this.props.actualizarLocal(this.state.local)
                    .then(()=>
                        this.props.hideModal()
                    )
                    .catch(error=>{
                        this.setState({error: error.data})
                    })
            })
        }
    }
    render(){
        return (
            <Modal show={true} onHide={this.props.hideModal}>
                <Modal.Header closeButton>
                    <Modal.Title>Editando local: {this.props.local.cliente.nombreCorto} {this.props.local.numero}</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <form className="form-horizontal" action="" onSubmit={this.handleSubmit}>
                        {/* Nombre */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Nombre</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control" placeholder="Nombre de local"
                                       value={this.state.local.nombre} onChange={this.onInputChange.bind(this, 'nombre')}
                                />
                                {this.state.error.nombre && <span className={cx('form__error-message')}>{this.state.error.nombre[0]}</span>}
                            </div>
                        </div>
                        {/* Formato */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Formato</label>
                            <div className="col-sm-5">
                                <select className="form-control"
                                        value={this.state.local.idFormatoLocal}
                                        onChange={this.onInputChange.bind(this, 'idFormatoLocal')}
                                >
                                    {this.props.formatoLocales.map(formato=>
                                        <option key={formato.idFormatoLocal} value={formato.idFormatoLocal}>{formato.nombre}</option>
                                    )}
                                </select>
                            </div>
                        </div>
                        {/* Jornada */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Jornada</label>
                            <div className="col-sm-5">
                                <select className="form-control"
                                        value={this.state.local.idJornadaSugerida}
                                        onChange={this.onInputChange.bind(this, 'idJornadaSugerida')}
                                >
                                    {this.props.jornadas.map(jornada=>
                                        <option key={jornada.idJornada} value={jornada.idJornada}>{jornada.nombre}</option>
                                    )}
                                </select>
                            </div>
                        </div>
                        {/* Hora Apertura */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Hora Apertura</label>
                            <div className="col-sm-5">
                                <input type="time" className="form-control"
                                       value={this.state.local.horaApertura} onChange={this.onInputChange.bind(this, 'horaApertura')}
                                />
                                {this.state.error.horaApertura && <span className={cx('form__error-message')}>{this.state.error.horaApertura[0]}</span>}
                            </div>
                        </div>
                        {/* Hora Cierre */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Hora Cierre</label>
                            <div className="col-sm-5">
                                <input type="time" className="form-control"
                                       value={this.state.local.horaCierre} onChange={this.onInputChange.bind(this, 'horaCierre')}
                                />
                                {this.state.error.horaCierre && <span className={cx('form__error-message')}>{this.state.error.horaCierre[0]}</span>}
                            </div>
                        </div>
                        {/* Email */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Email</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control"
                                       value={this.state.local.emailContacto} onChange={this.onInputChange.bind(this, 'emailContacto')}
                                />
                                {this.state.error.emailContacto && <span className={cx('form__error-message')}>{this.state.error.emailContacto[0]}</span>}
                            </div>
                        </div>
                        {/* Telefono */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Telefonos</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control"
                                       value={this.state.local.telefono1} onChange={this.onInputChange.bind(this, 'telefono1')}
                                />
                                {this.state.error.telefono1 && <span className={cx('form__error-message')}>{this.state.error.telefono1[0]}</span>}
                            </div>
                            <div className="col-sm-5">
                                <input type="text" className="form-control"
                                       value={this.state.local.telefono2} onChange={this.onInputChange.bind(this, 'telefono2')}
                                />
                                {this.state.error.telefono2 && <span className={cx('form__error-message')}>{this.state.error.telefono2[0]}</span>}
                            </div>
                        </div>
                        {/* Stock */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Stock</label>
                            <div className="col-sm-5">
                                <input type="number" className="form-control"
                                       value={this.state.local.stock} onChange={this.onInputChange.bind(this, 'stock')}
                                />
                                {this.state.error.stock && <span className={cx('form__error-message')}>{this.state.error.stock[0]}</span>}
                            </div>
                        </div>
                        {/* Comuna */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Comuna</label>
                            <div className="col-sm-5">
                                <select className="form-control"
                                        value={this.state.local.cutComuna}
                                        onChange={this.onInputChange.bind(this, 'cutComuna')}
                                >
                                    {this.props.comunas.map(comuna=>
                                        <option key={comuna.cutComuna} value={comuna.cutComuna}>{comuna.nombre}</option>
                                    )}
                                </select>
                            </div>
                        </div>
                        {/* Dirección */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Dirección</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control"
                                       value={this.state.local.direccion} onChange={this.onInputChange.bind(this, 'direccion')}
                                />
                                {this.state.error.direccion && <span className={cx('form__error-message')}>{this.state.error.direccion[0]}</span>}
                            </div>
                        </div>

                        <div className={cx('form-group', 'form__control-label')}>
                            <a className={cx("btn btn-block btn-default", 'btn-50-perc')} href="#" onClick={this.props.hideModal}>Cancelar</a>
                            <button className={cx("btn btn-block btn-primary", 'btn-50-perc')} type="submit">Actualizar</button>
                        </div>
                    </form>
                </Modal.Body>
            </Modal>
        )
    }
}