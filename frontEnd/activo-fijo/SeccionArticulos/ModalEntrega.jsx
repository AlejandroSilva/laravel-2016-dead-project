// Librerias
import React from 'react'
import _ from 'lodash'
let PropTypes = React.PropTypes
// Componentes
import Modal from 'react-bootstrap/lib/Modal.js'
import { TablaEntrega } from './TablasArticulos.jsx'
import { InputBarra } from '../../modulos-logistica/shared/InputBarra.jsx'
// Styles
import * as css from './seccionArticulos.css'

export class ModalEntrega extends React.Component {
    constructor(props) {
        super(props)
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
    render(){
        return (
            <Modal
                show={this.state.modalVisible}
                //onEnter={this.props.onEnter}
                onHide={this.hideModal}
                animation={false}
                dialogClassName={css.modalEntrega}
            >
                <Modal.Header closeButton>
                    <Modal.Title>Entregar articulos</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    {this.props.children}
                </Modal.Body>
            </Modal>
        )
    }
}


export class EntregaArticulos extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            idAlmacenDestino: this.props.almacenDestino,
            articulos: [],
            // todo: el scroll no esta funcionando bien, se mueve al "penultimo elemento"
            articulo_scrollToRow: null
        }
        this.seleccionarAlmacenDestino = (evt)=>{
            this.setState({idAlmacenDestino: evt.target.value})
        }
        this.escanearProducto = (codigo, errorCallback)=>{
            this.props.buscarBarra(codigo)
                .then(articulos=>{
                    let articulo = articulos[0]
                    // articulo es {} cuando no se encuentra, entonces se revisa que tenga la propiedad codArt, y
                    // se agrega solo si no existe en el arreglo
                    if(!articulo || !articulo.idArticuloAF)
                        return errorCallback('No encontrado en maestra')

                    if(articulo.idArticuloAF!=undefined && _.find(this.state.articulos, {idArticuloAF: articulo.idArticuloAF})==undefined){
                        // se transforma el archivo cuando se recibe, para manejar un objeto mas simple
                        let _stockDisponible = articulo.existencias.reduce((exA, exB)=>{
                            // se obtiene el total de existencias del articulo en el almacen disponible
                            return exA + (exB.idAlmacenAF==1? exB.stockActual : 0)
                        }, 0)
                        let articuloDisponible = {
                            idArticuloAF: articulo.idArticuloAF,
                            SKU: articulo.SKU,
                            barras: articulo.barras,
                            descripcion: articulo.descripcion,
                            stockDisponible: _stockDisponible,
                            stockSeleccionado: _stockDisponible>0? 1 : 0    // si existen articulos disponibles, por defecto seleccionar 1
                        }
                        console.log(articuloDisponible)
                        // se agrega el articulo al final del array
                        let articulosActualizado = [...this.state.articulos, articuloDisponible]
                        console.log(articulosActualizado, articulosActualizado.length-1)
                        this.setState({
                            articulos: articulosActualizado,
                            articulo_scrollToRow: articulosActualizado.length-1
                        })
                    }
                })
                .catch(er=>{
                    console.log(er)
                })
        }
        this.cambiarCantidad = (idArticulo, evt)=>{
            this.setState({
                articulos: this.state.articulos.map(articulo=>{
                    // se busca el articulo y se actualiza el stockSeleccionado
                    if(articulo.idArticuloAF==idArticulo)
                        return Object.assign({}, articulo, {stockSeleccionado: evt.target.value})
                    return articulo
                })
            })
        }
        this._realizarEntrega = ()=>{
            // se realiza la entrega de todos los articulos seleccionados
            this.props.realizarEntrega({
                articulos: this.state.articulos.map(producto=>({
                    'idArticuloAF': producto.idArticuloAF,
                    'stockPorEntregar': producto.stockSeleccionado
                })),
                almacenDestino: this.state.idAlmacenDestino
            })
                .then(resp=>{
                    this.props.hideModal()
                })
        }
    }
    componentDidMount(){
        this.refInputBarra.focus()
    }

    render() {
        let almacenSeleccionado = this.state.idAlmacenDestino!="0"
        let unoOmasProductosSeleccionados = this.state.articulos.length>0

        return (
            <div className="form-horizontal">
                {/* Hacia */}
                <div className="form-group">
                    <label className="col-xs-3">Entregar a:</label>
                    <div className="col-xs-9">
                        <select className="form-control"
                                value={this.state.idAlmacenDestino}
                                onChange={this.seleccionarAlmacenDestino}
                                disabled
                        >
                            {this.props.almacenes.map(almacen=>
                                <option key={almacen.idAlmacenAF} value={almacen.idAlmacenAF}>{almacen.nombre}</option>
                            )}
                        </select>
                    </div>
                </div>

                {/* Productos */}
                <div>
                    <TablaEntrega
                        scrollToRow={this.state.articulo_scrollToRow}
                        articulos={this.state.articulos}
                        cambiarCantidad={this.cambiarCantidad}
                    />
                </div>

                {/* Escanear */}
                <div className="form-group">
                    <label className="col-xs-2">Código</label>
                    <div className="col-xs-10">
                        <InputBarra
                            className="form-control"
                            onScan={this.escanearProducto}
                            ref={ref=>this.refInputBarra=ref}
                        />
                    </div>
                </div>

                {/* Botones Cancelar/Siguiente */}
                <div className="btn-group btn-group-justified">
                    <button type='button' className="btn btn-default" style={{width: '50%'}}
                        // onClick={this.props.onCancel}>
                            onClick={this.props.hideModal}>
                        Cancelar
                    </button>
                    <button type='button' className="btn btn-primary" style={{width: '50%'}}
                        // onClick={this.props.onCancel}>
                            disabled={ !almacenSeleccionado || !unoOmasProductosSeleccionados}
                            onClick={this._realizarEntrega}>
                        Siguiente
                    </button>
                </div>
            </div>
        )
    }
}
// se debe definir el contextTypes o de lo contrario no recibe el context del padre
EntregaArticulos.propTypes = {
    // metodos
    hideModal: PropTypes.func.isRequired,
    realizarEntrega: PropTypes.func.isRequired,
    buscarBarra: PropTypes.func.isRequired,
    // objetos
    almacenes: PropTypes.arrayOf(PropTypes.object).isRequired,
    almacenDestino: PropTypes.number.isRequired,
}