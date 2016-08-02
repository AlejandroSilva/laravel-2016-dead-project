// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import Modal from 'react-bootstrap/lib/Modal.js'
// Estilos
import classNames from 'classnames/bind'
import * as css from './modales.css'
let cx = classNames.bind(css)

export class ModalAgregarArticulo extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            modalVisible: false,
            articulo: {
                stock: 0
            },
            errors: {}
        }

        // Modal
        this.showModal = ()=>{
            this.setState({
                modalVisible: true,
                articulo: {
                    stock: 0
                },
                errors: {}
            })
        }
        this.hideModal = ()=>{
            this.setState({ modalVisible: false})
        }
        this.changeStock = (evt)=>{
            this.setState({
                articulo: {
                    stock: evt.target.value
                },
                errors: {}
            })
        }
        this.agregarArticulo = (evt)=>{
            evt.preventDefault()

            // limpiar los errores
            this.setState({errors: {} })
            // agregar el articulo, quitar los mensajes de error, y dejar el formulario en blanco
            this.props.agregarArticulo({
                SKU: this.props.skuProducto,
                ...this.state.articulo
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
                        <Modal.Title id="contained-modal-title">Agregar Articulo</Modal.Title>
                    </Modal.Header>
                    <Modal.Body className={cx('modal-body')} >

                        <form onSubmit={this.agregarArticulo}>
                            <div className="form-group">
                                <label>SKU de producto</label>
                                <input className="form-control" type="string" value={this.props.skuProducto} disabled/>
                                <p className={cx('error-msg')}>{this.state.errors.SKU} &nbsp;</p>
                            </div>

                            <div className="form-group">
                                <label>Stock</label>
                                <input className="form-control" type="number" placeholder="Stock" min={0}
                                       value={this.state.articulo.stock}
                                       onChange={this.changeStock}
                                />
                                <p className={cx('error-msg')}>{this.state.errors.stock} &nbsp;</p>
                            </div>
                            <p className={cx('error-msg')}>{this.state.errors.error} &nbsp;</p>
                        </form>

                    </Modal.Body>
                    <Modal.Footer className={cx('modal-footer')} >
                        <button className={cx('btn btn-block btn-default')} onClick={this.hideModal}>Cancelar</button>
                        <button className={cx('btn btn-block btn-primary')} onClick={this.agregarArticulo}>Agregar</button>
                    </Modal.Footer>
                </Modal>
            </div>
        )
    }
}
ModalAgregarArticulo.propTypes = {
    // Objetos
    skuProducto: PropTypes.string.isRequired,
    // Metodos
    agregarArticulo: PropTypes.func.isRequired
}