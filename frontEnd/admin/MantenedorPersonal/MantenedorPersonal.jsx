// Librerias
import React from 'react'
import api from '../../apiClient/v1'

// Componentes
import { TablaPersonal } from './TablaPersonal.jsx'
import { ModalTrigger, ModalAgregarUsuario } from './Modales.jsx'

export class MantenedorPersonal extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            usuariosRAW: [],
            usuarios: [],
            comunas: [],
            busquedaRUN: '',
            busquedaNombre: '',
            busquedaApellido: ''
        }
        this.nuevoUsuario = (datos)=>{
            let request = api.usuarios.nuevoUsuario(datos)
            request
                .then(resp=>{
                    this.fetchUsuarios()
                })
            return request
        }
        this.actualizarUsuario = (idUsuario, usuarioActualizado)=>{
            // fechaNacimiento viene como date, se debe convertir a texto
            let request = api.usuario(idUsuario).actualizar(usuarioActualizado)
            request
                .then(this.fetchUsuarios)// no es necesario actualizar la lista completa... pero da lo mismo
            return request
        }
        this.fetchUsuarios = ()=>{
            api.usuarios.fetch()
                .then(usuarios=>{
                    this.setState({
                        usuariosRAW: usuarios,
                    }, this.filtrarUsuarios)
                })
        }
        this.realizarBusqueda = (campo, busquedaOriginal)=>{
            let newState = {}
            newState[campo] = busquedaOriginal.trim().toLowerCase()
            this.setState(newState, this.filtrarUsuarios)
        }
        this.filtrarUsuarios = ()=>{
            this.setState({
                usuarios: this.state.usuariosRAW
                    .filter(us=> us.RUN.indexOf(this.state.busquedaRUN)>=0)
                    .filter(us=> us.apellidoPaterno.toLowerCase().indexOf(this.state.busquedaApellido)>=0  || us.apellidoMaterno.toLowerCase().indexOf(this.state.busquedaApellido)>=0)
                    .filter(us=> us.nombre1.toLowerCase().indexOf(this.state.busquedaNombre)>=0  || us.nombre2.toLowerCase().indexOf(this.state.busquedaNombre)>=0)
            })
        }
        this.bloquearUsuario = (idUsuario)=>{
            api.usuario(idUsuario).bloquear()
                .then(this.fetchUsuarios)
        }
        this.cambiarContrasena = (idUsuario, contrasena)=>{
            api.usuario(idUsuario).cambiarContrasena(contrasena)
        }
        this.verHistorial = (idUsuario)=>{
            return api.usuario(idUsuario).historial()
        }
    }
    componentWillMount(){
        this.fetchUsuarios()
        api.otros.comunas()
            .then(comunas=>{
                this.setState({comunas})
            })
    }
    render(){
        return(
            <div>
                <div className="row">
                    <div className="col-md-6">
                        <h1>Mantenedor de Personal</h1>
                    </div>
                    <div className="col-md-6">
                        <div style={{marginTop: '25px'}} className="pull-right">
                            <ModalTrigger>
                                {(isVisible, showModal, hideModal)=>
                                    <button className="btn btn-sm btn-success" onClick={showModal}>
                                        Agregar usuario
                                        {isVisible && (
                                            <ModalAgregarUsuario
                                                hideModal={hideModal}
                                                nuevoUsuario={this.nuevoUsuario}
                                            />
                                        )}
                                    </button>
                                }
                            </ModalTrigger>
                        </div>
                    </div>
                </div>

                <TablaPersonal
                    usuarios={this.state.usuarios}
                    actualizarUsuario={this.actualizarUsuario}
                    bloquearUsuario={this.bloquearUsuario}
                    cambiarContrasena={this.cambiarContrasena}
                    verHistorial={this.verHistorial}
                    // busqueda
                    busquedaRUN={this.state.busquedaRUN}
                    realizarBusquedaRUN={this.realizarBusqueda.bind(this, 'busquedaRUN')}
                    busquedaNombre={this.state.busquedaNombre}
                    realizarBusquedaNombre={this.realizarBusqueda.bind(this, 'busquedaNombre')}
                    busquedaApellido={this.state.busquedaApellido}
                    realizarBusquedaApellido={this.realizarBusqueda.bind(this, 'busquedaApellido')}
                />
            </div>
        )
    }
}