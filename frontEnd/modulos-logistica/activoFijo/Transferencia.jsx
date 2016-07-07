// Librerias
import React from 'react'
import _ from 'lodash'
let PropTypes = React.PropTypes
// Componentes
import { TablaArticulosTransferencia } from './TablaArticulosTransferencia.jsx'
import { InputBarra } from '../shared/InputBarra.jsx'
// Styles

export class Transferencia extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            idAlmacenOrigen: this.props.almacenOrigen,//this.props.almacenes[0]? this.props.almacenes[0].idAlmacenAF : 0,
            idAlmacenDestino: this.props.almacenes[0]? this.props.almacenes[0].idAlmacenAF : 0,
            articulos: []
        }
        // Metodos
        this.seleccionarAlmacenOrigen = (evt)=>{
            this.setState({idAlmacenOrigen: evt.target.value})
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
        this.quitarProducto = (idProducto)=>{
            this.setState({
                articulos: _.remove(this.state.articulos, producto=>producto.id!=idProducto)
            })
        }
        this._realizarTransferencia = ()=>{
            let codigosArticulos = this.state.articulos.map(producto=>producto.codArt)
            // se realiza la transferencia de todos los articulos seleccionados
            this.props.realizarTransferenia({
                codigosArticulos,
                almacenDestino: this.state.idAlmacenDestino
            })
            .then(resp=>{
                this.context.$_hideModal()
            })
        }
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
                    <label className="col-xs-2">Desde</label>
                    <div className="col-xs-10">
                        <select className="form-control"
                                value={this.state.idAlmacenOrigen}
                                onChange={this.seleccionarAlmacenOrigen}
                        >
                            <option value="0" disabled>--</option>
                            {this.props.almacenes.map(almacen=>
                                <option key={almacen.idAlmacenAF} value={almacen.idAlmacenAF}>{almacen.nombre}</option>
                            )}
                        </select>
                    </div>
                </div>

                {/* Hacia */}
                <div className="form-group">
                    <label className="col-xs-2">Hacia</label>
                    <div className="col-xs-10">
                        <select className="form-control"
                                value={this.state.idAlmacenDestino}
                                onChange={this.seleccionarAlmacenDestino}
                        >
                            <option value="0" disabled>--</option>
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
                <div>
                    <button className="btn btn-default btn-block"
                            // onClick={this.props.onCancel}>
                            onClick={this.context.$_hideModal}>
                        Cancelar
                    </button>
                    <button className="btn btn-primary btn-block"
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
Transferencia.contextTypes = {
    $_showModal: React.PropTypes.func,
    $_hideModal: React.PropTypes.func
}
Transferencia.propTypes = {
//     numero: PropTypes.number.isRequired,
//     texto: PropTypes.string.isRequired,
//     objecto: PropTypes.object.isRequired,
    almacenOrigen: PropTypes.number.isRequired,
    almacenes: PropTypes.arrayOf(PropTypes.object).isRequired
}