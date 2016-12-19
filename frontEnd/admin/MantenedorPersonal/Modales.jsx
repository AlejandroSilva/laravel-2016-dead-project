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
        this.state = {visible: false}
        let self = this
        this.show = ()=>
            self.setState({visible: true})
        this.hide = ()=>{
            self.setState({visible: false})
        }
    }
    render(){
        return this.props.children(this.state.visible, this.show, this.hide)
    }
}

export class ModalAgregarUsuario extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            usuario: {
                usuarioRUN: '',
                usuarioDV: '',
                nombre1: '',
                nombre2: '',
                apellidoPaterno: '',
                apellidoMaterno: '',
                email: '',
                emailPersonal: '',
                fechaNacimiento: '0000-00-00',
                cutComuna: 7301
            },
            error: {}
        }
        this.onRunChange = (evt)=>{
            this.setState({ usuario: Object.assign({}, this.state.usuario, {usuarioRUN: evt.target.value}) })
        }
        this.onDvChange = (evt)=>{
            this.setState({ usuario: Object.assign({}, this.state.usuario, {usuarioDV: evt.target.value}) })
        }
        this.onNombre1Change = (evt)=>{
            this.setState({ usuario: Object.assign({}, this.state.usuario, {nombre1: evt.target.value}) })
        }
        this.onNombre2Change = (evt)=>{
            this.setState({ usuario: Object.assign({}, this.state.usuario, {nombre2: evt.target.value}) })
        }
        this.onApellidoPaternoChange = (evt)=>{
            this.setState({ usuario: Object.assign({}, this.state.usuario, {apellidoPaterno: evt.target.value}) })
        }
        this.onApellidoMaternoChange = (evt)=>{
            this.setState({ usuario: Object.assign({}, this.state.usuario, {apellidoMaterno: evt.target.value}) })
        }
        this.onEmailChange = (evt)=>{
            this.setState({ usuario: Object.assign({}, this.state.usuario, {email: evt.target.value}) })
        }
        this.onEmailPersonalChange = (evt)=>{
            this.setState({ usuario: Object.assign({}, this.state.usuario, {emailPersonal: evt.target.value}) })
        }
        this.handleSubmit = (evt)=>{
            evt.preventDefault()
            evt.stopPropagation()
            this.props.nuevoUsuario(this.state.usuario)
                .then(()=>{
                    this.props.hideModal()
                })
                .catch(err=>{
                    console.error(err.data)
                    this.setState({
                        error: err.data
                    })
                })

        }
    }
    render(){
        return (
            <Modal show={true} onHide={this.props.hideModal}>
                <Modal.Header closeButton>
                    <Modal.Title>Nuevo usuario</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <form className="form-horizontal" action="" onSubmit={this.handleSubmit}>
                        <h4>Datos personales</h4>
                        {/* RUN */}
                        <div className={cx("form-group", 'form__control-label')}>
                            <label className="col-sm-2 control-label">RUN (*)</label>
                            <div className="col-sm-3">
                                <input type="text" className="form-control" placeholder="RUN"
                                       value={this.state.usuario.usuarioRUN} onChange={this.onRunChange}
                                />
                                {this.state.error.usuarioRUN && <span className={cx('form__error-message')}>{this.state.error.usuarioRUN[0]}</span>}
                            </div>
                            <div className="col-sm-2">
                                <input type="text" className="form-control"
                                       value={this.state.usuario.usuarioDV} onChange={this.onDvChange}
                                />
                                {this.state.error.usuarioDV && <span className={cx('form__error-message')}>{this.state.error.usuarioDV[0]}</span>}
                            </div>
                        </div>
                        {/* Nombres */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Nombres (*)</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control" placeholder="Primer Nombre"
                                       value={this.state.usuario.nombre1} onChange={this.onNombre1Change}
                                />
                                {this.state.error.nombre1 && <span className={cx('form__error-message')}>{this.state.error.nombre1[0]}</span>}
                            </div>
                            <div className="col-sm-5">
                                <input type="text" className="form-control" placeholder="Segundo Nombre"
                                       value={this.state.usuario.nombre2} onChange={this.onNombre2Change}
                                />
                                {this.state.error.nombre2 && <span className={cx('form__error-message')}>{this.state.error.nombre2[0]}</span>}
                            </div>
                        </div>
                        {/* Apellidos */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Apellidos (*)</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control" placeholder="Apellido Paterno"
                                       value={this.state.usuario.apellidoPaterno} onChange={this.onApellidoPaternoChange}
                                />
                                {this.state.error.apellidoPaterno && <span className={cx('form__error-message')}>{this.state.error.apellidoPaterno[0]}</span>}
                            </div>
                            <div className="col-sm-5">
                                <input type="text" className="form-control" placeholder="Apellido Materno"
                                       value={this.state.usuario.apellidoMaterno} onChange={this.onApellidoMaternoChange}
                                />
                                {this.state.error.apellidoMaterno && <span className={cx('form__error-message')}>{this.state.error.apellidoMaterno[0]}</span>}
                            </div>
                        </div>

                        <h4>Contacto</h4>
                        {/* Email */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Email</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control" placeholder="Email"
                                       value={this.state.usuario.email} onChange={this.onEmailChange}
                                />
                                {this.state.error.email && <span className={cx('form__error-message')}>{this.state.error.email[0]}</span>}
                            </div>
                            <div className="col-sm-5">
                                <input type="text" className="form-control" placeholder="Email Personal"
                                       value={this.state.usuario.emailPersonal} onChange={this.onEmailPersonalChange}
                                />
                                {this.state.error.emailPersonal && <span className={cx('form__error-message')}>{this.state.error.emailPersonal[0]}</span>}
                            </div>
                        </div>

                        <div className={cx('form-group', 'form__control-label')}>
                            <a className={cx("btn btn-block btn-default", 'btn-50-perc')} href="#" onClick={this.props.hideModal}>Cancelar</a>
                            <button className={cx("btn btn-block btn-primary", 'btn-50-perc')} type="submit">Agregar</button>
                        </div>
                    </form>
                </Modal.Body>
            </Modal>
        )
    }
}

export class ModalEditarUsuario extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            usuario: this.props.usuario,
            error: {}
        }
        this.onRunChange = (evt)=>{
            this.setState({ usuario: Object.assign({}, this.state.usuario, {usuarioRUN: evt.target.value}) })
        }
        this.onDvChange = (evt)=>{
            this.setState({ usuario: Object.assign({}, this.state.usuario, {usuarioDV: evt.target.value}) })
        }
        this.onNombre1Change = (evt)=>{
            this.setState({ usuario: Object.assign({}, this.state.usuario, {nombre1: evt.target.value}) })
        }
        this.onNombre2Change = (evt)=>{
            this.setState({ usuario: Object.assign({}, this.state.usuario, {nombre2: evt.target.value}) })
        }
        this.onApellidoPaternoChange = (evt)=>{
            this.setState({ usuario: Object.assign({}, this.state.usuario, {apellidoPaterno: evt.target.value}) })
        }
        this.onApellidoMaternoChange = (evt)=>{
            this.setState({ usuario: Object.assign({}, this.state.usuario, {apellidoMaterno: evt.target.value}) })
        }
        this.onEmailChange = (evt)=>{
            this.setState({ usuario: Object.assign({}, this.state.usuario, {email: evt.target.value}) })
        }
        this.onEmailPersonalChange = (evt)=>{
            this.setState({ usuario: Object.assign({}, this.state.usuario, {emailPersonal: evt.target.value}) })
        }

        this.handleSubmit = (evt)=>{
            evt.preventDefault()
            evt.stopPropagation()

            this.setState({error: {}}, ()=>{
                this.props.actualizarUsuario(this.state.usuario.id, this.state.usuario)
                    .then(()=>{
                        this.props.hideModal()
                    })
                    .catch(error=>{
                        this.setState({error: error.data})
                    })
            })
        }
    }
    render(){
        return(
            <Modal show={true} onHide={this.props.hideModal}>
                <Modal.Header closeButton>
                    <Modal.Title>Editando a : {this.state.usuario.nombre1} {this.state.usuario.apellidoPaterno} </Modal.Title>
                </Modal.Header>
                <Modal.Body>

                    <form className="form-horizontal" onSubmit={this.handleSubmit}>
                        <h4>Datos Personales</h4>
                        {/* RUN */}
                        <div className={cx("form-group", 'form__control-label')}>
                            <label className="col-sm-2 control-label">RUN (*)</label>
                            <div className="col-sm-3">
                                <input className="form-control"
                                       value={this.state.usuario.usuarioRUN} onChange={this.onRunChange}
                                />
                                {this.state.error.usuarioRUN && <span className={cx('form__error-message')}>{this.state.error.usuarioRUN[0]}</span>}
                            </div>
                            <div className="col-sm-2">
                                <input className="form-control" max="1"
                                       value={this.state.usuario.usuarioDV} onChange={this.onDvChange}
                                />
                            </div>
                        </div>
                        {/* Nombres */}
                        <div className={cx("form-group", 'form__control-label')}>
                            <label className="col-sm-2 control-label">Nombres (*)</label>
                            <div className="col-sm-5">
                                <input className="form-control" placeholder='Primer Nombre' min="3"
                                       value={this.state.usuario.nombre1} onChange={this.onNombre1Change}
                                />
                                {this.state.error.nombre1 && <span className={cx('form__error-message')}>{this.state.error.nombre1[0]}</span>}
                            </div>
                            <div className="col-sm-5">
                                <input className="form-control" placeholder='Segundo Nombre' min="3"
                                       value={this.state.usuario.nombre2} onChange={this.onNombre2Change}
                                />
                                {this.state.error.nombre2 && <span className={cx('form__error-message')}>{this.state.error.nombre2[0]}</span>}
                            </div>
                        </div>
                        {/* Apellidos */}
                        <div className={cx("form-group", 'form__control-label')}>
                            <label className="col-sm-2 control-label">Apellidos (*)</label>
                            <div className="col-sm-5">
                                <input className="form-control" placeholder='Apellido Paterno' min="3"
                                       value={this.state.usuario.apellidoPaterno} onChange={this.onApellidoPaternoChange}
                                />
                                {this.state.error.apellidoPaterno && <span className={cx('form__error-message')}>{this.state.error.apellidoPaterno[0]}</span>}
                            </div>
                            <div className="col-sm-5">
                                <input className="form-control" placeholder='Apellido Materno' min="3"
                                       value={this.state.usuario.apellidoMaterno} onChange={this.onApellidoMaternoChange}
                                />
                                {this.state.error.apellidoMaterno && <span className={cx('form__error-message')}>{this.state.error.apellidoMaterno[0]}</span>}
                            </div>
                        </div>

                        <h4>Contacto</h4>
                        {/* Email */}
                        <div className={cx("form-group", 'form__control-label')}>
                            <label className="col-sm-2 control-label">Email</label>
                            <div className="col-sm-5">
                                <input type="email" className="form-control" placeholder='Email SEI'
                                       value={this.state.usuario.email} onChange={this.onEmailChange}
                                />
                                {this.state.error.email && <span className={cx('form__error-message')}>{this.state.error.email[0]}</span>}
                            </div>
                            <div className="col-sm-5">
                                <input type="email" className="form-control" placeholder='Email personal'
                                       value={this.state.usuario.emailPersonal} onChange={this.onEmailPersonalChange}
                                />
                                {this.state.error.emailPersonal && <span className={cx('form__error-message')}>{this.state.error.emailPersonal[0]}</span>}
                            </div>
                        </div>

                        <div className={cx('editarUsuario_footer')}>
                            <a href="#" className={cx("btn btn-block btn-default", 'btn-50-perc')} onClick={this.props.hideModal}>Cancelar</a>
                            <button type="submit" className={cx("btn btn-block btn-primary", 'btn-50-perc')}>Modificar</button>
                        </div>
                    </form>

                </Modal.Body>
            </Modal>
        )
    }
}

