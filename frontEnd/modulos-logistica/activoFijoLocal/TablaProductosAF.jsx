// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import { Table, Column, Cell } from 'fixed-data-table'
import TouchExampleWrapper from '../shared/TouchExampleWrapper.jsx'
// Styles
import classNames from 'classnames/bind'
import * as css from './TablaProductos.css'
let cx = classNames.bind(css)

export class TablaProductosAF extends React.Component {
    // constructor(props) {
    //     super(props)
    // }
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
                    rowsCount={this.props.productos.length}>

                    <Column
                        header={<Cell>#</Cell>}
                        cell={({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {rowIndex+1}
                            </Cell>}
                        width={30}
                        flexGrow={1}
                    />
                    <Column
                        header={<Cell>código</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {this.props.productos[rowIndex].codigo}
                            </Cell> }
                        width={100}
                        flexGrow={1}
                    />
                    <Column
                        header={<Cell>Descripión</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {this.props.productos[rowIndex].descripcion}
                            </Cell> }
                        width={125}
                        flexGrow={1}
                    />
                    <Column
                        header={<Cell>Almacen</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                <a href="#">{this.props.productos[rowIndex].almacen}</a>
                            </Cell>}
                        width={80}
                        flexGrow={1}
                    />
                    <Column
                        header={<Cell>Barra1</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {this.props.productos[rowIndex].barra1}
                            </Cell> }
                        width={95}
                        flexGrow={1}
                    />
                    <Column
                        header={<Cell>Barra2</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {this.props.productos[rowIndex].barra2}
                            </Cell> }
                        width={95}
                        flexGrow={1}
                    />
                    <Column
                        header={<Cell>Barra3</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {this.props.productos[rowIndex].barra3}
                            </Cell> }
                        width={95}
                        flexGrow={1}
                    />
                    <Column
                        header={<Cell>Precio</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {this.props.productos[rowIndex].precio}
                            </Cell> }
                        width={70}
                        flexGrow={1}
                    />
                </Table>
            </TouchExampleWrapper>
        )
    }
}

TablaProductosAF.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}