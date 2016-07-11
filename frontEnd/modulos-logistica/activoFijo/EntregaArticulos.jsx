// Librerias
import React from 'react'
import _ from 'lodash'
let PropTypes = React.PropTypes
// Componentes
import { TablaArticulosTransferencia } from './TablaArticulosTransferencia.jsx'
import { InputBarra } from '../shared/InputBarra.jsx'
// Styles

export class EntregaArticulos extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            idAlmacenDestino: this.props.almacenDestino,
            articulos: []
        }
        this.seleccionarAlmacenDestino = (evt)=>{
            this.setState({idAlmacenDestino: evt.target.value})
        }
        this.escanearProducto = (codigo)=>{
            this.props.buscarBarra(codigo)
                .then(articulo=>{
                    // articulo es {} cuando no se encuentra, entonces se revisa que tenga la propiedad codArt, y
                    // se agrega solo si no existe en el arreglo
                    if(articulo.codArt!=undefined && _.find(this.state.articulos, {codArt: articulo.codArt})==undefined){
                        this.setState({
                            articulos: [...this.state.articulos, articulo]
                        })
                    }
                })
                .catch(er=>{
                    console.log(er)
                })
        }
        this.quitarProducto = (codArt)=>{
            this.setState({
                articulos: _.remove(this.state.articulos, articulo=>articulo.codArt!=codArt)
            })
        }
        this._realizarEntrega = ()=>{
            let codigosArticulos = this.state.articulos.map(producto=>producto.codArt)
            // se realiza la entrega de todos los articulos seleccionados
            this.props.realizarEntrega({
                codigosArticulos,
                almacenDestino: this.state.idAlmacenDestino
            })
                .then(resp=>{
                    this.context.$_hideModal()
                })
        }
    }

    render() {
        let almacenSeleccionado = this.state.idAlmacenDestino!="0"
        // el origen es valido, siempre que sea igual a "disponible"
        let articulosDisponibles = this.state.articulos.every(producto=>producto.idAlmacen==1)
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
                    <TablaArticulosTransferencia
                        articulos={this.state.articulos}
                        quitarProducto={this.quitarProducto}
                    />
                </div>
                {/* Escanear */}
                <div className="form-group">
                    <label className="col-xs-2">Código</label>
                    <div className="col-xs-10">
                        <InputBarra
                            className="form-control"
                            onScan={this.escanearProducto}
                        />
                    </div>
                </div>

                {/* Botones Cancelar/Siguiente */}
                <div className="btn-group btn-group-justified">
                    <button type='button' className="btn btn-default" style={{width: '50%'}}
                        // onClick={this.props.onCancel}>
                            onClick={this.context.$_hideModal}>
                        Cancelar
                    </button>
                    <button type='button' className="btn btn-primary" style={{width: '50%'}}
                        // onClick={this.props.onCancel}>
                            disabled={ !almacenSeleccionado || !articulosDisponibles || !unoOmasProductosSeleccionados}
                            onClick={this._realizarEntrega}>
                        Siguiente
                    </button>
                </div>
            </div>
        )
    }
}
// se debe definir el contextTypes o de lo contrario no recibe el context del padre
EntregaArticulos.contextTypes = {
    $_showModal: React.PropTypes.func,
    $_hideModal: React.PropTypes.func
}
EntregaArticulos.propTypes = {
//     numero: PropTypes.number.isRequired,
//     texto: PropTypes.string.isRequired,
//     objecto: PropTypes.object.isRequired,
    almacenes: PropTypes.arrayOf(PropTypes.object).isRequired
}