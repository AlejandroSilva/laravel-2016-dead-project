// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import Modal from 'react-bootstrap/lib/Modal.js'
// Estilos
import classNames from 'classnames/bind'
import * as css from './modales.css'
let cx = classNames.bind(css)

export class ModalAgregarProducto extends React.Component{
    constructor(props){
        super(props)
        const productoEnBlanco = {
            SKU: '',
            descripcion: '',
            valorMercado: ''
        }
        this.state = {
            showModalAgregarProducto: false,
            producto: productoEnBlanco,
            errors: {}
        }
        // Modal
        this.showModal = ()=>
            this.setState({
                showModalAgregarProducto: true,
                producto: productoEnBlanco,
                errors: {}
            })
        this.cancelarModal = ()=>
            this.setState({ showModalAgregarProducto: false})

        this.changeSKU = (evt)=>
            this.setState({producto: Object.assign({}, this.state.producto, { SKU: evt.target.value})})
        this.changeDescripcion = (evt)=>
            this.setState({producto: Object.assign({}, this.state.producto, { descripcion: evt.target.value})})
        this.changeValorMercado = (evt)=>
            this.setState({producto: Object.assign({}, this.state.producto, { valorMercado: evt.target.value})})


        this.agregarProducto = (evt)=>{
            evt.preventDefault()

            // limpiar los errores
            this.setState({errors: {} })
            // agregar el producto, quitar los mensajes de error, y dejar el formulario en blanco
            this.props.agregarProducto(this.state.producto)
                .then(()=>{
                    console.log('agregar ok')
                    // al terminar, se oculta el modal
                    this.cancelarModal()
                    // this.setState({
                    //     producto: {SKU:'', descripcion:'', valorMercado:''},
                    //     errors: {}
                    // })
                })
                .catch(error=>{
                    if(error.status==400)
                        this.setState({errors: error.data})
                })
        }

    }
    render() {
        return (
            <div>
                <Modal
                    show={this.state.showModalAgregarProducto}
                    onHide={this.cancelarModal}
                    container={this}
                    dialogClassName={cx('modal-agregar-producto')}
                >
                    <Modal.Header closeButton>
                        <Modal.Title id="contained-modal-title">Agregar Producto</Modal.Title>
                    </Modal.Header>
                    <Modal.Body className={cx('modal-body')} >


                        <form onSubmit={this.agregarProducto}>
                            <div className="form-group">
                                <label>SKU</label>
                                <input className="form-control" type="text" placeholder="SKU"
                                       value={this.state.producto.SKU}
                                       onChange={this.changeSKU}
                                />
                                <p className={cx('error-msg')}>{this.state.errors.SKU} &nbsp;</p>
                            </div>

                            <div className="form-group">
                                <label>Descripción</label>
                                <input className="form-control" type="text" placeholder="Descripción"
                                       value={this.state.producto.descripcion}
                                       onChange={this.changeDescripcion}
                                />
                                <p className={cx('error-msg')}>{this.state.errors.descripcion} &nbsp;</p>
                            </div>

                            <div className="form-group">
                                <label>Valor Mercado</label>
                                <input className="form-control" type="number" placeholder="Valor mercado"
                                       value={this.state.producto.valorMercado}
                                       onChange={this.changeValorMercado}
                                />
                                <p className={cx('error-msg')}>{this.state.errors.valorMercado} &nbsp;</p>
                            </div>
                        </form>

                    </Modal.Body>
                    <Modal.Footer className={cx('modal-footer')} >
                        <button className={cx('btn btn-block btn-default')} onClick={this.cancelarModal}>Cancelar</button>
                        <button className={cx('btn btn-block btn-primary')} onClick={this.agregarProducto}>Agregar</button>
                    </Modal.Footer>
                </Modal>
            </div>
        )
    }
}
ModalAgregarProducto.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
    //puedeAgregar: PropTypes.object.isRequired,
    agregarProducto: PropTypes.func.isRequired
}