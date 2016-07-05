// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import { Table, Column, Cell } from 'fixed-data-table'
// Styles
import classNames from 'classnames/bind'
//import * as css from './TablaMaestra.css'
//let cx = classNames.bind(css)

export class TablaPreguias extends React.Component {
    constructor(props) {
        super(props)
    }
    render(){
        return (
            <Table
                rowHeight={30}
                rowsCount={this.props.preguias.length}
                width={600}
                height={300}
                headerHeight={30}>

                <Column
                    header={<Cell>id</Cell>}
                    cell={ ({rowIndex})=> <Cell>{rowIndex+1}</Cell> }
                    width={30}
                />
                <Column
                    header={<Cell>Fecha</Cell>}
                    cell={ ({rowIndex})=> <Cell>{rowIndex+1}</Cell> }
                    width={80}
                />
                <Column
                    header={<Cell>Descripión</Cell>}
                    cell={ ({rowIndex})=> <Cell>{rowIndex+1}</Cell> }
                    width={80}
                />
                <Column
                    header={<Cell>Estado</Cell>}
                    cell={ ({rowIndex})=> <Cell>{rowIndex+1}</Cell> }
                    width={80}
                />
                <Column
                    header={<Cell>Acciones</Cell>}
                    cell={ ({rowIndex})=> <Cell>{rowIndex+1}</Cell> }
                    width={80}
                    flexGrow={1}
                />
            </Table>
        )
    }
}

TablaPreguias.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}