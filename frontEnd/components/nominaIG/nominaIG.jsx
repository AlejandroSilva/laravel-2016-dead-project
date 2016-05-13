import React from 'react'
let PropTypes = React.PropTypes
import _ from 'lodash'
import api from '../../apiClient/v1'
// Componentes
import * as css from './nominaIG.css'
import ReactNotify from 'react-notify'
import * as ReactNotifyCSS from '../shared/ReactNotify.css'

//import { ModalNuevoUsuario } from './ModalNuevoUsuario.jsx'
import Modal from 'react-bootstrap/lib/Modal.js'
import { FormularioUsuario } from './FormularioUsuario.jsx'
import { RowOperador } from './RowOperador.jsx'

export class NominaIG extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            showModal: false,
            RUNbuscado: '',
            dotacion: this.props.nomina.dotacion
        }
    }

    agregarUsuario(run){
        //this.refs.notificator.success("Title.", "Msg - body.", 40*1000);
        if(run===''){
            return console.log('RUN vacio, no se hace nada');
        }
        api.nomina.agregarOperador(this.props.nomina.idNomina, run)
            .then(response=>{
                let dotacion = response.data
                let statusCode = response.status

                // si no se encuentra el usuario, se debe mostrar el formulario para crear uno
                if(statusCode==204){
                    console.log("RUN no existe, mostrando formulario")
                    this.setState({
                        showModal: true,
                        RUNbuscado: run
                    })
                } else if(statusCode==200){
                    console.log('operador ya existe, dotacion sin cambios', dotacion)
                    this.setState({dotacion})

                } else if(statusCode==201){
                    // se agrego el usuario a la dotacion, retornan la dotacion actualizada
                    console.log('usuario agregado, dotacion actualizada', dotacion)
                    this.setState({dotacion})
                }
            })
            .catch(err=>{
                console.log('ha ocurrido un error al intentar obtener los datos del usuario. ', err)
                alert('ha ocurrido un error al intentar obtener los datos del usuario. ', err)
            })
    }
    quitarUsuario(run){
        console.log('quitar usuario', run)
        api.nomina.quitarOperador(this.props.nomina.idNomina, run)
            .then(dotacion=>{
                console.log('dotacion actualizada')
                this.setState({dotacion})
            })
            .catch(err=>{
                console.log('ha ocurrido un error al quitar el operador ', err)
                alert('ha ocurrido un error al quitar el operador ', err)
            })
    }

    onNuevoUsuario(datos){
        console.log('enviando', datos)

        api.usuario.nuevoOperador(datos)
            .then(usuario=>{
                console.log('nuevo usuario creado', usuario)
                this.setState({showModal: false})
                this.agregarUsuario(usuario.usuarioRUN)
            })
            .catch(err=>{
                alert('ha ocurrido un erro al crear un usuario ', err)
            })
    }
    onCancelarFormulario(){
        this.setState({showModal: false})
        console.log('formulario cancelado')
    }

    render(){
        return (
            <div className="container">
                <ReactNotify ref='notificator' className={ReactNotifyCSS}/>
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
                    Nomina para <b>{this.props.inventario.local.nombre}</b> de <b>{this.props.inventario.local.cliente.nombreCorto}</b>
                </h1>
                <div className="row">
                    <div className="col-sm-6">
                        {/* Datos generales del inventario */}
                        <h3>Inventario</h3>
                        <table className={'table '+css.tablaDatosInventario}>
                            <tbody>
                                <tr>
                                    <td>Cliente</td><td>{this.props.inventario.local.cliente.nombreCorto}</td>
                                </tr>
                                <tr>
                                    <td>Local</td><td>({this.props.inventario.local.numero}) {this.props.inventario.local.nombre}</td>
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
                                        <td>Dirección</td><td>{this.props.inventario.local.direccion}</td>
                                    </tr>
                                    <tr>
                                        <td>Comuna</td><td>{this.props.inventario.local.comuna_nombre}</td>
                                    </tr>
                                    <tr>
                                        <td>Región</td><td>{this.props.inventario.local.region_numero}</td>
                                    </tr>
                                    <tr>
                                        <td>Formato Local</td><td>{this.props.inventario.local.formatoLocal_nombre}</td>
                                    </tr>
                                    <tr>
                                        <td>Hr.Apertura</td><td>{this.props.inventario.local.horaApertura}</td>
                                    </tr>
                                    <tr>
                                        <td>Hr.Cierre</td><td>{this.props.inventario.local.horaCierre}</td>
                                    </tr>
                                    <tr>
                                        <td>Telefono 1</td><td>{this.props.inventario.local.telefono1}</td>
                                    </tr>
                                    <tr>
                                        <td>Telefono 2</td><td>{this.props.inventario.local.telefono2}</td>
                                    </tr>
                                    <tr>
                                        <td>Correo</td><td>{this.props.inventario.local.emailContacto}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div className="row">
                    {/* Dotacion Titular */}
                    <table className="table table-striped table-bordered table-hover table-condensed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th colSpan="5">Titular</th>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>RUN</th>
                                <th>DV</th>
                                <th>Nombre</th>
                                <th>Cargo</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {_.range(0, this.props.nomina.dotacionAsignada).map(index=>{
                                let operador = this.state.dotacion[index]
                                return <RowOperador
                                    key={index}
                                    correlativo={index+1}
                                    operador={operador}
                                    agregarUsuario={this.agregarUsuario.bind(this)}
                                    quitarUsuario={this.quitarUsuario.bind(this)}
                                />
                            })}
                        </tbody>
                    </table>
                </div>

                <div className="row">
                    {/* Dotacion Reemplazo */}
                    <table className="table table-striped table-bordered table-hover table-condensed">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th colSpan="5">Reemplazo</th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th>RUN</th>
                            <th>DV</th>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th>Opciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        {_.range(0, this.props.nomina.dotacionAsignada).map(index=>{
                            let operador = {} //this.props.nomina.dotacion[index]
                            return <RowOperador
                                key={index}
                                correlativo={index+1}
                                operador={ {} }
                                agregarUsuario={this.agregarUsuario.bind(this)}
                                quitarUsuario={this.quitarUsuario.bind(this)}
                            />
                        })}
                        </tbody>
                    </table>
                </div>

            </div>
        )
    }
}

NominaIG.propTypes = {
    usuario: PropTypes.object.isRequired,
    inventario: PropTypes.object.isRequired,
    nomina: PropTypes.object.isRequired,
    comunas: PropTypes.arrayOf(PropTypes.object).isRequired
}
NominaIG.defaultProps = {
    usuario: {}
}