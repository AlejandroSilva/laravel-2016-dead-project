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

export class ModalDevolucion extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            preguia: {},
            articulos: []
        }

        this.onCancelar = ()=>{
            console.log('cerrar modal')
        }
        this.onDevolverArticulos = ()=>{
            this.props.devolverArticulos(this.props.idPreguia, {
                codigosArticulos: this.state.articulos.map(art=>art.codArticuloAF)
            })
                .then(resp=>{
                    // al devolverlos, esconder el modal
                    this.props.hideModal()
                })
        }
    }
    componentWillMount(){
        this.props.fetchPreguia(this.props.idPreguia)
            .then(preguia=>{
                this.setState({
                    preguia,
                    articulos: preguia.articulos
                })
                console.log('datos preguia', preguia.articulos)
            })
    }

    render(){
        return (
            <div>
                <h4>{this.state.preguia.descripcion}</h4>
                <TablaArticulosDevolucion
                    articulos={this.state.articulos}
                />

                {/* Botones Cancelar/Siguiente */}
                <div className="btn-group btn-group-justified">
                    <button type='button' className="btn btn-default" style={{width: '50%'}}
                            onClick={this.onCancelar}>
                        Cancelar
                    </button>
                    <button type='button' className="btn btn-primary" style={{width: '50%'}}
                            disabled={false}
                            onClick={this.onDevolverArticulos}>
                        Devolver articulos
                    </button>
                </div>
            </div>
        )
    }
}

ModalDevolucion.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
    fetchPreguia: PropTypes.func.isRequired
}

/** ******************************************* **/

export class TablaArticulosDevolucion extends React.Component {
    constructor(props) {
        super(props)
        this.estados = ['---', 'entregado', 'retornado']
    }
    render()    {
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
                    rowHeight={30}
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
                                {this.props.articulos[rowIndex].codArticuloAF}
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
                        header={<Cell>Estado</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {this.estados[ this.props.articulos[rowIndex].estado ]}
                            </Cell> }
                        width={95}
                        flexGrow={1}
                    />
                </Table>
            </TouchExampleWrapper>
        )
    }
}

TablaArticulosDevolucion.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}