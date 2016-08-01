// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import Modal from 'react-bootstrap/lib/Modal.js'
// Estilos
import classNames from 'classnames/bind'
import * as css from './modalConfirmacion.css'
let cx = classNames.bind(css)


export class ModalConfirmacion extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            modalConfirmacionVisible: false,
        }
        this.showModal = ()=>
            this.setState({ modalConfirmacionVisible: true})
        this.hideModal = ()=>
            this.setState({ modalConfirmacionVisible: false})
    }
    render() {
        return (
            <div>
                <Modal
                    show={this.state.modalConfirmacionVisible}
                    onHide={this.hideModal}
                    container={this}
                    dialogClassName={cx('modal-confirmacion')}
                >
                    <Modal.Header closeButton>
                        <Modal.Title id="contained-modal-title">
                            {this.props.textModalHeader}
                        </Modal.Title>
                    </Modal.Header>
                    {/* si no tiene un textDescription, el modal no tiene cuerpo */}
                    {this.props.textDescription!==''?
                        <Modal.Body className={cx('modal-body')} >
                            {this.props.textDescription}
                        </Modal.Body>
                        :
                        null
                    }
                    <Modal.Footer className={cx('modal-footer')} >
                        <button className={cx('btn btn-block btn-default')} onClick={this.props.onCancel}>
                            {this.props.textCancel}
                        </button>
                        <button className={cx('btn btn-block', this.props.acceptClassname)} onClick={this.props.onAccept}>
                            {this.props.textAccept}
                        </button>
                    </Modal.Footer>
                </Modal>
            </div>
        )
    }
}
ModalConfirmacion.propTypes = {
    textModalHeader: PropTypes.string.isRequired,
    textDescription: PropTypes.string,
    textCancel: PropTypes.string.isRequired,
    textAccept: PropTypes.string.isRequired,
    acceptClassname: PropTypes.string,
    // Metodos
    onAccept: PropTypes.func.isRequired,
    onCancel: PropTypes.func.isRequired
}
ModalConfirmacion.defaultProps = {
    textDescription: '',
    acceptClassname: 'btn-primary'
}