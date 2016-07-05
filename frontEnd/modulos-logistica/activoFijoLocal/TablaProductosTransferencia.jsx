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

export class TablaProductosTransferencia extends React.Component {
    // constructor(props) {
    //     super(props)
    // }
    render(){
        return (
            <TouchExampleWrapper
                tableWidth={390}
                tableHeight={250}
            >
                <Table
                    // Table
                    width={390}
                    height={250}
                    // Header
                    headerHeight={30}
                    // Rows
                    rowHeight={30}
                    rowsCount={this.props.productos.length}>
                    <Column
                        header={<Cell>#</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={css.cell}>
                                {rowIndex+1}
                            </Cell>}
                        width={30}
                    />
                    <Column
                        header={<Cell>COD</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={css.cell}>
                                {this.props.productos[rowIndex].codigo}
                            </Cell> }
                        width={90}
                    />
                    <Column
                        header={<Cell>Descripción</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={css.cell}>
                                {this.props.productos[rowIndex].descripcion}
                            </Cell> }
                        width={90}
                    />
                    <Column
                        header={<Cell>Almacen</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={css.cell}>
                                <a href="#">{this.props.productos[rowIndex].almacen}</a>
                            </Cell> }
                        width={75}
                    />
                    <Column
                        header={<Cell></Cell>}
                        cell={ ({rowIndex})=> <Cell>
                            <button className="btn btn-xs btn-danger"
                                onClick={this.props.quitarProducto.bind(this, this.props.productos[rowIndex].id)}
                            >
                                quitar
                            </button>
                        </Cell> }
                        width={60}
                    />
                </Table>
            </TouchExampleWrapper>
        )
    }
}

TablaProductosTransferencia.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}