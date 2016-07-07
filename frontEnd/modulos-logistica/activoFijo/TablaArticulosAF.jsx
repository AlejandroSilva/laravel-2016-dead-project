// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import { Table, Column, Cell } from 'fixed-data-table'
import TouchExampleWrapper from '../shared/TouchExampleWrapper.jsx'
// Styles
import classNames from 'classnames/bind'
import * as css from './TablaArticulosAF.css'
let cx = classNames.bind(css)

export class TablaArticulosAF extends React.Component {
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
                    rowHeight={20}
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
                                {this.props.articulos[rowIndex].SKU}
                            </Cell> }
                        width={50}
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
                        header={<Cell>COD</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {this.props.articulos[rowIndex].codArt}
                            </Cell> }
                        width={120}
                        flexGrow={1}
                    />
                    <Column
                        header={<Cell>Almacen</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                <a href="#">{this.props.articulos[rowIndex].almacen}</a>
                            </Cell>}
                        width={80}
                        flexGrow={1}
                    />
                    <Column
                        header={<Cell>Barra1</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {this.props.articulos[rowIndex].barras[0]}
                            </Cell> }
                        width={95}
                        flexGrow={1}
                    />
                </Table>
            </TouchExampleWrapper>
        )
    }
}

TablaArticulosAF.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}