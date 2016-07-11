// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import Modal from 'react-bootstrap/lib/Modal.js'
import { Table, Column, Cell } from 'fixed-data-table'
import { ModalDevolucion } from './ModalDevolucion.jsx'
// Styles
import classNames from 'classnames/bind'
import * as css from './TablaArticulosAF.css'
import * as cssModal from './modal.css'
let cx = classNames.bind(css)

export class TablaPreguias extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            modal_devolucionVisible: false,
            modal_idPreguia: 0
        }
        this.showModalDevolucion = (idPreguia)=>{
            this.setState({
                modal_devolucionVisible: true,
                modal_idPreguia: idPreguia
            })
        }
        this.hideModalDevolucion = ()=>{
            this.setState({modal_devolucionVisible: false})
        }
    }
    render(){
        return (
            <div>
                <Modal
                    show={this.state.modal_devolucionVisible}
                    //onEnter={this.props.onEnter}
                    onHide={this.hideModalDevolucion}
                    animation={false}
                    dialogClassName={cssModal.modalDevolucion}>
                    <Modal.Header closeButton>
                        <Modal.Title>Devolución de Artiulos</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>

                        <ModalDevolucion
                            idPreguia={this.state.modal_idPreguia}
                            fetchPreguia={this.props.fetchPreguia}
                            devolverArticulos={this.props.devolverArticulos}
                            hideModal={this.hideModalDevolucion}
                        />

                    </Modal.Body>
                </Modal>

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
                        header={<Cell>Fecha Emisión</Cell>}
                        cell={ ({rowIndex})=>
                                <Cell className={cx('cell')}>
                                    {this.props.preguias[rowIndex].fechaEmision}
                                </Cell> }
                        width={80}
                    />
                    <Column
                        header={<Cell>Descripción</Cell>}
                        cell={ ({rowIndex})=>
                                <Cell className={cx('cell')}>
                                    {this.props.preguias[rowIndex].descripcion}
                                </Cell> }
                        width={150}
                    />
                    <Column
                        header={<Cell>Origen</Cell>}
                        cell={ ({rowIndex})=>
                                <Cell className={cx('cell')}>
                                    {this.props.preguias[rowIndex].almacenOrigen}
                                </Cell> }
                        width={80}
                    />
                    <Column
                        header={<Cell>Destino</Cell>}
                        cell={ ({rowIndex})=>
                                <Cell className={cx('cell')}>
                                    {this.props.preguias[rowIndex].almacenDestino}
                                </Cell> }
                        width={80}
                    />
                    <Column
                        header={<Cell>Acciones</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell>
                                <button className="btn btn-xs btn-primary"
                                        onClick={ this.showModalDevolucion.bind(this, this.props.preguias[rowIndex].idPreguia) }
                                >Devolver articulos</button>
                                {/*<button className="btn btn-xs btn-default">Ver</button>*/}
                            </Cell> }
                        width={80}
                        flexGrow={1}
                    />
                </Table>
            </div>
        )
    }
}

TablaPreguias.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
    fetchPreguia: PropTypes.func.isRequired
}