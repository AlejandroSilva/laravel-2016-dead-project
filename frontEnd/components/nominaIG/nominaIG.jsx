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
            esTitular: true,
            lider: this.props.nomina.lider,
            supervisor: this.props.nomina.supervisor,
            dotacionTitular: this.props.nomina.dotacionTitular,
            dotacionReemplazo: this.props.nomina.dotacionReemplazo
        }
    }

    agregarLider(run) {
        if (run === '') {
            return console.log('RUN vacio, no se hace nada');
        }
        api.nomina.agregarLider(this.props.nomina.idNomina, run)
            .then(nominaActualizada=> {
                this.refs.notificator.success("Nómina", "Lider agregado", 4*1000);
                this.setState({
                    lider: nominaActualizada.lider,
                    supervisor: nominaActualizada.supervisor,
                    dotacionTitular: nominaActualizada.dotacionTitular,
                    dotacionReemplazo: nominaActualizada.dotacionReemplazo
                })
            })
            .catch(err=>{
                let msgs = _.values(err.data).join('. ')
                console.log('ha ocurrido un error al asignar el lider', err)
                this.refs.notificator.error("Error al asignar el Lider", msgs, 4*1000);
            })
    }
    quitarLider(){
        api.nomina.quitarLider(this.props.nomina.idNomina)
            .then(nominaActualizada=>{
                console.log('dotacion actualizada')
                this.setState({
                    lider: nominaActualizada.lider,
                    supervisor: nominaActualizada.supervisor,
                    dotacionTitular: nominaActualizada.dotacionTitular,
                    dotacionReemplazo: nominaActualizada.dotacionReemplazo
                })
            })
            .catch(err=>{
                console.log('ha ocurrido un error al quitar el lider', err)
                alert('ha ocurrido un error al quitar el lider', err)
            })
    }

    agregarSupervisor(run) {
        if (run === '') {
            return console.log('RUN vacio, no se hace nada');
        }
        api.nomina.agregarSupervisor(this.props.nomina.idNomina, run)
            .then(nominaActualizada=> {
                this.refs.notificator.success("Nómina", "Supervisor agregado", 4*1000);
                this.setState({
                    lider: nominaActualizada.lider,
                    supervisor: nominaActualizada.supervisor,
                    dotacionTitular: nominaActualizada.dotacionTitular,
                    dotacionReemplazo: nominaActualizada.dotacionReemplazo
                })
            })
            .catch(err=>{
                let msgs = _.values(err.data).join('. ')
                console.log('ha ocurrido un error al asignar el Supervisor', err)
                this.refs.notificator.error("Error al asignar el Supervisor", msgs, 4*1000);
            })
    }
    quitarSupervisor(){
        api.nomina.quitarSupervisor(this.props.nomina.idNomina)
            .then(nominaActualizada=>{
                console.log('dotacion actualizada')
                this.setState({
                    lider: nominaActualizada.lider,
                    supervisor: nominaActualizada.supervisor,
                    dotacionTitular: nominaActualizada.dotacionTitular,
                    dotacionReemplazo: nominaActualizada.dotacionReemplazo
                })
            })
            .catch(err=>{
                console.log('ha ocurrido un error al quitar el Supervisor', err)
                alert('ha ocurrido un error al quitar el Supervisor', err)
            })
    }
    
    agregarOperador(esTitular, run){
        if(run===''){
            return console.log('RUN vacio, no se hace nada');
        }
        api.nomina.agregarOperador(this.props.nomina.idNomina, run, esTitular)
            .then(response=>{
                let nominaActualizada = response.data
                let statusCode = response.status

                // si no se encuentra el usuario, se debe mostrar el formulario para crear uno
                if(statusCode==204){
                    console.log("RUN no existe, mostrando formulario")
                    this.setState({
                        showModal: true,
                        RUNbuscado: run,
                        esTitular
                    })
                } else if(statusCode==200){
                    this.refs.notificator.error("Nómina", "El usuario ya existe en la nómina", 4*1000);
                    //console.log('operador ya existe, dotacion sin cambios', dotacion)
                    this.setState({
                        lider: nominaActualizada.lider,
                        supervisor: nominaActualizada.supervisor,
                        dotacionTitular: nominaActualizada.dotacionTitular,
                        dotacionReemplazo: nominaActualizada.dotacionReemplazo
                    })

                } else if(statusCode==201){
                    // se agrego el usuario a la dotacion, retornan la dotacion actualizada
                    this.refs.notificator.success("Nómina", "Usuario agregado correctamente", 4*1000);
                    //console.log('usuario agregado, dotacion actualizada', dotacion)
                    this.setState({
                        lider: nominaActualizada.lider,
                        supervisor: nominaActualizada.supervisor,
                        dotacionTitular: nominaActualizada.dotacionTitular,
                        dotacionReemplazo: nominaActualizada.dotacionReemplazo
                    })
                }
            })
            .catch(err=>{
                let msgs = _.values(err.data).join('. ')
                console.log('ha ocurrido un error al intentar obtener los datos del usuario. ', err)
                this.refs.notificator.error("Error", msgs, 4*1000);
            })
    }
    quitarOperador(run){
        console.log('quitar usuario', run)
        api.nomina.quitarOperador(this.props.nomina.idNomina, run)
            .then(nominaActualizada=>{
                console.log('dotacion actualizada')
                this.setState({
                    lider: nominaActualizada.lider,
                    supervisor: nominaActualizada.supervisor,
                    dotacionTitular: nominaActualizada.dotacionTitular,
                    dotacionReemplazo: nominaActualizada.dotacionReemplazo
                })
            })
            .catch(err=>{
                console.log('ha ocurrido un error al quitar el operador ', err)
                alert('ha ocurrido un error al quitar el operador ', err)
            })
    }
    // modificarOperador(run, datos) {
    //     datos.idRoleAsignado = 6;
    //     api.nomina.modificarOperador(this.props.nomina.idNomina, run, datos)
    //         .then(resp=>{
    //             console.log(resp)
    //         })
    //         .catch(err=>{
    //             alert('error al modificar ', err)
    //             console.log(err)
    //         })
    // }

    onNuevoUsuario(datos){
        console.log('enviando', datos)

        api.usuario.nuevoOperador(datos)
            .then(usuario=>{
                console.log('nuevo usuario creado', usuario)
                this.agregarOperador(this.state.esTitular, usuario.usuarioRUN)
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
                        <Modal.Title>Nuevo Operador</Modal.Title>
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

                <div className="row">
                    <div className="col-sm-6">
                        {/* Datos generales del inventario */}
                        <div className="panel panel-default">
                            <div className={'panel-heading '+css.panelDatos_heading}>Inventario</div>
                                <table className={'table table-compact table-striped '+css.tablaDatos}>
                                    <tbody>
                                        <tr>
                                            <td>Cliente</td><td>{this.props.inventario.local.cliente.nombreCorto}</td>
                                            <td>Dotación asignada</td><td>{this.props.nomina.dotacionAsignada}</td>
                                        </tr>
                                        <tr>
                                            <td>Local</td><td>({this.props.inventario.local.numero}) {this.props.inventario.local.nombre}</td>
                                            <td></td><td></td>
                                        </tr>
                                        <tr>
                                            <td>Fecha programada</td><td>{this.props.inventario.inventario_fechaProgramada}</td>
                                            <td></td><td></td>
                                        </tr>
                                        <tr>
                                            <td>Hr. llegada lider</td><td>{this.props.nomina.horaPresentacionLider}</td>
                                            <td></td><td></td>
                                        </tr>
                                        <tr>
                                            <td>Hr. llegada equipo</td><td>{this.props.nomina.horaPresentacionEquipo}</td>
                                            <td></td><td></td>
                                        </tr>
                                    </tbody>
                                </table>
                        </div>
                    </div>
                    <div className="col-sm-6">
                        {/* Datos generales del local*/}
                        <div className="panel panel-default">
                            <div className={'panel-heading '+css.panelDatos_heading}>Local</div>
                            <table className={'table table-compact '+css.tablaDatos}>
                                <tbody>
                                    <tr>
                                        <td>Dirección</td><td>{this.props.inventario.local.direccion}</td>
                                        <td>Hr.Apertura</td><td>{this.props.inventario.local.horaApertura}</td>
                                    </tr>
                                    <tr>
                                        <td>Comuna</td><td>{this.props.inventario.local.comuna_nombre}</td>
                                        <td>Hr.Cierre</td><td>{this.props.inventario.local.horaCierre}</td>
                                    </tr>
                                    <tr>
                                        <td>Región</td><td>{this.props.inventario.local.region_numero}</td>
                                        <td>Teléfono 1</td><td>{this.props.inventario.local.telefono1}</td>
                                    </tr>
                                    <tr>
                                        <td>Formato Local</td><td>{this.props.inventario.local.formatoLocal_nombre}</td>
                                        <td>Teléfono 2</td><td>{this.props.inventario.local.telefono2}</td>
                                    </tr>
                                    <tr>
                                        <td></td><td></td>
                                        <td>Correo</td><td>{this.props.inventario.local.emailContacto}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div className="row">
                    {/* Dotacion Titular */}
                    <section className="col-sm-6">
                        <div className="panel panel-primary">
                            <div className={'panel-heading '+css.panelDatos_heading}>Personal Asignado</div>
                            <table className={'table table-striped table-bordered table-hover table-condensed '+css.tablaDotacion}>
                                <colgroup>
                                    <col className={css.colCorrelativo}/>
                                    <col className={css.colUsuarioRUN}/>
                                    <col className={css.colUsuarioDV}/>
                                    <col className={css.colNombre}/>
                                    <col className={css.colCargo}/>
                                    <col className={css.colOpciones}/>
                                </colgroup>
                                <thead>
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
                                    {/* Lider */}
                                    <RowOperador
                                        correlativo={1}
                                        operador={ this.state.lider }
                                        cargo="Lider"
                                        agregarUsuario={this.agregarLider.bind(this)}
                                        quitarUsuario={this.quitarLider.bind(this)}
                                    />
                                    {/* Supervisor */}
                                    <RowOperador
                                        correlativo={2}
                                        operador={ this.state.supervisor }
                                        cargo="Supervisor"
                                        agregarUsuario={this.agregarSupervisor.bind(this)}
                                        quitarUsuario={this.quitarSupervisor.bind(this)}
                                    />
                                    {/* Dotación */}
                                    {_.range(1, this.props.nomina.dotacionAsignada).map(index=>{
                                        let operador = this.state.dotacionTitular[index]
                                        return <RowOperador
                                            key={index}
                                            correlativo={index+2}
                                            operador={operador}
                                            cargo="Operador"
                                            agregarUsuario={this.agregarOperador.bind(this, true)}
                                            quitarUsuario={this.quitarOperador.bind(this)}
                                        />
                                    })}
                                </tbody>
                            </table>
                        </div>
                    </section>

                    {/* Dotacion Reemplazo */}
                    <section className="col-sm-6">
                        <div className="panel panel-primary">
                            <div className={'panel-heading '+css.panelDatos_heading}>Personal Reemplazo</div>
                            <table className={'table table-striped table-bordered table-hover table-condensed '+css.tablaDotacion}>
                                <colgroup>
                                    <col className={css.colCorrelativo}/>
                                    <col className={css.colUsuarioRUN}/>
                                    <col className={css.colUsuarioDV}/>
                                    <col className={css.colNombre}/>
                                    <col className={css.colCargo}/>
                                    <col className={css.colOpciones}/>
                                </colgroup>
                                <thead>
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
                                    {/* Dotación Reserva */}
                                    {_.range(0, this.props.nomina.dotacionAsignada).map(index=>{
                                        let operador = this.state.dotacionReemplazo[index]
                                        return <RowOperador
                                            key={index}
                                            correlativo={index+1}
                                            operador={ operador }
                                            cargo="Operador"
                                            agregarUsuario={this.agregarOperador.bind(this, false)}
                                            quitarUsuario={this.quitarOperador.bind(this)}
                                        />
                                    })}
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                <div className="row">
                    <div className="col-md-4 col-md-offset-4">
                        <button className="btn btn-block btn-sm btn-primary" disabled>Enviar nómina</button>
                        <button className="btn btn-block btn-sm btn-success" disabled>Aprobar nómina</button>
                    </div>
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