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

export class TablaArticulosTransferencia extends React.Component {
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
                    rowsCount={this.props.articulos.length}>
                    <Column
                        header={<Cell>#</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={css.cell}>
                                {rowIndex+1}
                            </Cell>}
                        width={30}
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
                        header={<Cell>COD</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={css.cell}>
                                {this.props.articulos[rowIndex].codArt}
                            </Cell> }
                        width={90}
                    />
                    <Column
                        header={<Cell>Descripción</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={css.cell}>
                                {this.props.articulos[rowIndex].descripcion}
                            </Cell> }
                        width={90}
                    />
                    <Column
                        header={<Cell>Almacen</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={css.cell}>
                                <a href="#">{this.props.articulos[rowIndex].almacen}</a>
                            </Cell> }
                        width={75}
                    />
                    <Column
                        header={<Cell></Cell>}
                        cell={ ({rowIndex})=> <Cell>
                            <button className="btn btn-xs btn-danger"
                                onClick={this.props.quitarProducto.bind(this, this.props.articulos[rowIndex].codArt)}
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

TablaArticulosTransferencia.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}