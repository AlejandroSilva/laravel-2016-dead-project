// Librerias
import React from 'react'
// Componentes
import Modal from 'react-bootstrap/lib/Modal.js'
// Styles
import * as cssModal from './modal.css'


export class ModalContainer extends React.Component {
    constructor(props, context) {
        super(props, context)
        this.state = {
            modalVisible: false
        }
        this.showModal = ()=>{
            this.setState({modalVisible: true})
        }
        this.hideModal = ()=>{
            this.setState({modalVisible: false})
        }
    }
    getChildContext(){
        return {
            $_showModal: this.showModal,
            $_hideModal: this.hideModal
        }
    }

    render(){
        // se tomar el buttonComponent, y se le agrega el evento "showModal"
        return (
            <div>
                {React.cloneElement(this.props.buttonComponent, {
                    onClick: this.showModal
                })}
                <Modal
                    show={this.state.modalVisible}
                    //onEnter={this.props.onEnter}
                    onHide={this.hideModal}
                    animation={false}
                    dialogClassName={cssModal.modalTransferencia}>
                    <Modal.Header closeButton>
                        <Modal.Title>{this.props.titulo}</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        {this.props.children}
                    </Modal.Body>
                </Modal>
            </div>
        )
    }
}
ModalContainer.childContextTypes = {
    $_showModal: React.PropTypes.func,
    $_hideModal: React.PropTypes.func
}
ModalContainer.defaultProps = {
    onEnter: ()=>{}
}
// ModalContainer.propTypes = {
//     botonHabilitado: React.PropTypes.bool.isRequired
// }