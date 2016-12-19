// Librerias
import React from 'react'
// Componentes
import Modal from 'react-bootstrap/lib/Modal.js'
//import { Tab, Tabs, TabList, TabPanel } from 'react-tabs'
import { FormularioUsuario } from './FormularioUsuario_.jsx'

// Sttyles
import * as cssModal from './modal.css'

export class ModalEdicion extends React.Component {
    constructor(props, context) {
        super(props, context)
    }

    render(){
        // se tomar el buttonComponent, y se le agrega el evento "showModal"
        return (
            <div>
                <Modal
                    show={this.state.visible}
                    onHide={this.hideModal}
                    animation={false}
                    dialogClassName={cssModal.modalEdicion}>
                    <Modal.Header closeButton>
                        <Modal.Title>Editar Usuario</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        
                        <FormularioUsuario
                            usuario={this.state.usuario}
                            enviarFormulario={this.props.modificarUsuario}
                            cancelarFormulario={this.hideModal}
                            comunas={this.props.comunas}
                        />
                    </Modal.Body>
                </Modal>
            </div>
        )
    }
}
// ModalEdicion.childContextTypes = {
//     $_showModal: React.PropTypes.func,
//     $_hideModal: React.PropTypes.func
// }
ModalEdicion.defaultProps = {
    onEnter: ()=>{}
}
ModalEdicion.propTypes = {
    // ocultarModal: React.PropTypes.func.isRequired
    comunas: React.PropTypes.arrayOf(React.PropTypes.object).isRequired,
    modificarUsuario: React.PropTypes.func
}