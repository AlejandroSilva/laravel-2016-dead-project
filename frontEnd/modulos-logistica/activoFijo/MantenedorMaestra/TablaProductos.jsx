// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import { Table, Column, Cell } from 'fixed-data-table'
import { InputTexto } from '../../shared/InputTexto.jsx'
// Styles
import classNames from 'classnames/bind'
import * as css from './tablaProductos.css'
let cx = classNames.bind(css)

export class TablaProductos extends React.Component {
    constructor(props) {
        super(props)

        this.changeData = (row, colField, inputState)=>{
            // hacer la peticion al servidor solo si ha cambiado y el valor es valido
            if(inputState.dirty && inputState.valid){
                let reqData = {}
                reqData[colField] = inputState.valor
                this.props.actualizarProducto(this.props.productos[row].SKU, reqData)
            }
        }
    }
    render(){
        return (
            <Table
                // Table
                width={1000 - 30}
                height={400}
                // Header
                headerHeight={30}
                // Row
                rowHeight={28}
                rowsCount={this.props.productos.length}>
                <Column
                    header={<Cell>#</Cell>}
                    cell={ ({rowIndex})=> <Cell>{rowIndex+1}</Cell> }
                    width={30}
                />
                <Column
                    header={<Cell>SKU</Cell>}
                    cell={ <SkuCell className={cx('td-sku')} column='SKU'
                            data={this.props.productos} changeData={this.changeData}
                        />}
                    width={200}
                />
                <Column
                    header={<Cell>Descripción</Cell>}
                    cell={ <DescripcionCell editable={this.props.puedeModificar} className={cx('td-descripcion')} column='descripcion'
                            data={this.props.productos} changeData={this.changeData}
                        />}
                    width={350}
                />
                <Column
                    header={<Cell>Valor mercado</Cell>}
                    cell={ <PrecioCell editable={this.props.puedeModificar} className={cx('td-precio')} column='valorMercado'
                            data={this.props.productos} changeData={this.changeData}
                        />}
                    width={120}
                />
            </Table>
        )
    }
}

TablaProductos.propTypes = {
    // Objetos
    productos: PropTypes.arrayOf(PropTypes.object).isRequired,
    // Permisos
    puedeModificar: PropTypes.bool.isRequired,
    // Metodos
    actualizarProducto: PropTypes.func.isRequired
}


// ## ######################### Componentes privados ######################### ## // 

const SkuCell = ({data, rowIndex, column, changeData, editable, ...props})=>(
    <Cell {...props}>
        <InputTexto
            asignada={data[rowIndex][column]}
            onGuardar={changeData.bind(this, rowIndex, column)}
            focusRowAnterior={()=>{}}
            focusRowSiguiente={()=>{}}
            editable={editable}
        />
    </Cell>
)
const DescripcionCell = ({data, rowIndex, column, changeData, editable, ...props})=>(
    <Cell {...props}>
        <InputTexto
            asignada={data[rowIndex][column]}
            onGuardar={changeData.bind(this, rowIndex, column)}
            focusRowAnterior={()=>{}}
            focusRowSiguiente={()=>{}}
            editable={editable}
        />
    </Cell>
)
const PrecioCell = ({data, rowIndex, column, changeData, editable, ...props})=>(
    <Cell {...props}>
        <InputTexto
            asignada={data[rowIndex][column]}
            onGuardar={changeData.bind(this, rowIndex, column)}
            focusRowAnterior={()=>{}}
            focusRowSiguiente={()=>{}}
            editable={editable}
        />
    </Cell>
)

// const BarraCell = ({data, rowIndex, column, changeData, ...props})=>(
//     <Cell {...props}>
//         <InputTexto
//             asignada={data[rowIndex][column] || ''}
//             onGuardar={changeData.bind(this, rowIndex, column)}
//             focusRowAnterior={()=>{}}
//             focusRowSiguiente={()=>{}}
//             //editable={false}
//         />
//     </Cell>
// )