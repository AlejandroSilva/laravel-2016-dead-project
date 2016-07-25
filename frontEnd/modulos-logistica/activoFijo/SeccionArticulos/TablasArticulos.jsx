// Librerias
import React from 'react'
let PropTypes = React.PropTypes
import _ from 'lodash'
// Componentes
import { Table, Column, Cell } from 'fixed-data-table'
import TouchExampleWrapper from '../../../shared/TouchExampleWrapper.jsx'
// Styles
import classNames from 'classnames/bind'
import * as css from './seccionArticulos.css'
let cx = classNames.bind(css)

/** ***************************************** ***************************************** **/
export class TablaGeneralArticulos extends React.Component {
    render(){
        return (
            <TouchExampleWrapper
                tableWidth={600}
                tableHeight={300}
            >
                <Table
                    // table
                    width={600}
                    height={300}
                    // header
                    headerHeight={30}
                    // rows
                    rowHeight={22}
                    rowsCount={this.props.articulos.length}>

                    <Column
                        header={<Cell>#</Cell>}
                        cell={({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {rowIndex+1}
                            </Cell>}
                        width={35}
                        flexGrow={1}
                    />
                    <Column
                        header={<Cell>SKU</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {this.props.articulos[rowIndex].sku}
                            </Cell> }
                        width={40}
                        flexGrow={1}
                    />
                    <Column
                        header={<Cell>Descripión</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {this.props.articulos[rowIndex].descripcion}
                            </Cell> }
                        width={120}
                        flexGrow={1}
                    />
                    <Column
                        header={<Cell>Barras</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {this.props.articulos[rowIndex].barras[0]}
                                {this.props.articulos[rowIndex].barras[1]? `  ${this.props.articulos[rowIndex].barras[1]}` : ''}
                                {this.props.articulos[rowIndex].barras[2]? `  ${this.props.articulos[rowIndex].barras[2]}` : ''}
                            </Cell> }
                        width={180}
                        flexGrow={1}
                    />
                    <Column
                        header={<Cell>Almacen</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                <a onClick={this.props.seleccionarAlmancen.bind(this, this.props.articulos[rowIndex].idAlmacenAF)}>
                                    {this.props.articulos[rowIndex].almacen}
                                </a>
                            </Cell>}
                        width={80}
                        flexGrow={1}
                    />
                    <Column
                        header={<Cell>Stock Actual asignado</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {this.props.articulos[rowIndex].stockActual}
                            </Cell> }
                        width={95}
                        flexGrow={1}
                    />
                </Table>
            </TouchExampleWrapper>
        )
    }
}


/** ***************************************** ***************************************** **/
export class TablaEntrega extends React.Component {
    render(){
        return (
            <TouchExampleWrapper
                tableWidth={600} // 630 -15 -15
                tableHeight={250}
            >
                <Table
                    // Table
                    width={390}
                    height={250}
                    // Header
                    headerHeight={30}
                    // Rows
                    rowHeight={40}
                    rowsCount={this.props.articulos.length}>
                    <Column
                        header={<Cell>#</Cell>}
                        cell={ ({rowIndex})=>
                            <CellNumeral numero={rowIndex+1}/>
                        }
                        width={30}
                    />
                    <Column
                        header={<Cell>Producto</Cell>}
                        cell={ ({rowIndex})=>
                            <CellProducto
                                SKU={this.props.articulos[rowIndex].SKU}
                                descripcion={this.props.articulos[rowIndex].descripcion}
                            />
                        }
                        width={150}
                        //flexGrow={1}
                    />
                    <Column
                        header={<Cell>Barras</Cell>}
                        cell={ ({rowIndex})=>
                            <CellBarras
                                barras={this.props.articulos[rowIndex].barras}
                            />
                        }
                        width={150} // importante por el css
                    />
                    <Column
                        header={<Cell className={cx('header-compact')} >
                            Stock Disponible
                        </Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={css.cell}>
                                {this.props.articulos[rowIndex].stockDisponible}
                            </Cell> }
                        width={60}
                    />
                    <Column
                        header={<Cell className={cx('header-compact')}>Stock a entregar</Cell>}
                        cell={ ({rowIndex})=> <Cell>
                            <select value={this.props.articulos[rowIndex].stockSeleccionado}
                                    onChange={this.props.cambiarCantidad.bind(this, this.props.articulos[rowIndex].idArticuloAF)}
                            >
                                {_.range(0, this.props.articulos[rowIndex].stockDisponible+1).map(cantidad=>(
                                    <option key={cantidad} value={cantidad}>{cantidad}</option>
                                ))}
                            </select>
                        </Cell> }
                        width={60}
                    />
                </Table>
            </TouchExampleWrapper>
        )
    }
}
TablaEntrega.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    cambiarCantidad: PropTypes.func.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}


/** ***************************************** ***************************************** **/
export class TablaTransferencia extends React.Component {
    render(){
        return (
            <TouchExampleWrapper
                tableWidth={570}    // 600 -15 -15
                tableHeight={250}
            >
                <Table
                    // Table
                    width={390}
                    height={250}
                    // Header
                    headerHeight={30}
                    // Rows
                    rowHeight={40}
                    rowsCount={this.props.articulos.length}>
                    <Column
                        header={<Cell>#</Cell>}
                        cell={ ({rowIndex})=>
                            <CellNumeral numero={rowIndex+1}/>
                        }
                        width={30}
                    />
                    <Column
                        header={<Cell>Producto</Cell>}
                        cell={ ({rowIndex})=>
                            <CellProducto
                                SKU={this.props.articulos[rowIndex].SKU}
                                descripcion={this.props.articulos[rowIndex].descripcion}
                            />
                        }
                        width={160}
                        //flexGrow={1}
                    />
                    <Column
                        header={<Cell>Barras</Cell>}
                        cell={ ({rowIndex})=>
                            <CellBarras
                                barras={this.props.articulos[rowIndex].barras}
                            />
                        }
                        width={140}     // importante por el css
                    />
                    <Column
                        header={<Cell className={cx('header-compact')} >Stock en origen</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={css.cell}>
                                {this.props.articulos[rowIndex].stockEnOrigen}
                            </Cell> }
                        width={60}
                    />
                    <Column
                        header={<Cell className={cx('header-compact')} >Stock a transferir</Cell>}
                        cell={ ({rowIndex})=> <Cell>
                            <select value={this.props.articulos[rowIndex].stockATransferir}
                                    onChange={this.props.cambiarStockATransferir.bind(this, this.props.articulos[rowIndex].idArticuloAF)}
                            >
                                {_.range(0, this.props.articulos[rowIndex].stockEnOrigen+1).map(cantidad=>(
                                    <option key={cantidad} value={cantidad}>{cantidad}</option>
                                ))}
                            </select>
                        </Cell> }
                        width={60}
                    />
                </Table>
            </TouchExampleWrapper>
        )
    }
}
TablaTransferencia.propTypes = {
    cambiarStockATransferir: PropTypes.func.isRequired,
    articulos: PropTypes.arrayOf(PropTypes.object).isRequired
}

/** ***************************************** ***************************************** **/

const CellNumeral = ({numero})=>
    <Cell className={cx('cell')} >
        {numero}
    </Cell>


const CellProducto = ({SKU, descripcion})=>
    <Cell className={cx('cell-producto')}>
        <p className={cx('producto-sku')} ><span>sku</span>{SKU}</p>
        <p className={cx('producto-descripcion')} >{descripcion}</p>
    </Cell>

const CellBarras = ({barras})=>
    <Cell className={cx('cell-barras')}>
        {barras.map(barra=>
            <p key={barra} className={cx('barra-codigo')} >{barra}</p>
        )}
    </Cell>