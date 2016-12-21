// Libs
import React from 'react'
import api from '../../apiClient/v1'
// Componentes
import { AutoSizer, Table, Column } from 'react-virtualized'
import {
    ModalTrigger,
    ModalAgregarLocal,
    ModalEditarLocal
} from './Modales.jsx'
// Styles
import * as css from './mantenedorLocal.css'

class MantenedorLocales2 extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            idCliente: 1,
            locales: [],
        }
        this.seleccionarCliente = (evt)=>{
            const idCliente = evt.target.value
            this.setState({
                idCliente: idCliente,
                locales: []
            }, ()=>{
                this.fetchLocales()
            })
        }
        this.fetchLocales = ()=>{
            api.cliente.getLocales(this.state.idCliente)
                .then(locales=>{
                    this.setState({locales})
                })
                .catch(meh=>{})
        }
        this.actualizarLocal = (local)=>{
            let request = api.local.actualizar(local.idLocal, local)
            request
                .then(()=> this.fetchLocales())
                .catch(meh=>{})
            return request
        }
        this.agregarLocal = (local)=>{
            let request = api.local.nuevo(local)
            request
                .then(()=> this.fetchLocales())
                .catch(meh=>{})
            return request
        }
    }
    componentWillMount(){
        this.fetchLocales()
    }

    render(){
        return (
            <div>
                <div className="row">
                    <div className="col-md-6">
                        <h1>Mantenedor de Locales</h1>
                    </div>
                    <div className="col-md-6">
                        <div className="pull-right" style={{marginTop: '25px'}}>
                            <ModalTrigger>
                                {(isVisible, showModal, hideModal)=>
                                    <button className="btn btn-sm btn-success" onClick={showModal}>
                                        Agregar Local
                                        {isVisible && <ModalAgregarLocal
                                            agregarLocal={this.agregarLocal}
                                            hideModal={hideModal}
                                            idCliente={this.state.idCliente}
                                            jornadas={this.props.jornadas}
                                            comunas={this.props.comunas}
                                            formatoLocales={this.props.formatoLocales}
                                        />}
                                    </button>
                                }
                            </ModalTrigger>
                        </div>
                    </div>
                </div>
                <SelectorCliente
                    idClienteSeleccionado={this.state.idCliente}
                    seleccionarCliente={this.seleccionarCliente}
                    clientes={this.props.clientes}
                />
                <Locales
                    locales={this.state.locales}
                    jornadas={this.props.jornadas}
                    formatoLocales={this.props.formatoLocales}
                    comunas={this.props.comunas}
                    actualizarLocal={this.actualizarLocal}
                />
            </div>
        )
    }
}

export default MantenedorLocales2

const SelectorCliente = ({idClienteSeleccionado, seleccionarCliente, clientes})=>
    <div className="row">
        <div className="col-sm-3 form-group ">
            <label className="control-label" htmlFor="cliente">Cliente</label>
            <select className="form-control" name="cliente"
                    value={idClienteSeleccionado}
                    onChange={seleccionarCliente}
            >
                <option value="-1" disabled>--</option>
                {clientes.map(cliente=>
                    <option key={cliente.idCliente} value={cliente.idCliente}>{`${cliente.nombreCorto} - ${cliente.nombre}`}</option>
                )}
            </select>
        </div>
    </div>

const Locales = ({locales, jornadas, formatoLocales, comunas, actualizarLocal})=>
    <div style={{height: '80%'}}>
        <AutoSizer>
        {({height, width}) =>
            <Table
                // general
                height={height}
                width={width}
                // headers
                headerHeight={30}
                headerClassName={css.headerColumn}
                // rows
                rowCount={locales.length}
                rowGetter={({index})=> locales[index]}
                rowHeight={30}
                rowClassName={({index})=> index<0? css.headerRow : ((index%2===0)? css.evenRow : css.oddRow)}
            >
                {/* Cliente */}
                <Column
                    dataKey='cliente' label="CL"
                    cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData.cliente.nombreCorto}
                    width={40}
                />
                {/* Ceco */}
                <Column
                    dataKey='numero' label="CECO"
                    cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                    width={40}
                />
                {/* Nombre */}
                <Column
                    dataKey='nombre' label="Nombre"
                    cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                    width={120}
                />
                {/* Formato Local */}
                <Column
                    dataKey='formatoLocal_nombre' label="Formato"
                    cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                    width={120}
                />
                {/* Jornada */}
                <Column
                    dataKey='jornadaSugerida' label="Jornada"
                    cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                    width={70}
                />
                {/* Hora Apertura */}
                <Column
                    dataKey='horaApertura' label="Apertura"
                    cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                    width={70}
                />
                {/* Hora Cierre */}
                <Column
                    dataKey='horaCierre' label="Cierre"
                    cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                    width={50}
                />
                {/* Email */}
                <Column
                    dataKey='emailContacto' label="Email"
                    cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                    width={100}
                />
                {/* Telefono 1 */}
                <Column
                    dataKey='telefono1' label="Telefono"
                    cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                    width={85}
                />
                {/* Telefono 2 */}
                <Column
                    dataKey='telefono2' label=""
                    cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                    width={85}
                />
                {/* Stock */}
                <Column
                    dataKey='stock' label="Stock"
                    cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                    width={50}
                />
                {/* Comuna */}
                <Column
                    dataKey='comuna_nombre' label="Comuna"
                    cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                    width={100}
                />
                {/* Direccion */}
                <Column
                    dataKey='direccion' label="Dirección"
                    cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                    width={200}
                    flexGrow={1}
                />
                <Column
                    dataKey='direccion' label="Opciones"
                    cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) =>
                        <ModalTrigger>
                            {(isVisible, showModal, hideModal)=>
                                <button className="btn btn-xs btn-primary" onClick={showModal}>
                                    Editar
                                    {isVisible && <ModalEditarLocal
                                        local={rowData}
                                        hideModal={hideModal}
                                        jornadas={jornadas}
                                        comunas={comunas}
                                        formatoLocales={formatoLocales}
                                        actualizarLocal={actualizarLocal}
                                    />}
                                </button>
                            }
                        </ModalTrigger>
                    }
                    width={80}
                />
            </Table>
        }
    </AutoSizer>
    </div>