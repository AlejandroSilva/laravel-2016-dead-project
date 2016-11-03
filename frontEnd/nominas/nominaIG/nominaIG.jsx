import React from 'react'
let PropTypes = React.PropTypes
import _ from 'lodash'
import api from '../../apiClient/v1'
// Componentes
import * as css from './nominaIG.css'
import ReactNotify from 'react-notify'
import * as ReactNotifyCSS from '../../shared/ReactNotify/ReactNotify.css'
import Modal from 'react-bootstrap/lib/Modal.js'
import { FormularioUsuario } from './FormularioUsuario.jsx'
import { PanelDatos } from './PanelDatos.jsx'
import { PanelEstados } from './PanelEstados.jsx'
import { PanelCaptadorSEI, PanelCaptador } from './PanelCaptador.jsx'

export class NominaIG extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            showModal: false,
            RUNbuscado: '',
            esTitular: true,
            // --------
            nomina: this.props.nomina
        }
        this.ajaxErrorHandler = (errorTitle)=>{
            return (err)=>{
                if(err.status==500)
                    return this.refs.notificator.error("Error critico en el servidor", "Contactese con el departamento de informática.", 4*1000);
                // en otro error, buscar el cuerpo del mensaje
                let msgs = _.values(err.data).join('. ')
                console.log(err)
                console.log(errorTitle, err.data)
                this.refs.notificator.error(errorTitle, msgs, 4*1000);
            }
        }
    }

    getChildContext(){
        return {
            // modificar lider
            agregarLider: (run)=>{
                if (run === '')
                    return console.log('RUN vacio, no se hace nada')
                api.nomina(this.props.nomina.idNomina).agregarLider(run)
                    .then(nomina=> {
                        this.refs.notificator.success("Nómina", "Lider agregado", 4*1000);
                        this.setState({nomina})
                    })
                    .catch(this.ajaxErrorHandler('Error al asignar el Lider'))
            },
            quitarLider: ()=>{
                api.nomina(this.props.nomina.idNomina).quitarLider()
                    .then(nomina=>{
                        console.log('lider quitado correctamente')
                        this.setState({nomina})
                    })
                    .catch(this.ajaxErrorHandler('Error al quitar el Lider'))
            },
            // modificar supervisor
            agregarSupervisor: (run)=>{
                if (run === '')
                    return console.log('RUN vacio, no se hace nada')
                api.nomina(this.props.nomina.idNomina).agregarSupervisor(run)
                    .then(nomina=> {
                        this.refs.notificator.success("Nómina", "Supervisor agregado", 4*1000);
                        this.setState({nomina})
                    })
                    .catch(this.ajaxErrorHandler('Error al asignar el Supervisor'))
            },
            quitarSupervisor: ()=>{
                api.nomina(this.props.nomina.idNomina).quitarSupervisor()
                    .then(nomina=>{
                        console.log('dotacion actualizada')
                        this.setState({nomina})
                    })
                    .catch(this.ajaxErrorHandler('Error al quitar el Supervisor'))
            },
            // modificar dotacion
            agregarOperador: (esTitular, idCaptador, run)=>{
                console.log(esTitular, idCaptador, run)
                if(run===''){
                    return console.log('RUN vacio, no se hace nada');
                }
                api.nomina(this.props.nomina.idNomina).agregarOperador(run, esTitular, idCaptador)
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
                            this.refs.notificator.success("Nómina", "Usuario agregado correctamente", 4*1000);
                            this.setState({nomina: nominaActualizada})
                        }
                    })
                    .catch(this.ajaxErrorHandler('Agregar Operador'))
            },
            quitarOperador: (idUsuario)=>{
                console.log('quitar usuario', idUsuario)
                api.nomina(this.props.nomina.idNomina).quitarOperador(idUsuario)
                    .then(nomina=>{
                        console.log('dotacion actualizada')
                        this.setState({nomina})
                    })
                    .catch(this.ajaxErrorHandler('error al quitar el operador'))
            }
        }
    }

    // ######### Metodos para cambiar la dotacion de la nomina #########
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
        return api.nomina(this.state.nomina.idNomina).enviar()
            .then(nomina=>{
                this.setState({nomina})
            })
            .catch(err=>{
                let msgs = _.values(err.data).join('. ')
                console.log('error al enviar nomina', err.data)
                this.refs.notificator.error("Error", msgs, 4*1000);
            })
    }
    aprobarNomina(){
        // al aprobar una nomina, esta pasa al estado "aprobada"
        return api.nomina(this.state.nomina.idNomina).aprobar()
            .then(nomina=>{
                this.setState({nomina})
            })
            .catch(err=> {
                let msgs = _.values(err.data).join('. ')
                console.log('error al aprobar nomina', err.data)
                this.refs.notificator.error("Error", msgs, 4 * 1000);
            })
    }
    rechazarNomina(){
        // al rechazar una nomina, esta vuelve a quedar en estado pendiente
        return api.nomina(this.state.nomina.idNomina).rechazar()
            .then(nomina=>{
                this.setState({nomina})
            })
            .catch(err=> {
                let msgs = _.values(err.data).join('. ')
                console.log('error al rechazar nomina', err.data)
                this.refs.notificator.error("Error", msgs, 4 * 1000);
            })
    }
    informarNomina(){
        // al aprobar una nomina, esta pasa al estado "aprobada"
        return api.nomina(this.props.state.idNomina).informar()
            .then(nomina=>{
                this.setState({nomina})
            })
            .catch(err=> {
                let msgs = _.values(err.data).join('. ')
                console.log('Error al aprobar nomina', err.data)
                this.refs.notificator.error("Error", msgs, 4 * 1000);
            })
    }
    completarSinCorreo(){
        // al aprobar una nomina, esta pasa al estado "aprobada"
        return api.nomina(this.state.nomina.idNomina).completarSinCorreo()
            .then(nomina=>{
                this.setState({nomina})
            })
            .catch(err=> {
                let msgs = _.values(err.data).join('. ')
                console.log('Error al aprobar nomina', err.data)
                this.refs.notificator.error("Error", msgs, 4 * 1000);
            })
    }
    rectificarNomina(){
        // al aprobar una nomina, esta pasa al estado "aprobada"
        return api.nomina(this.state.nomina.idNomina)   .rectificar()
            .then(nomina=>{
                this.setState({nomina})
            })
            .catch(err=> {
                let msgs = _.values(err.data).join('. ')
                console.log('Error al rectificar nomina', err.data)
                this.refs.notificator.error("Error", msgs, 4 * 1000);
            })
    }

    render(){
        let nominaPendiente = this.state.nomina.idEstadoNomina==2

        let operadoresAsignados = this.state.nomina.supervisor? this.state.nomina.dotacionTitular.length+1 : this.state.nomina.dotacionTitular.length
        let totalAsignados = this.state.nomina.lider? operadoresAsignados +1 : operadoresAsignados
        // la nomina tiene todos los captadores asignados? y el total corresponde a lo asignado?
        let nominaCompleta = (this.state.nomina.dotacionTotal==totalAsignados)
        console.log('totalAsignados', totalAsignados)

        return (
            <div className="container-fluid">
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
                    nomina={this.state.nomina}
                />

                {this.props.permisos.verTodasNominas?
                    <PanelCaptadorSEI
                        captadorSEI={this.state.nomina.captadorSEI}
                        lider={this.state.nomina.lider}
                        supervisor={this.state.nomina.supervisor}
                        maximoOperadores={this.state.nomina.dotacionOperadores}

                        // permisos (solo puede editar si esta pendente, y es un captador asignado o es un "captador SEI"
                        editarLiderSupervisor={nominaPendiente && this.props.permisos.cambiarLiderSupervisor}
                        editarOperadores={nominaPendiente && this.props.permisos.editarTodasNominas}
                    />
                    :
                    null
                }

                {this.state.nomina.captadores.map(captador=> {
                    let esMiNomina = this.props.permisos.editarIdCaptador == captador.idCaptador

                    return (this.props.permisos.verTodasNominas || esMiNomina)?
                         <PanelCaptador
                            key={captador.idCaptador}
                            captador={captador}
                            // permisos (solo puede editar si esta pendente, y es un captador asignado o es un "captador SEI"
                            editarLiderSupervisor={nominaPendiente && this.props.permisos.cambiarLiderSupervisor}
                            editarOperadores={nominaPendiente && (this.props.permisos.editarTodasNominas || esMiNomina)}
                        />
                        :
                        null
                })}

                <PanelEstados
                    idEstado={this.state.nomina.idEstadoNomina}
                    haSidoRectificada={this.state.nomina.rectificada}
                    enviarNomina={this.enviarNomina.bind(this)}
                    aprobarNomina={this.aprobarNomina.bind(this)}
                    rechazarNomina={this.rechazarNomina.bind(this)}
                    informarNomina={this.informarNomina.bind(this)}
                    completarSinCorreo={this.completarSinCorreo.bind(this)}
                    rectificarNomina={this.rectificarNomina.bind(this)}
                    nominaCompleta={nominaCompleta}
                    // permisos
                    permisos={this.props.permisos}
                />
            </div>
        )
    }
}

NominaIG.propTypes = {
    nomina: PropTypes.object.isRequired,
    comunas: PropTypes.arrayOf(PropTypes.object).isRequired,
    // Permisos
    permisos: PropTypes.object.isRequired
}
NominaIG.childContextTypes = {
    agregarLider: PropTypes.func,
    quitarLider: PropTypes.func,
    agregarSupervisor: PropTypes.func,
    quitarSupervisor: PropTypes.func,
    agregarOperador: PropTypes.func,
    quitarOperador: PropTypes.func
}