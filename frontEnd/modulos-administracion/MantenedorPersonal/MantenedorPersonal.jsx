// Librerias
import React from 'react'
import api from '../../apiClient/v1'
import moment from 'moment'

// Componentes
import { TablaPersonal } from './TablaPersonal.jsx'
import { ModalEdicion } from './ModalEdicion.jsx'

export class MantenedorPersonal extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            usuarios: [],
            comunas: []
        }

        this.seleccionarUsuario = (idUsuario)=>{
            api.usuario(idUsuario).get()
                .then(usuario=>{
                    this.refModalEdicion.showModal(usuario)
                })
        }
        this.modificarUsuario = (usuarioId, usuarioActualizado)=>{
            // fechaNacimiento viene como date, se debe convertir a texto
            usuarioActualizado.fechaNacimiento = moment(usuarioActualizado.fechaNacimiento).format('YYYY-MM-DD')
            console.log('actualizando' , usuarioActualizado)
            api.usuario(usuarioId).actualizar(usuarioActualizado)
                .then(resp=>{
                    console.log(resp)
                })
        }
    }
    componentWillMount(){
        api.usuarios.fetch()
            .then(usuarios=>{
                this.setState({usuarios})
            })
        api.otros.comunas()
            .then(comunas=>{
                this.setState({comunas})
            })
    }
    render(){
        return (
            <div>
                <div className="row">
                    <div className="col-md-6">
                        <h4>MantenedorPersonal</h4>
                    </div>
                    <div className="col-md-3">
                        <button className="btn btn-default">Agregar usuario</button>
                        <button className="btn btn-default">Agregar</button>
                        <button className="btn btn-default">Agregar</button>
                    </div>
                </div>
                <TablaPersonal
                    usuarios={this.state.usuarios}
                    seleccionarUsuario={this.seleccionarUsuario}
                />
                
                <ModalEdicion
                    ref={ref=>this.refModalEdicion=ref}
                    comunas={this.state.comunas}
                    modificarUsuario={this.modificarUsuario}
                />
            </div>
        )
    }
}

MantenedorPersonal.propTypes = {
    // numero: React.PropTypes.number.isRequired,
    // texto: React.PropTypes.string.isRequired,
    // objeto: React.PropTypes.object.isRequired,
    // arreglo: React.PropTypes.arrayOf(PropTypes.object).isRequired
}