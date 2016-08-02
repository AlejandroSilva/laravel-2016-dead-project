// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import { Table, Column, Cell } from 'fixed-data-table'
import { InputTexto } from '../../shared/InputTexto.jsx'
import { InputNumber } from '../../shared/InputNumber.jsx'
import TouchExampleWrapper from '../../../shared/TouchExampleWrapper.jsx'
// Styles
import classNames from 'classnames/bind'
import * as css from './tablaProductos.css'
let cx = classNames.bind(css)

/** ########################################## ########################################## **/
export class TablaProductos extends React.Component {
    changeDataProducto(row, colField, inputState){
        // hacer la peticion al servidor solo si ha cambiado y el valor es valido
        if(inputState.dirty && inputState.valid){
            let reqData = {}
            reqData[colField] = inputState.valor
            this.props.actualizarProducto(this.props.productos[row].SKU, reqData)
        }
    }
    render(){
        const {productos, skuSeleccionado, seleccionarProducto} = this.props
        return (
            <div className={cx('tablaProductos')} >
                <TouchExampleWrapper tableWidth={540+20} tableHeight={400}>
                    <Table
                    scrollToRow={this.props.scrollToRow}
                    // Table
                    width={540+20}
                    height={400}
                    // Header
                    headerHeight={30}
                    // Row
                    rowHeight={28}
                    rowsCount={productos.length}>
                        <Column
                            header={<Cell>#</Cell>}
                            cell={ ({rowIndex})=> <Cell>{rowIndex+1}</Cell> }
                            width={30}
                        />
                        <Column
                            header={<Cell>SKU</Cell>}
                            cell={ ({rowIndex})=>
                                <TextoCell editable={false}
                                            texto={productos[rowIndex].SKU}
                                            filaSeleccionada={productos[rowIndex].SKU==skuSeleccionado}
                                            // metodos
                                            onClick={ seleccionarProducto.bind(this, productos[rowIndex].SKU, rowIndex) }
                                            changeData={()=>{}}
                                />}
                            width={80}
                        />
                        <Column
                            header={<Cell>Descripción</Cell>}
                            cell={ ({rowIndex})=>
                                <TextoCell editable={this.props.puedeModificarProducto}
                                           texto={productos[rowIndex].descripcion}
                                           filaSeleccionada={productos[rowIndex].SKU==skuSeleccionado}
                                           // metodos
                                           onClick={ seleccionarProducto.bind(this, productos[rowIndex].SKU, rowIndex) }
                                           changeData={this.changeDataProducto.bind(this, rowIndex, 'descripcion')}
                                />}
                            width={350}
                        />
                        <Column
                            header={<Cell>Valor mercado</Cell>}
                            cell={ ({rowIndex})=>
                                <NumberCell editable={this.props.puedeModificarProducto}
                                               number={productos[rowIndex].valorMercado}
                                               filaSeleccionada={productos[rowIndex].SKU==skuSeleccionado}
                                               // metodos
                                               onClick={ seleccionarProducto.bind(this, productos[rowIndex].SKU, rowIndex) }
                                               changeData={this.changeDataProducto.bind(this, rowIndex, 'valorMercado')}

                                />}
                            width={80}
                        />
                    </Table>
                </TouchExampleWrapper>

                {/* Menu de opciones */}
                {this.props.children}
            </div>
        )
    }
}
TablaProductos.propTypes = {
    // Objetos
    productos: PropTypes.arrayOf(PropTypes.object).isRequired,
    skuSeleccionado: PropTypes.string.isRequired,
    scrollToRow: PropTypes.number.isRequired,
    // Permisos productos
    puedeModificarProducto: PropTypes.bool.isRequired,
    // Metodos
    seleccionarProducto: PropTypes.func.isRequired,
    actualizarProducto: PropTypes.func.isRequired,
}

/** ########################################## ########################################## **/
export class TablaArticulos extends React.Component {
    changeDataArticulo(row, colField, inputState){
        // hacer la peticion al servidor solo si ha cambiado y el valor es valido
        if(inputState.dirty && inputState.valid){
            let reqData = {}
            reqData[colField] = inputState.valor
            this.props.actualizarArticulo(this.props.articulos[row].idArticuloAF, reqData)
        }
    }
    render() {
        const {articulos, idArticuloSeleccionado, seleccionarArticulo} = this.props
        return (
            <div className={cx('tablaArticulos')} >
                <TouchExampleWrapper tableWidth={180 + 20} tableHeight={400} style={{display: 'inline-block'}}>
                    <Table
                        // Table
                        width={180 + 20}
                        height={400}
                        scrollToRow={this.props.scrollToRow}
                        // Header
                        headerHeight={30}
                        // Row
                        rowHeight={28}
                        rowsCount={articulos.length}>
                            <Column
                                header={<Cell>ID</Cell>}
                                cell={ ({rowIndex})=>
                                    <NumberCell editable={false}
                                               number={articulos[rowIndex].idArticuloAF}
                                               filaSeleccionada={articulos[rowIndex].idArticuloAF==idArticuloSeleccionado}
                                                // metodos
                                               onClick={ seleccionarArticulo.bind(this, articulos[rowIndex], rowIndex) }
                                               changeData={()=>{}}
                                    />}
                                width={60}
                            />
                            <Column
                                header={<Cell>Stock Total</Cell>}
                                cell={ ({rowIndex})=>
                                    <NumberCell editable={this.props.puedeModificarArticulo}
                                                number={articulos[rowIndex].stock}
                                                filaSeleccionada={articulos[rowIndex].idArticuloAF==idArticuloSeleccionado}
                                                // metodos
                                                onClick={ seleccionarArticulo.bind(this, articulos[rowIndex], rowIndex) }
                                                changeData={this.changeDataArticulo.bind(this, rowIndex, 'stock')}
                                    />}
                                width={120}
                            />
                    </Table>
                </TouchExampleWrapper>

                {/* Menu de opciones */}
                {this.props.children}
            </div>
        )
    }
}
TablaArticulos.propTypes = {
    // Objetos
    articulos: PropTypes.arrayOf(PropTypes.object).isRequired,
    idArticuloSeleccionado: PropTypes.number.isRequired,
    scrollToRow: PropTypes.number.isRequired,
    // Permisos Articulos
    puedeModificarArticulo: PropTypes.bool.isRequired,
    // Metodos
    seleccionarArticulo: PropTypes.func.isRequired,
    actualizarArticulo: PropTypes.func.isRequired
}


/** ########################################## ########################################## **/
export class TablaBarras extends React.Component {
    render() {
        const {barras, barraSeleccionada, seleccionarBarra} = this.props
        const width = 150
        return (
            <div className={cx('tablaBarras')} >
                <TouchExampleWrapper tableWidth={width+20} tableHeight={400} style={{display: 'inline-block'}}>
                    <Table
                        // Table
                        width={width+ 20}
                        height={400}
                        // Header
                        headerHeight={30}
                        // Row
                        rowHeight={28}
                        rowsCount={barras.length}>
                        <Column
                            header={<Cell>Barra</Cell>}
                            cell={ ({rowIndex})=>
                                <TextoCell editable={false}
                                           texto={barras[rowIndex]}
                                           filaSeleccionada={barras[rowIndex] == barraSeleccionada}
                                           // metodos
                                           onClick={ seleccionarBarra.bind(this, barras[rowIndex]) }
                                           changeData={()=>{}}
                                />}
                            width={150}
                        />
                    </Table>
                </TouchExampleWrapper>

                <div className="pull-right">
                    <button className="btn btn-xs btn-default">
                        Agregar
                    </button>
                    <button className="btn btn-xs btn-default">
                        Eliminar
                    </button>
                </div>
            </div>
        )
    }
}
TablaBarras.propTypes = {
    // Objetos
    barras: PropTypes.arrayOf(PropTypes.string).isRequired,
    barraSeleccionada: PropTypes.string.isRequired,
    // Permisos
    puedeModificar: PropTypes.bool.isRequired,
    // Metodos
    //actualizarProducto: PropTypes.func.isRequired,
    seleccionarBarra: PropTypes.func.isRequired
}

// ## ######################### Componentes privados ######################### ## //
const TextoCell = ({texto, editable, filaSeleccionada, changeData, ...props})=>(
    <Cell {...props}
          className={cx('texto-cell', {'selected-row': filaSeleccionada})}
    >
        <InputTexto
            asignada={texto}
            onGuardar={changeData}
            focusRowAnterior={()=>{}}
            focusRowSiguiente={()=>{}}
            editable={editable}
        />
    </Cell>
)
const NumberCell = ({number, editable, filaSeleccionada, changeData,  ...props})=>(
    <Cell {...props}
          className={cx('td-precio', {'selected-row': filaSeleccionada})}
    >
        <InputNumber
            asignada={number}
            onGuardar={changeData}
            focusRowAnterior={()=>{}}
            focusRowSiguiente={()=>{}}
            editable={editable}
        />
    </Cell>
)