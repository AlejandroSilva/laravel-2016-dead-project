// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import { Table, Column, Cell } from 'fixed-data-table'
import TouchExampleWrapper from '../../../shared/TouchExampleWrapper.jsx'
// Styles
import classNames from 'classnames/bind'
import * as css from './seccionPreguias.css'
let cx = classNames.bind(css)


export class TablaRetornoArticulos extends React.Component {
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
                    headerHeight={35}
                    // rows
                    rowHeight={40}
                    rowsCount={this.props.articulos.length}>

                    <Column
                        header={<Cell>#</Cell>}
                        cell={({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {rowIndex+1}
                            </Cell>}
                        width={30}
                    />
                    <Column
                        header={<Cell>Producto</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell-producto')}>
                                <p className={cx('producto-sku')} >
                                    <span>sku</span>
                                    {this.props.articulos[rowIndex].SKU}</p>
                                <p className={cx('producto-descripcion')} >{this.props.articulos[rowIndex].descripcion}</p>
                            </Cell> }
                        width={160}
                        flexGrow={1}
                    />
                    <Column
                        header={<Cell>Barras</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell-barras')}>
                                {this.props.articulos[rowIndex].barras.map(barra=>
                                    <p key={barra} className={cx('barra-codigo')} >{barra}</p>
                                )}
                            </Cell> }
                        width={120}
                        //flexGrow={1}
                    />
                    <Column
                        header={<Cell>Retorn/Entreg</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell-stock')}>
                                {this.props.articulos[rowIndex].stockRetornado} / {this.props.articulos[rowIndex].stockEntregado}
                                {/*
                                 <p className={cx('stock-retornado')}>
                                 <span>retor.</span>{this.props.articulos[rowIndex].stockRetornado}
                                 </p>
                                 <p className={cx('stock-total')} >
                                 <span>entre.</span>{this.props.articulos[rowIndex].stockEntregado}
                                 </p>
                                 */}
                            </Cell>}
                        width={80}
                        flexGrow={1}
                    />
                    <Column
                        header={<Cell>A retornar</Cell>}
                        cell={ ({rowIndex})=> {
                            let articulo = this.props.articulos[rowIndex]
                            return (
                                <Cell className={cx('cell')}>
                                    <select value={articulo.stockParaRetornar}
                                            onChange={this.props.cambiarCantidadDevolucion.bind(this, articulo.idArticuloAF)}
                                        //disabled={articulo.stockPendienteRetorno==0}
                                    >
                                        {_.range(0, articulo.stockPendienteRetorno+1).map(cantidad=>(
                                            <option key={cantidad} value={cantidad}>{cantidad}</option>
                                        ))}
                                    </select>
                                </Cell>
                            )
                        }}
                        width={95}
                        flexGrow={1}
                    />
                </Table>
            </TouchExampleWrapper>
        )
    }
}

TablaRetornoArticulos.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    articulos: PropTypes.arrayOf(PropTypes.object).isRequired
}