export const ModalBloquearUsuario = ({hideModal, onBloquear})=>
    <Modal show={true} onHide={hideModal}>
        <Modal.Header closeButton>
            <Modal.Title>¿Esta seguro que desea bloquear al usuario?</Modal.Title>
        </Modal.Header>
        <Modal.Footer className={cx('bloquearUsuario_footer')} >
            <button className="btn btn-block btn-default" onClick={hideModal}>Cancelar</button>
            <button className="btn btn-block btn-danger" onClick={onBloquear}>Boquear usuario</button>
        </Modal.Footer>
    </Modal>

export const ModalCambiarContrasena = ({hideModal, onAceptar})=>{
    let refContrasena = null
    const onCambiar = ()=>{
        onAceptar(refContrasena.value)
    }
    return (
        <Modal show={true} onHide={hideModal}>
            <Modal.Header closeButton>
                <Modal.Title>Cambio de contraseña</Modal.Title>
            </Modal.Header>
            <Modal.Body>
                <form className="form-horizontal">
                    <div className="form-group">
                        <label htmlFor="password" className="col-sm-2 control-label">Contraseña</label>
                        <div className="col-xs-10">
                            <input className="form-control" type="text"
                                   ref={ref=> refContrasena=ref}
                                   onKeyDown={evt=>{
                                       if(evt.keyCode==13){
                                           onCambiar()
                                           evt.preventDefault()
                                           return
                                       }
                                   }}
                            />
                        </div>
                    </div>
                </form>
            </Modal.Body>
            <Modal.Footer className={cx('cambiarContrasena_footer')} >
                <button className="btn btn-block btn-default" onClick={hideModal}>Cancelar</button>
                <button className="btn btn-block btn-success" onClick={onCambiar}>Cambiar</button>
            </Modal.Footer>
        </Modal>
    )
}

