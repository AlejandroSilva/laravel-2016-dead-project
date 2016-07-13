// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import Modal from 'react-bootstrap/lib/Modal.js'
import { Table, Column, Cell } from 'fixed-data-table'
import { TablaProductos } from './TablaProductos.jsx'
import { FormNuevoProducto } from './FormNuevoProducto.jsx'
// Styles
import * as cssModal from '../modal.css'

export class ModalMantenedorMaestra extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            modalVisible: false,
            productos: []
        }
        this.showModal = ()=>{
            this.buscarProductos()
        }
        this.hideModal = ()=>{
            this.setState({
                modalVisible: false
            })
        }
        this.buscarProductos = ()=>{
            this.props.fetchProductos()
                .then(productos=>{
                    this.setState({
                        modalVisible: true,
                        productos
                    })
                })
        }
        this.actualizarProducto = (skuProducto, data)=>{
            // todo cambiar
            //console.log('actualizar producto ', skuProducto, data)
            this.props.actualizarProducto(skuProducto, data)
                .then(prodActualizado=>{
                    // todo: actualizar producto... esto es malo para el rendimiento, pero rapido de prototipar
                    this.buscarProductos()
                })
        }
        this.agregarProducto = (data)=>{
            return this.props.agregarProducto(data)
                .then(nuevoProd=>{
                    console.log('nuevo: ', nuevoProd)
                    // todo: agregar producto... esto es malo para el rendimiento, pero rapido de prototipar
                    this.buscarProductos()
                })
        }
    }

    render(){
        return (
            <Modal
                show={this.state.modalVisible}
                onHide={this.hideModal}
                animation={false}
                dialogClassName={cssModal.modalMantenedorProductos}>
                <Modal.Header closeButton>
                    <Modal.Title>Maestra de Productos</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    
                    <TablaProductos
                        productos={this.state.productos}
                        actualizarProducto={this.actualizarProducto}
                        // permisos
                        puedeModificar={true}
                    />

                    <FormNuevoProducto
                        agregarProducto={this.agregarProducto}
                        // permisos
                        puedeAgregar={true}
                    />
                </Modal.Body>
            </Modal>
        )
    }
}

ModalMantenedorMaestra.propTypes = {
    // Llamadas al API
    fetchProductos: PropTypes.func.isRequired,
    actualizarProducto: PropTypes.func.isRequired,
    agregarProducto: PropTypes.func.isRequired
}