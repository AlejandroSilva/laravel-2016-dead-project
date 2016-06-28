// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import { Table, Column, Cell } from 'fixed-data-table'
import { InputTexto } from './shared/InputTexto.jsx'
// Styles
import classNames from 'classnames/bind'
import * as css from './TablaMaestra.css'
let cx = classNames.bind(css)

export class TablaMaestra extends React.Component {
    constructor(props) {
        super(props)
    }
    
    render(){
        return (
            <Table
                rowHeight={30}
                rowsCount={this.props.productosMaestra.length}
                width={1200}
                height={600}
                headerHeight={30}>
                <Column
                    header={<Cell>#</Cell>}
                    cell={ ({rowIndex})=> <Cell>{rowIndex+1}</Cell> }
                    width={80}
                />
                <Column
                    header={<Cell>SkU</Cell>}
                    cell={ <SkuCell className={cx('td-sku')} column='SKU'
                            data={this.props.productosMaestra} changeData={this.props.changeData}
                        />}
                    width={200}
                />
                <Column
                    header={<Cell>Descripción</Cell>}
                    cell={ <DescripcionCell className={cx('td-descripcion')} column='descripcion'
                            data={this.props.productosMaestra} changeData={this.props.changeData}
                        />}
                    width={300}
                />
                <Column
                    header={<Cell>Precio</Cell>}
                    cell={ <PrecioCell className={cx('td-precio')} column='precio'
                            data={this.props.productosMaestra} changeData={this.props.changeData}
                        />}
                    width={80}
                />
                <Column
                    header={<Cell>Barra1</Cell>}
                    cell={ <BarraCell className={cx('td-barra')} column='barra1'
                            data={this.props.productosMaestra} changeData={this.props.changeData}
                        />}
                    width={120}
                />
                <Column
                    header={<Cell>Barra1</Cell>}
                    cell={ <BarraCell className={cx('td-barra')} column='barra2'
                            data={this.props.productosMaestra} changeData={this.props.changeData}
                        />}
                    width={120}
                />
                <Column
                    header={<Cell>Barra1</Cell>}
                    cell={ <BarraCell className={cx('td-barra')} column='barra3'
                            data={this.props.productosMaestra} changeData={this.props.changeData}
                        />}
                    width={120}
                />
            </Table>
        )
    }
}

TablaMaestra.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}


// ## ######################### Componentes privados ######################### ## // 

const SkuCell = ({data, rowIndex, column, changeData, ...props})=>(
    <Cell {...props}>
        <InputTexto
            asignada={data[rowIndex][column]}
            onGuardar={changeData.bind(this, rowIndex, column)}
            focusRowAnterior={()=>{}}
            focusRowSiguiente={()=>{}}
            //editable={false}
        />
        {/*
         <input type="text"
         value={data[rowIndex].SKU}
         onChange={changeData.bind(this, rowIndex, column)}
         />
         */}
    </Cell>
)
const DescripcionCell = ({data, rowIndex, column, changeData, ...props})=>(
    <Cell {...props}>
        <InputTexto
            asignada={data[rowIndex][column]}
            onGuardar={changeData.bind(this, rowIndex, column)}
            focusRowAnterior={()=>{}}
            focusRowSiguiente={()=>{}}
            //editable={false}
        />
        {/*
         <input type="text"
         value={data[rowIndex].descripcion}
         onChange={changeData.bind(this, rowIndex, column)}
         />
         */}
    </Cell>
)
const PrecioCell = ({data, rowIndex, column, changeData, ...props})=>(
    <Cell {...props}>
        <InputTexto
            asignada={data[rowIndex][column]}
            onGuardar={changeData.bind(this, rowIndex, column)}
            focusRowAnterior={()=>{}}
            focusRowSiguiente={()=>{}}
            //editable={false}
        />
        {/*
         <input type="text"
         value={data[rowIndex].precio}
         onChange={changeData.bind(this, rowIndex, column)}
         />
         */}
    </Cell>
)

const BarraCell = ({data, rowIndex, column, changeData, ...props})=>(
    <Cell {...props}>
        <InputTexto
            asignada={data[rowIndex][column] || ''}
            onGuardar={changeData.bind(this, rowIndex, column)}
            focusRowAnterior={()=>{}}
            focusRowSiguiente={()=>{}}
            //editable={false}
        />
    </Cell>
)