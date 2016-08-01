// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// API
import api from '../../../apiClient/v1'
// Componentes
import ReactNotify from 'react-notify'
import * as ReactNotifyCSS from '../../../shared/ReactNotify/ReactNotify.css'
import Modal from 'react-bootstrap/lib/Modal.js'
import { Table, Column, Cell } from 'fixed-data-table'
import { TablaProductos, TablaArticulos, TablaBarras } from './Tablas.jsx'

// Styles
import * as cssModal from '../modal.css'

export class ModalMantenedorMaestra extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            modalVisible: false,
            // TablaProductos
            productos: [],
            productoSkuSeleccionado: '',
            producto_scrollToRow: 0,
            // TablaArticulos
            articulos: [],
            idArticuloSeleccionado: 0,
            // TablaBarras
            barras: [],
            barraSeleccionada: ''
        }
        this.showModal = ()=>{
            this.buscarProductos()
        }
        this.hideModal = ()=>{
            this.setState({
                modalVisible: false
            })
        }

        this.buscarProductos = ( onComplete=()=>{} )=>{
            // onComplete es un callback opcional, que se ejecuta luego de actualizar la lista de productos
            api.activoFijo.productos.fetch()
                .then(productos=>{
                    this.setState({
                        modalVisible: true,
                        productos
                    }, onComplete)
                })
        }

        // TablaProductos
        this.seleccionarProducto = (skuProducto, rowIndex)=>{
            this.setState({
                productoSkuSeleccionado: skuProducto,
                // hacer scroll hasta el producto
               producto_scrollToRow: rowIndex
            }, ()=>{
                // despues de seleccionar un producto, se descarga la lista actualizada de articulos
                this.buscarArticulosDeProducto()
            })
        }
        this.actualizarProducto = (sku, datos)=>{
            api.activoFijo.producto(sku).actualizar(datos)
                .then(prodActualizado=>{
                    // todo: actualizar producto... esto es malo para el rendimiento, pero rapido de prototipar
                    this.buscarProductos()
                })
        }
        this.agregarProducto = (datos)=>{
            let promise = api.activoFijo.productos.nuevo(datos)
            promise
                .then(nuevoProd=>{
                    console.log('nuevo: ', nuevoProd)
                    // todo: agregar producto... esto es malo para el rendimiento, pero rapido de prototipar
                    // despues de agregar el producto, se descarga la lista completa de productos nuevamente, y se
                    // selecciona (se hace scrollTo tambien) el sku recien agregado
                    this.buscarProductos( ()=>{
                        // se busca en que posicion de la lista quedo el elemento agregado
                        let rowIndex = this.state.productos.findIndex(producto=> producto.SKU==datos.SKU)
                        this.seleccionarProducto(datos.SKU, rowIndex)
                    })
                })
                // .catch(err=>{
                //     let msgs = _.values(err.data).join('. ')
                //     console.error("Error al agregar un producto ", msgs)
                //     this.refNotify.error("Error al agregar un producto", msgs, 4*1000);
                // })
            return promise
        }
        this.eliminarProducto = (sku)=>{
            return api.activoFijo.producto(sku).eliminar()
                .then(resp=>{
                    // una vez eliminado, se descarga la lista de productos nuevamente
                    // y se selecciona "ningun" producto
                    this.buscarProductos( ()=>{
                        // "producto_scrollToRow" tiene el index del elemento seleccionado para eliminar, luego de
                        // ser elinado, se puede seleccionar la misma posicion
                        this.setState({
                            //producto_scrollToRow: this.state.producto_scrollToRow,
                            productoSkuSeleccionado: ''
                        })
                    })
                })
                .catch(err=>{
                    let msgs = _.values(err.data).join('. ')
                    console.error("Error al eliminar producto ", msgs)
                    this.refNotify.error("Error al eliminar producto", msgs, 4*1000);
                })
        }
        this.buscarArticulosDeProducto = ()=>{
            // buscar los articulos que tenga el producto
            api.activoFijo.producto(this.state.productoSkuSeleccionado).articulos()
                .then(articulos=>{
                    this.setState({articulos})
                })
        }

        // Tabla Articulos
        this.seleccionarArticulo = (articulo)=>{
            this.setState({
                idArticuloSeleccionado: articulo.idArticuloAF,
                barras: articulo.barras
            })
        }
        this.actualizarArticulo = (idArticuloAF, datos)=>{
            api.activoFijo.articulo(idArticuloAF).actualizar(datos)
                .then(articuloActualizado=>{
                    // todo: se actualiza un articulo, pero se vuelve a bajar todos los datos, deberia actualizarse solo un elemento
                    this.buscarArticulosDeProducto()
                })
        }

        // Tabla Barras
        this.seleccionarBarra = (barra)=>{
            console.log('barra', barra)
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

                    {/* Notificaciones */}
                    <ReactNotify ref={ref=>this.refNotify=ref} className={ReactNotifyCSS}/>

                    <TablaProductos
                        productos={this.state.productos}
                        skuSeleccionado={this.state.productoSkuSeleccionado}
                        scrollToRow={this.state.producto_scrollToRow}
                        // permisos
                        puedeModificar={true}
                        puedeAgregarProductos={this.props.puedeAgregarProductos}
                        puedeModificarProductos={this.props.puedeModificarProductos}
                        puedeEliminarProductos={this.props.puedeEliminarProductos}
                        // Metodos
                        actualizarProducto={this.actualizarProducto}
                        seleccionarProducto={this.seleccionarProducto}
                        agregarProducto={this.agregarProducto}
                        eliminarProducto={this.eliminarProducto}
                    />
                    <TablaArticulos
                        ref={ref=>this.refTablaArticulos=ref}
                        articulos={this.state.articulos}
                        idArticuloSeleccionado={this.state.idArticuloSeleccionado}
                        // permisos
                        puedeModificar={true}
                        // Metodos
                        actualizarArticulo={this.actualizarArticulo}
                        seleccionarArticulo={this.seleccionarArticulo}
                    />
                    <TablaBarras
                        barras={this.state.barras}
                        barraSeleccionada={this.state.barraSeleccionada}
                        // permisos
                        puedeModificar={true}
                        // metodos
                        seleccionarBarra={this.seleccionarBarra}
                    />
                </Modal.Body>
            </Modal>
        )
    }
}
ModalMantenedorMaestra.propTypes = {
    // Permisos
    puedeAgregarProductos: PropTypes.bool.isRequired,
    puedeModificarProductos: PropTypes.bool.isRequired,
    puedeEliminarProductos: PropTypes.bool.isRequired
}