import React from 'react'
let PropTypes = React.PropTypes
import _ from 'lodash'
import api from '../../apiClient/v1'
// Componentes
import * as css from './nominaIG.css'
import ReactNotify from 'react-notify'
import * as ReactNotifyCSS from '../shared/ReactNotify.css'
import Modal from 'react-bootstrap/lib/Modal.js'
import { FormularioUsuario } from './FormularioUsuario.jsx'
import { PanelDatos } from './PanelDatos.jsx'
import { PanelDotaciones } from './PanelDotaciones.jsx'
import { PanelEstados } from './PanelEstados.jsx'

export class NominaIG extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            showModal: false,
            RUNbuscado: '',
            esTitular: true,
            // --------
            idEstadoNomina: this.props.nomina.estado.idEstadoNomina,
            lider: this.props.nomina.lider,
            supervisor: this.props.nomina.supervisor,
            dotacionTitular: this.props.nomina.dotacionTitular,
            dotacionReemplazo: this.props.nomina.dotacionReemplazo
        }
    }

    // ######### Metodos para cambiar la dotacion de la nomina #########
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
                console.log('ha ocurrido un error al asignar el lider', err.data)
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
                let msgs = _.values(err.data).join('. ')
                console.log('ha ocurrido un error al quitar el Lider', err.data)
                this.refs.notificator.error("Error al quitar el Lider", msgs, 4*1000);
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
                console.log('ha ocurrido un error al asignar el Supervisor', err.data)
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
                let msgs = _.values(err.data).join('. ')
                console.log('ha ocurrido un error al quitar el Supervisor', err.data)
                this.refs.notificator.error("Error", msgs, 4*1000);
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
                console.log('ha ocurrido un error al agregar un operador', err.data)
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
                let msgs = _.values(err.data).join('. ')
                console.log('error al quitar el operador', err.data)
                this.refs.notificator.error("Error", msgs, 4*1000);
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

        api.usuarios.nuevoOperador(datos)
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

    // ######### Metodos para cambiar el estado de la nomina #########
    enviarNomina(){
        // al enviar una nomina, esta pasa al estado "enviada"
        return api.nomina.enviar(this.props.nomina.idNomina)
            .then(nomina=>{
                this.setState({idEstadoNomina: nomina.estado.idEstadoNomina})
            })
            .catch(err=>{
                let msgs = _.values(err.data).join('. ')
                console.log('error al enviar nomina', err.data)
                this.refs.notificator.error("Error", msgs, 4*1000);
            })
    }
    aprobarNomina(){
        // al aprobar una nomina, esta pasa al estado "aprobada"
        return api.nomina.aprobar(this.props.nomina.idNomina)
            .then(nomina=>{
                this.setState({idEstadoNomina: nomina.estado.idEstadoNomina})
            })
            .catch(err=> {
                let msgs = _.values(err.data).join('. ')
                console.log('error al aprobar nomina', err.data)
                this.refs.notificator.error("Error", msgs, 4 * 1000);
            })
    }
    rechazarNomina(){
        // al rechazar una nomina, esta vuelve a quedar en estado pendiente
        return api.nomina.rechazar(this.props.nomina.idNomina)
            .then(nomina=>{
                this.setState({idEstadoNomina: nomina.estado.idEstadoNomina})
            })
            .catch(err=> {
                let msgs = _.values(err.data).join('. ')
                console.log('error al rechazar nomina', err.data)
                this.refs.notificator.error("Error", msgs, 4 * 1000);
            })
    }
    informarNomina(){
        // al aprobar una nomina, esta pasa al estado "aprobada"
        return api.nomina.informar(this.props.nomina.idNomina)
            .then(nomina=>{
                this.setState({idEstadoNomina: nomina.estado.idEstadoNomina})
            })
            .catch(err=> {
                let msgs = _.values(err.data).join('. ')
                console.log('Error al aprobar nomina', err.data)
                this.refs.notificator.error("Error", msgs, 4 * 1000);
            })
    }
    rectificarNomina(){
        // al aprobar una nomina, esta pasa al estado "aprobada"
        return api.nomina.rectificar(this.props.nomina.idNomina)
            .then(nomina=>{
                this.setState({idEstadoNomina: nomina.estado.idEstadoNomina})
            })
            .catch(err=> {
                let msgs = _.values(err.data).join('. ')
                console.log('Error al rectificar nomina', err.data)
                this.refs.notificator.error("Error", msgs, 4 * 1000);
            })
    }

    render(){
        let operadoresAsignados = this.state.supervisor? this.state.dotacionTitular.length+1 : this.state.dotacionTitular.length
        let totalAsignados = this.state.lider? operadoresAsignados +1 : operadoresAsignados
        // la nomina tiene todos los captadores asignados? y el total corresponde a lo asignado?
        let nominaCompleta = (this.state.dotacionTitular.length==operadoresAsignados) &&
                             (this.props.nomina.dotacionTotal==totalAsignados)
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

                <PanelDatos
                    inventario={this.props.inventario}
                    nomina={this.props.nomina}
                />

                <PanelDotaciones
                    // general
                    dotacionOperadores={this.props.nomina.dotacionOperadores}
                    // para poder editar, se debe considerar: estado, permisos, y otras variables
                    liderEditable={this.state.idEstadoNomina==2 && this.props.permisos.cambiarLider}
                    supervisorEditable={this.state.idEstadoNomina==2 && this.props.permisos.cambiarSupervisor}
                    dotacionEditable={this.state.idEstadoNomina==2 && this.props.permisos.cambiarDotacion}
                    // dotacion
                    lider={this.state.lider}
                    supervisor={this.state.supervisor}
                    dotacionTitular={this.state.dotacionTitular}
                    dotacionReemplazo={this.state.dotacionReemplazo}
                    // metodos
                    agregarOperadorTitular={this.agregarOperador.bind(this, true)}
                    agregarOperadorReemplazo={this.agregarOperador.bind(this, false)}
                    quitarOperador={this.quitarOperador.bind(this)}
                    agregarLider={this.agregarLider.bind(this)}
                    quitarLider={this.quitarLider.bind(this)}
                    agregarSupervisor={this.agregarSupervisor.bind(this)}
                    quitarSupervisor={this.quitarSupervisor.bind(this)}
                />

                <PanelEstados
                    idEstado={this.state.idEstadoNomina}
                    enviarNomina={this.enviarNomina.bind(this)}
                    aprobarNomina={this.aprobarNomina.bind(this)}
                    rechazarNomina={this.rechazarNomina.bind(this)}
                    informarNomina={this.informarNomina.bind(this)}
                    rectificarNomina={this.rectificarNomina.bind(this)}
                    nominaCompleta={nominaCompleta}
                    // permisos
                    permisos={this.props.permisos}
                />

                <a href={`/programacionIG/nomina/${this.props.nomina.publicIdNomina}/pdf`} className="btn btn-xs pull-right">Descargar Pdf</a>
                <a href={`/programacionIG/nomina/${this.props.nomina.publicIdNomina}/excel`} className="btn btn-xs pull-right">Descargar Excel</a>
            </div>
        )
    }
}

NominaIG.propTypes = {
    usuario: PropTypes.object.isRequired,
    inventario: PropTypes.object.isRequired,
    nomina: PropTypes.object.isRequired,
    comunas: PropTypes.arrayOf(PropTypes.object).isRequired,
    // Permisos
    permisos: PropTypes.object.isRequired
}
NominaIG.defaultProps = {
    usuario: {}
}