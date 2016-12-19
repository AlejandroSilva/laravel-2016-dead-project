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
            usuarios: [],
            comunas: [],
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
            console.log('actualizando' , usuarioActualizado)
            let request = api.usuario(idUsuario).actualizar(usuarioActualizado)
            request
                .then(resp=>{
                    // no es necesario actualizar la lista completa... pero da lo mismo
                    this.fetchUsuarios()
                })
            return request
        }
        this.fetchUsuarios = ()=>{
            api.usuarios.fetch()
                .then(usuarios=>{
                    this.setState({usuarios})
                })
        }
        this.bloquearUsuario = (idUsuario)=>{
            console.log('bloqueando a ', idUsuario)
            api.usuario(idUsuario).bloquear()
                .then(usuario=>{
                    this.fetchUsuarios()
                })
        }
        this.cambiarContrasena = (idUsuario, contrasena)=>{
            api.usuario(idUsuario).cambiarContrasena(contrasena)
        }
        this.verHistorial = (idUsuario)=>{
            api.usuario(idUsuario).historial()
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
//                    seleccionarUsuario={this.seleccionarUsuario}
                />
            </div>
        )
    }
}