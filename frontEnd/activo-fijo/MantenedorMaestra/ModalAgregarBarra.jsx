// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import Modal from 'react-bootstrap/lib/Modal.js'
// Estilos
import classNames from 'classnames/bind'
import * as css from './modales.css'
let cx = classNames.bind(css)

export class ModalAgregarBarra extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            modalVisible: false,
            barra: '',
            errors: {}
        }

        // Modal
        this.showModal = ()=>{
            this.setState({
                modalVisible: true,
                barra: '',
                errors: {}
            })
        }
        this.hideModal = ()=>{
            this.setState({ modalVisible: false})
        }
        this.changeBarra = (evt)=>{
            this.setState({
                barra: evt.target.value,
                errors: {}
            })
        }
        this.agregarBarra = (evt)=>{
            evt.preventDefault()
            // limpiar los errores
            this.setState({errors: {} })
            // agregar el barra, quitar los mensajes de error, y dejar el formulario en blanco
            this.props.agregarBarra({
                idArticuloAF: this.props.idArticuloSeleccionado,
                barra: this.state.barra
            })
                .then(()=>{
                    // al terminar, se oculta el modal
                    this.hideModal()
                })
                .catch(error=>{
                    if(error.status==400 || error.status==403)
                        this.setState({errors: error.data})
                })
        }
    }

    render() {
        return (
            <div>
                <Modal
                    show={this.state.modalVisible}
                    onHide={this.hideModal}
                    container={this}
                    dialogClassName={cx('modal-agregar-producto')}
                >
                    <Modal.Header closeButton>
                        <Modal.Title id="contained-modal-title">Agregar Barra</Modal.Title>
                    </Modal.Header>
                    <Modal.Body className={cx('modal-body')} >

                        <form onSubmit={this.agregarBarra}>
                            <div className="form-group">
                                <label>SKU de producto</label>
                                <input className="form-control" type="string" value={this.props.skuProducto} disabled/>
                                <p className={cx('error-msg')}>{this.state.errors.SKU} &nbsp;</p>
                            </div>

                            <div className="form-group">
                                <label>Barra</label>
                                <input className="form-control" type="string" placeholder="Escanee o dígite el código de barra"
                                       value={this.state.barra}
                                       onChange={this.changeBarra}
                                />
                                <p className={cx('error-msg')}>{this.state.errors.barra} &nbsp;</p>
                            </div>
                            <p className={cx('error-msg')}>{this.state.errors.error} &nbsp;</p>
                        </form>

                    </Modal.Body>
                    <Modal.Footer className={cx('modal-footer')} >
                        <button className={cx('btn btn-block btn-default')} onClick={this.hideModal}>Cancelar</button>
                        <button className={cx('btn btn-block btn-primary')} onClick={this.agregarBarra}>Agregar</button>
                    </Modal.Footer>
                </Modal>
            </div>
        )
    }
}
ModalAgregarBarra.propTypes = {
    // Objetos
    skuProducto: PropTypes.string.isRequired,
    idArticuloSeleccionado: PropTypes.number.isRequired,
    // Metodos
    agregarBarra: PropTypes.func.isRequired
}