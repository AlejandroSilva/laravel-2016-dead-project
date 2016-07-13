// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import { InputTexto } from '../../shared/InputTexto.jsx'
// Styles
import classNames from 'classnames/bind'
import * as css from './formNuevoProducto.css'
let cx = classNames.bind(css)

export class FormNuevoProducto extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            producto: {
                SKU: '',
                descripcion: '',
                valorMercado: ''
            },
            errors: {}
        }
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
                    this.setState({
                        producto: {SKU:'', descripcion:'', valorMercado:''},
                        errors: {}
                    })
                })
                .catch(error=>{
                    if(error.status==400)
                        this.setState({errors: error.data})
                })
        }
    }
    render(){
        return (
            <form className={cx('formulario-nuevo-producto')}
                  onSubmit={this.agregarProducto}
            >
                <div className={cx('column-sku')}>
                    <input type="text" placeholder="SKU"
                           value={this.state.producto.SKU}
                           onChange={this.changeSKU}
                    />
                    <p className={cx('error-msg')}>{this.state.errors.SKU} &nbsp;</p>
                </div>
                <div className={cx('column-descripcion')}>
                    <input type="text" placeholder="Descripción"
                           value={this.state.producto.descripcion}
                           onChange={this.changeDescripcion}
                    />
                    <p className={cx('error-msg')}>{this.state.errors.descripcion} &nbsp;</p>
                </div>
                <div className={cx('column-valor-mercado')}>
                    <input type="number" placeholder="Valor mercado"
                           value={this.state.producto.valorMercado}
                           onChange={this.changeValorMercado}
                    />
                    <p className={cx('error-msg')}>{this.state.errors.valorMercado} &nbsp;</p>
                </div>
                <div className={cx('column-botones')}>
                    <button type="submit" className="btn btn-sm btn-primary">
                        Agregar producto
                    </button>
                    &nbsp;
                </div>
            </form>
        )
    }
}

FormNuevoProducto.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
    // Permisos
    puedeAgregar: PropTypes.bool.isRequired
}