// Librerias
import React from 'react'
import _ from 'lodash'
let PropTypes = React.PropTypes
// Componentes
import Modal from 'react-bootstrap/lib/Modal.js'
import { TablaTransferencia } from './TablasArticulos.jsx'
import { InputBarra } from '../../modulos-logistica/shared/InputBarra.jsx'
// Styles
import * as css from './seccionArticulos.css'

export class ModalTransferencia extends React.Component {
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
                dialogClassName={css.modalTransferencia}
            >
                <Modal.Header closeButton>
                    <Modal.Title>Transferir articulos</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    {this.props.children}
                </Modal.Body>
            </Modal>
        )
    }
}


export class TransferenciaArticulos extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            idAlmacenOrigen: this.props.almacenOrigen,//this.props.almacenes[0]? this.props.almacenes[0].idAlmacenAF : 0,
            idAlmacenDestino: this.props.almacenes[0]? this.props.almacenes[0].idAlmacenAF : 0,
            articulos: []
        }
        // puede transferir a todos los almacenes, excepto a el mismo y a Disponible (se debe hacer un "retorno" para esto)
        this.almacenesParaTransferir = this.props.almacenes.filter(alm=>
            alm.idAlmacenAF!=1 && alm.idAlmacenAF!=this.props.almacenOrigen
        )

        // Metodos
        this.seleccionarAlmacenOrigen = (evt)=>{
            this.setState({idAlmacenOrigen: evt.target.value})
        }
        this.seleccionarAlmacenDestino = (evt)=>{
            this.setState({idAlmacenDestino: evt.target.value})
        }

        this.escanearProducto = (codigo, errorCallback)=>{
            this.props.buscarBarra(codigo)
                .then(articulos=>{
                    let articulo = articulos[0]
                    // articulo es {} cuando no se encuentra, el articulo se encontro si tiene la propiedad idArticuloAF
                    if(!articulo || !articulo.idArticuloAF)
                        return errorCallback('No encontrado en maestra')

                    let existencia = _.find(articulo.existencias, {idAlmacenAF: this.state.idAlmacenOrigen})
                    // si el producto existe, y el almacen de origen tiene stock asociado, entonces se agrega a los articulos
                    if(existencia){
                        let stockEnOrigen = existencia.stockActual
                        console.log('stock en origen', stockEnOrigen)

                        let articuloYaAgregado = _.find(this.state.articulos, {idArticuloAF: articulo.idArticuloAF})
                        if(articuloYaAgregado){
                            // TODO si se escanea un producto que ya estaba, se incrementa su stockATransferir
                            console.error('pendiente el aumento de stockATransferir')
                        }
                        else{
                            // si no existe en la lista de seleccionados, se agrega con stockATransferir=1
                            this.setState({
                                articulos: [...this.state.articulos, {
                                    sku: articulo.sku,
                                    descripcion: articulo.descripcion,
                                    idArticuloAF: articulo.idArticuloAF,
                                    barras: articulo.barras,
                                    stockATransferir: 1,
                                    stockEnOrigen: articulo.stock
                                }]
                            })
                        }
                    }else{
                        return errorCallback('Sin stock en almacén de origen')
                    }
                })
                // .catch(er=>{
                //     console.log(er)
                // })
        }
        this.cambiarStockATransferir = (idArticuloAF, evt)=>{
            console.log('sock a transferir', idArticuloAF, evt)
        }
        // this.quitarProducto = (codArt)=>{
        //     this.setState({
        //         articulos: _.remove(this.state.articulos, articulo=>articulo.codArt!=codArt)
        //     })
        // }
        this._realizarTransferencia = ()=>{
            this.props.realizarTransferencia({
                almacenOrigen: this.state.idAlmacenOrigen,
                almacenDestino: this.state.idAlmacenDestino,
                // TODO: no enviar todos los datos, solo lo que le interesa al backend
                articulos: this.state.articulos //this.state.articulos.map(producto=>producto.codArt)
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
        let almacenesSeleccionados = this.state.idAlmacenOrigen!="0" && this.state.idAlmacenDestino!="0"
        // se puede seguir solo si han sido seleccionado dos almacenes diferentes
        let almacenesDistintos = this.state.idAlmacenOrigen!=this.state.idAlmacenDestino
        // el origen es valido, siempre que sea igual al origen seleccionado
        let articulosConOrigenValido = this.state.articulos.every(producto=>producto.idAlmacen==this.state.idAlmacenOrigen)
        let unoOmasProductosSeleccionados = this.state.articulos.length>0
        return (
            <div className="form-horizontal">
                {/* Desde */}
                <div className="form-group">
                    <label className="col-xs-3">Tomar de:</label>
                    <div className="col-xs-9">
                        <select className="form-control"
                                value={this.state.idAlmacenOrigen}
                                onChange={this.seleccionarAlmacenOrigen}
                                disabled
                        >
                            {this.props.almacenes.map(almacen=>
                                <option key={almacen.idAlmacenAF} value={almacen.idAlmacenAF}>{almacen.nombre}</option>
                            )}
                        </select>
                    </div>
                </div>

                {/* Hacia */}
                <div className="form-group">
                    <label className="col-xs-3">Entregar a:</label>
                    <div className="col-xs-9">
                        <select className="form-control"
                                value={this.state.idAlmacenDestino}
                                onChange={this.seleccionarAlmacenDestino}
                        >
                            {this.almacenesParaTransferir.map(almacen=>
                                <option key={almacen.idAlmacenAF} value={almacen.idAlmacenAF}>{almacen.nombre}</option>
                            )}
                        </select>
                    </div>
                </div>

                {/* Productos */}
                <div>
                    <TablaTransferencia
                        articulos={this.state.articulos}
                        //quitarProducto={this.quitarProducto}
                        cambiarStockATransferir={this.cambiarStockATransferir}
                    />
                </div>
                {/* Escanear */}
                <div className="form-group">
                    <label className="col-xs-2">Código</label>
                    <div className="col-xs-10">
                        <InputBarra
                            ref={ref=>this.refInputBarra=ref}
                            className="form-control"
                            onScan={this.escanearProducto}
                            errorMessage={this.state.errorInputBarra}
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
                            disabled={ !almacenesSeleccionados || !almacenesDistintos || !articulosConOrigenValido || !unoOmasProductosSeleccionados}
                            onClick={this._realizarTransferencia}>
                        Siguiente
                    </button>
                </div>
            </div>
        )
    }
}
// se debe definir el contextTypes o de lo contrario no recibe el context del padre
TransferenciaArticulos.contextTypes = {
    $_showModal: React.PropTypes.func,
}
TransferenciaArticulos.propTypes = {
    // metodos
    hideModal: PropTypes.func.isRequired,
    buscarBarra: PropTypes.func.isRequired,
    realizarTransferencia: PropTypes.func.isRequired,

    // objetos
    almacenOrigen: PropTypes.number.isRequired,
    almacenes: PropTypes.arrayOf(PropTypes.object).isRequired
}