export class ModalHistorial extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            nominas: [],
            nombre: '',
            done: false
        }
    }
    componentWillMount(){
        this.props.verHistorial()
            .then(historial=>{
                this.setState({
                    nombre: historial.nombre,
                    nominas: historial.nominas,
                    done: true
                })
            })
            .catch(error=>{
                console.error(error)
            })
    }
    render(){
        return(
            <Modal show={true} onHide={this.props.hideModal}>
                <Modal.Header closeButton>
                    <Modal.Title>Historial de nominas {this.state.nombre!=''? ` de ${this.state.nombre}` : ''}</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <table className="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Cargo</th>
                                <th>Local</th>
                            </tr>
                        </thead>
                        <tbody>
                        {this.state.done?
                            (this.state.nominas.length>0?
                                this.state.nominas.map((nom, index)=>
                                    <tr key={index}>
                                        <td>{index+1}</td>
                                        <td>{nom.fecha}</td>
                                        <td>{nom.role}</td>
                                        <td>{nom.inventario}</td>
                                    </tr>
                                )
                                :
                                <tr>
                                    <td>No tiene inventarios registrados</td>
                                </tr>
                            )
                            :
                            <tr>
                                <td>Cargando..</td>
                            </tr>
                        }

                        </tbody>
                    </table>
                </Modal.Body>
                <Modal.Footer>
                    <button className="btn btn-btn-default" onClick={this.props.hideModal}>Cerrar</button>
                </Modal.Footer>
            </Modal>
        )
    }
}