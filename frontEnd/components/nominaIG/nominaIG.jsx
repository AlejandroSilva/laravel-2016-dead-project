import React from 'react'
let PropTypes = React.PropTypes
import _ from 'lodash'
import api from '../../apiClient/v1'
// Componentes
import * as css from './nominaIG.css'
import { InputRun } from './InputRun.jsx'
//import { ModalNuevoUsuario } from './ModalNuevoUsuario.jsx'
import Modal from 'react-bootstrap/lib/Modal.js'
import { FormularioUsuario } from './FormularioUsuario.jsx'

export class NominaIG extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            showModal: false,
            RUNbuscado: ''
        }
    }

    buscarUsuario(run){
        api.usuario.buscarRUN(run)
            .then(usuario=>{
                // si no se encuentra el usuario, se debe mostrar el formulario para crear uno
                if(JSON.stringify(usuario)=='[]'){
                    console.log("usuario con run no existe")
                    this.setState({
                        showModal: true,
                        RUNbuscado: run
                    })
                }
                else
                    console.log('usuario encontrado', usuario)
            })
            .catch(err=>{
                alert('ha ocurrido un error al intentar obtener los datos del usuario. ', err)
            })
    }
    onNuevoUsuario(datos){
        console.log('enviando', datos)

        api.usuario.nuevoOperador(datos)
            .then(usuario=>{
                console.log('nuevo usuario ', usuario)
                this.setState({showModal: false})
            })
            .catch(err=>{
                alert('ha ocurrido un erro al crear un usuario ', err)
            })
    }
    onCancelarFormulario(){
        this.setState({showModal: false})
        console.log('formulario cancelado')
    }

    agregarUsuarioALaTabla(){

    }

    render(){
        return (
            <div className="container">
                <Modal
                    show={this.state.showModal}
                    onHide={this.onCancelarFormulario.bind(this)}
                    dialogClassName={css.modalAmplio}
                >
                    <Modal.Header closeButton>
                        <Modal.Title>Nuevo Usuario</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        {/* Formulario para agregar usuarios */}
                        <FormularioUsuario
                            comunas={this.props.comunas}
                            RUNbuscado={this.state.RUNbuscado}
                            enviarFormulario={this.onNuevoUsuario.bind(this)}
                            cancelarFormulario={this.onCancelarFormulario.bind(this)}
                        />
                    </Modal.Body>
                </Modal>

                <h1>
                    Nomina para <b>{this.props.inventario.local_nombre}</b> de <b>{this.props.inventario.cliente_nombreCorto}</b>
                </h1>
                <div className="row">
                    <div className="col-sm-6">
                        {/* Datos generales del inventario */}
                        <h3>Inventario</h3>
                        <table className={'table '+css.tablaDatosInventario}>
                            <tbody>
                                <tr>
                                    <td>Cliente</td><td>{this.props.inventario.cliente_nombreCorto}</td>
                                </tr>
                                <tr>
                                    <td>Local</td><td>({this.props.inventario.local_numero}) {this.props.inventario.local_nombre}</td>
                                </tr>
                                <tr>
                                    <td>Fecha programada</td><td>{this.props.inventario.inventario_fechaProgramada}</td>
                                </tr>
                                <tr>
                                    <td>Hr. llegada lider</td><td>HH:MM</td>
                                </tr>
                                <tr>
                                    <td>Hr. llegada equipo</td><td>HH:MM</td>
                                </tr>
                                <tr>
                                    <td>Dotación asignada</td><td>{this.props.nomina.dotacionAsignada}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div className="col-sm-6">
                        {/* Datos generales del local*/}
                        <h3>Local</h3>
                        <div className="row">
                            <table className={'table '+css.tablaDatosLocal}>
                                <tbody>
                                    <tr>
                                        <td>Dirección</td><td>{this.props.inventario.direccion}</td>
                                    </tr>
                                    <tr>
                                        <td>Comuna</td><td>{this.props.inventario.comuna_nombre}</td>
                                    </tr>
                                    <tr>
                                        <td>Región</td><td>{this.props.inventario.region_numero}</td>
                                    </tr>
                                    <tr>
                                        <td>Formato Local</td><td>{this.props.inventario.formatoLocal_nombre}</td>
                                    </tr>
                                    <tr>
                                        <td>Hr.Apertura</td><td>Hr.Apertura</td>
                                    </tr>
                                    <tr>
                                        <td>Hr.Cierre</td><td>Hr.Cierre</td>
                                    </tr>
                                    <tr>
                                        <td>Telefono</td><td>Telefono</td>
                                    </tr>
                                    <tr>
                                        <td>Correo</td><td>Correo</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div className="row">
                        {/* Datos generales del inventario */}
                        <table className="table table-striped table-bordered table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th colSpan="3">Titular</th>
                                    <th colSpan="3">Reemplazo</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th>#</th>
                                    <th>RUN</th>
                                    <th>DV</th>
                                    <th>Nombre</th>
                                    <th>RUN</th>
                                    <th>DV</th>
                                    <th>Nombre</th>
                                    <th>Cargo</th>
                                </tr>
                            </thead>
                            <tbody>
                                {_.range(1, this.props.nomina.dotacionAsignada).map(indice=>{
                                    return <tr key={indice}>
                                        <th>{indice}</th>
                                            {/* */}
                                        <th>
                                            <InputRun
                                                buscarUsuario={this.buscarUsuario.bind(this)}
                                            />
                                        </th>
                                        <th>DV</th>
                                        <th>Nombre</th>
                                        {/* Reemplazo */}
                                        <th>
                                            <InputRun
                                                buscarUsuario={this.buscarUsuario.bind(this)}
                                            />
                                        </th>
                                        <th>DV</th>
                                        <th>Nombre</th>
                                        <th>Cargo</th>
                                    </tr>
                                })}
                            </tbody>
                        </table>
                    </div>
                </div>
        )
    }
}

NominaIG.propTypes = {
    inventario: PropTypes.object.isRequired,
    usuario: PropTypes.object.isRequired,
    comunas: PropTypes.arrayOf(PropTypes.object).isRequired
}
NominaIG.defaultProps = {
    usuario: {}
}