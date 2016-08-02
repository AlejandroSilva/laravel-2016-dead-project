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
import { ModalAgregarProducto } from './ModalAgregarProducto.jsx'
import { ModalAgregarArticulo } from './ModalAgregarArticulo.jsx'
import { ModalConfirmacion } from '../../../shared/ModalConfirmacion.jsx'

// Styles
import * as cssModal from '../modal.css'

export class ModalMantenedorMaestra extends React.Component {
    constructor(props) {
        super(props)
        const defaultState = {
            modalVisible: false,
            // TablaProductos
            productos: [],
            productoSkuSeleccionado: '',
            producto_scrollToRow: 0,
            // TablaArticulos
            articulos: [],
            idArticuloSeleccionado: 0,
            articulo_scrollToRow: 0,
            // TablaBarras
            barras: [],
            barraSeleccionada: '',
            barar_scrollToRow: 0,
        }
        this.state = defaultState
        this.showModal = ()=>{
            // dejar el state por defecto de la app
            this.setState({
                modalVisible: true,
                ...defaultState
            })
            // buscar los productos
            this.buscarProductos()
        }
        this.hideModal = ()=>{
            this.setState({
                modalVisible: false
            })
        }

        // FETCH
        this.buscarProductos = (onComplete=()=>{}) =>{
            // onComplete es un callback opcional, que se ejecuta luego de actualizar la lista de productos
            api.activoFijo.productos.fetch()
                .then(productos=>{
                    this.setState({
                        modalVisible: true,
                        productos
                    }, onComplete)
                })
        }
        this.buscarArticulosDeProducto = (onComplete=()=>{}) =>{
            // buscar los articulos que tenga el producto
            api.activoFijo.producto(this.state.productoSkuSeleccionado).articulos()
                .then(articulos=>{
                    this.setState({articulos}, onComplete)
                })
        }

        // Productos: Tabla Principal
        this.seleccionarProducto = (skuProducto, rowIndex)=>{
            this.setState({
                // hacer scroll hasta el producto
                productoSkuSeleccionado: skuProducto,
                producto_scrollToRow: rowIndex,
                // al seleccionar un producto, se deja de des-selecciona el ultimo articulo tomado
                idArticuloSeleccionado: 0,
                articulo_scrollToRow: 0,
                // al seleccionar un producto, se deja de des-selecciona la ultima barra tomada
                barraSeleccionada: '',
                barar_scrollToRow: 0,
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
                .catch(err=>{
                    let msgs = _.values(err.data).join('. ')
                    console.error("Error al agregar un producto ", msgs)
                    this.refNotify.error("Error al agregar un producto", msgs, 4*1000);
                })
        }

        // Productos: Modal Agregar Producto
        this.showModalAgregarProducto = ()=>{
            this.refModalAgregarProducto.showModal()
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

        // Producto: Modal Eliminar Producto
        this.showModalEliminarProducto = ()=>{
            this.refModalEliminarProducto.showModal()
        }
        this.hideModalEliminarProducto = ()=>{
            this.refModalEliminarProducto.hideModal()
        }
        this.eliminarProductoSeleccionado = ()=>{
            return api.activoFijo.producto(this.state.productoSkuSeleccionado).eliminar()
                .then(resp=>{
                    // cuando se elimine el producto, se oculta el modal
                    this.hideModalEliminarProducto()
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
                    this.hideModalEliminarProducto()

                    let msgs = _.values(err.data).join('. ')
                    console.error("Error al eliminar producto ", msgs)
                    this.refNotify.error("Error al eliminar producto", msgs, 4*1000);
                })
        }

        // Articulos: Tabla Principal
        this.seleccionarArticulo = (articulo, rowIndex)=>{
            this.setState({
                idArticuloSeleccionado: articulo.idArticuloAF,
                articulo_scrollToRow: rowIndex,
                barras: articulo.barras,
                // al seleccionar un producto, se deja de des-selecciona la ultima barra tomada
                barraSeleccionada: '',
                barar_scrollToRow: 0,
            })
        }
        this.actualizarArticulo = (idArticuloAF, datos)=>{
            api.activoFijo.articulo(idArticuloAF).actualizar(datos)
                .then(articuloActualizado=>{
                    // todo: se actualiza un articulo, pero se vuelve a bajar todos los datos, deberia actualizarse solo un elemento
                    this.buscarArticulosDeProducto()
                })
                .catch(err=>{
                    let msgs = _.values(err.data).join('. ')
                    console.error("Error al eliminar producto ", msgs)
                    this.refNotify.error("Error al eliminar producto", msgs, 4*1000);
                })
        }

        // Articulos: Modal Agregar Articulo
        this.showModalAgregarArticulo = ()=>{
            this.refModalAgregarArticulo.showModal()
        }
        this.agregarArticulo = (datos)=>{
            let resp = api.activoFijo.articulos.nuevo(datos)
            resp
                .then(resp=>{
                    // actualizar los articulos del producto
                    this.buscarArticulosDeProducto()
                })
            return resp
        }

        // Articulos: Modal Eliminar Articulo
        this.showModalEliminarArticulo = ()=>{
            this.refModalEliminarArticulo.showModal()
        }
        this.hideModalEliminarArticulo = ()=>{
            this.refModalEliminarArticulo.hideModal()
        }
        this.eliminarArticulo = ()=>{
            return api.activoFijo.articulo(this.state.idArticuloSeleccionado).eliminar()
                .then(resp=>{
                    // cuando se elimine el producto, se oculta el modal
                    this.hideModalEliminarArticulo()
                    // una vez eliminado, se descarga la lista de articulos del producto nuevamente
                    this.buscarArticulosDeProducto( ()=>{
                        // "articulo_scrollToRow" tiene el index del elemento seleccionado para eliminar, luego de
                        // ser elinado, se puede seleccionar la misma posicion
                        this.setState({
                            //articulo_scrollToRow: this.state.articulo_scrollToRow,
                            idArticuloSeleccionado: 0
                        })
                    })
                })
                .catch(err=>{
                    let msgs = _.values(err.data).join('. ')
                    console.error("Error al eliminar producto ", msgs)
                    this.refNotify.error("Error al eliminar producto", msgs, 4*1000);
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

                    {/* === PRODUCTOS === */}
                    <TablaProductos
                        productos={this.state.productos}
                        skuSeleccionado={this.state.productoSkuSeleccionado}
                        scrollToRow={this.state.producto_scrollToRow}
                        // permisos producto
                        puedeModificarProducto={this.props.puedeModificarProducto}
                        // Metodos
                        actualizarProducto={this.actualizarProducto}
                        seleccionarProducto={this.seleccionarProducto}
                    >
                        <div className="pull-right">
                            <button className="btn btn-xs btn-default"
                                    onClick={this.showModalAgregarProducto}
                                    // debe tener los permisos
                                    disabled={!this.props.puedeAgregarProducto}>
                                Agregar
                            </button>
                            <button className="btn btn-xs btn-default"
                                    onClick={this.showModalEliminarProducto}
                                    // debe tener los permisos y debe haber un producto seleccionado
                                    disabled={!this.props.puedeEliminarProducto || this.state.productoSkuSeleccionado==''}>
                                Eliminar
                            </button>
                        </div>

                    </TablaProductos>

                    {/* === ARTICULOS === */}
                    <TablaArticulos
                        articulos={this.state.articulos}
                        idArticuloSeleccionado={this.state.idArticuloSeleccionado}
                        scrollToRow={this.state.articulo_scrollToRow}
                        // permisos articulos
                        puedeAgregarArticulo={this.props.puedeAgregarArticulo}
                        puedeModificarArticulo={/*this.props.puedeModificarArticulo*/ true}
                        puedeEliminarArticulo={/*this.props.puedeEliminarArticulo*/ true}

                        // Metodos
                        actualizarArticulo={this.actualizarArticulo}
                        seleccionarArticulo={this.seleccionarArticulo}
                        eliminarArticulo={this.eliminarArticulo}
                    >
                        <div className="pull-right">
                            <button className="btn btn-xs btn-default"
                                    // debe tener los permisos, y debe haber un producto seleccionado
                                    disabled={!this.props.puedeAgregarArticulo || this.state.productoSkuSeleccionado==''}
                                    onClick={this.showModalAgregarArticulo}
                            >
                                Agregar
                            </button>
                            <button className="btn btn-xs btn-default"
                                    // debe tener los permisos y debe haber un articulo seleccionado
                                    disabled={!this.props.puedeEliminarArticulo || this.state.idArticuloSeleccionado==0}
                                    onClick={this.showModalEliminarArticulo}
                            >
                                Eliminar
                            </button>
                        </div>
                    </TablaArticulos>

                    {/* === BARRAS === */}
                    <TablaBarras
                        barras={this.state.barras}
                        barraSeleccionada={this.state.barraSeleccionada}
                        // permisos
                        puedeModificar={true}
                        // metodos
                        seleccionarBarra={this.seleccionarBarra}
                    />


                    {/* === MODALES === */}
                    {/* Agregar Producto*/}
                    <ModalAgregarProducto
                        ref={ref=>this.refModalAgregarProducto=ref}
                        agregarProducto={this.agregarProducto}
                    />
                    {/* Eliminar Producto */}
                    <ModalConfirmacion
                        ref={ref=>this.refModalEliminarProducto=ref}
                        textModalHeader="¿Seguro que desea eliminar el Producto?"
                        //textDescription="lkjasldkj"
                        textCancel="Cancelar"
                        textAccept="Eliminar"
                        acceptClassname="btn-danger"
                        // Metodos
                        onAccept={this.eliminarProductoSeleccionado}
                        onCancel={this.hideModalEliminarProducto}
                    />
                    {/* Agregar Articulo */}
                    <ModalAgregarArticulo
                        ref={ref=>this.refModalAgregarArticulo=ref}
                        agregarArticulo={this.agregarArticulo}
                        skuProducto={this.state.productoSkuSeleccionado}
                    />
                    {/* Eliminar Articulo */}
                    <ModalConfirmacion
                        ref={ref=>this.refModalEliminarArticulo=ref}
                        textModalHeader="¿Seguro que desea eliminar el Articulo?"
                        //textDescription="lkjasldkj"
                        textCancel="Cancelar"
                        textAccept="Eliminar"
                        acceptClassname="btn-danger"
                        // Metodos
                        onAccept={this.eliminarArticulo}
                        onCancel={this.hideModalEliminarArticulo}
                    />
                </Modal.Body>
            </Modal>
        )
    }
}
ModalMantenedorMaestra.propTypes = {
    // Permisos productos
    puedeAgregarProducto: PropTypes.bool.isRequired,
    puedeModificarProducto: PropTypes.bool.isRequired,
    puedeEliminarProducto: PropTypes.bool.isRequired,
    // Permisos Articulos
    puedeAgregarArticulo: PropTypes.bool.isRequired,
    puedeModificarArticulo: PropTypes.bool.isRequired,
    puedeEliminarArticulo: PropTypes.bool.isRequired
}