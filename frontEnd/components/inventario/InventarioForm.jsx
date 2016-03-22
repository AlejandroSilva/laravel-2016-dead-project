// Librerias
import React from 'react'
let PropTypes = React.PropTypes
import JORNADA from '../../models/Jornadas'
import api from '../../apiClient/v1'
// Componentes
import NominaInventario from './NominaInventario.jsx'

class InventarioForm extends React.Component{
    constructor(props){
        super(props)
        this.localNoSeleccionado = {}

        // DefaultState
        this.state = {
            selectedClientId: -1,
            locales: [],
            selectedLocalId: -1,
            selectedLocal: {},
            selectedJornada: JORNADA.NoSeleccionada,
            selectedHoraLlegada: '00:00:00'
        }
    }
    clientSelected(event){
        // al seleccionar un cliente, se deben cargar los locales el el <select> de clientes
        let idCliente = event.target.value
        // buscamos el cliente seleccionado
        let selectedClient = this.props.clientes.find(cliente=>cliente.idCliente==idCliente)

        // lo marcados como seleccionado y mostramos la lista de locales
        this.setState({
            selectedClientId: idCliente,
            locales: selectedClient? selectedClient.locales : [],
            selectedLocalId: -1,
            selectedLocal: this.localNoSeleccionado,
            selectedJornada: JORNADA.NoSeleccionada,
            selectedHoraLlegada: '00:00:00'
        })
    }

    localSelected(event){
        let idLocal = event.target.value
        // hacer una peticion con ajax para obtener datos basicos, stock, jornada, produccion sugerida, entre otros
        api.locales.getVerbose(idLocal)
            .then(local=>{
                // buscar la direccion, comuna y region, retornar un objeto vacio si no existen
                local.direccion = local.direccion || {}
                local.formatolocal = local.formatolocal || {}
                let keyComuna = local.direccion.comuna || {}
                let keyRegion = (keyComuna.provincia && keyComuna.provincia.region)? keyComuna.provincia.region : {}

                // actualizar los datos del state
                this.setState({
                    selectedLocalId: idLocal,
                    selectedLocal: {
                        // informacion general
                        region: keyRegion.nombre,
                        comuna: keyComuna.nombre,
                        direccion: local.direccion.direccion,
                        horaCierre: local.horaCierre,
                        horaLlegadaSugerida: local.horaLlegadaSugerida,
                        // stock y produccion
                        stock: local.stock,
                        fechaStock: local.fechaStock,
                        stockSugerido: local.formatolocal.produccionSugerida,
                        produccion: Math.round( Math.random()*5000 ),              // PENDIENTE
                        produccionSugerida: Math.round( Math.random()*5000 ),      // PENDIENTE
                        idJornada: local.jornada? local.jornada.idJornada : '-1'
                    },
                    selectedHoraLlegada: local.horaLlegadaSugerida,
                    selectedJornada: local.jornada || JORNADA.NoSeleccionada
                })
                //console.log(local, this.state)
                console.log(this.state)
            })
            .catch(err=>{
                this.setState({
                    selectedLocalId: idLocal,
                    selectedLocal: this.localNoSeleccionado,
                    selectedJornada: JORNADA.NoSeleccionada
                })
                console.log(err)
            })
    }

    horaLlegadaSelected(event){
        let horaLlegada = event.target.value;
        console.log(horaLlegada);

        this.setState({
            selectedHoraLlegada: horaLlegada
        });
    }
    jornadaSelected(event){
        let idJornada = event.target.value

        // busca los datos de la jornada seleccionada
        let jornada = JORNADA.asArray[idJornada]
        if(!jornada)
            console.error('la jornada seleccionada no fue encontrada, utiliznando "jornada no encontrada" por defecto)');
        this.setState({
            selectedJornada: jornada || JORNADA.NoSeleccionada // utiliza NoSeleccionada si no encuentra la buscada
        })
        console.log("jornada seleccionada: ", jornada)
    }
    render(){
        return (
            <div>
                <h1 className="page-header">Nuevo Inventario</h1>
                <form className="form-horizontal" method="post">
                    {/* ####### Informacion General ####### */}
                    <div className="row">
                            <h4 className="page-header" style={{margin: '10px'}}>Información general / Cliente y Local</h4>
                    </div>

                    <div className="row">
                        <div className="col-sm-4">
                            {/* Fecha */}
                            <div className="form-group">
                                <label className="col-sm-4 control-label" htmlFor="fechaProgramada">Fecha</label>
                                <div className="col-sm-8">
                                    <input className="form-control" type="date" name="fechaProgramada"/>
                                </div>
                            </div>

                            {/* Cliente */}
                            <div className="form-group">
                                <label className="col-sm-4 control-label" htmlFor="cliente">Cliente</label>
                                <div className="col-sm-8">
                                    <select className="form-control" name="idCliente" onChange={this.clientSelected.bind(this)} value={this.state.selectedClientId}>
                                        <option value="-1" disabled>--</option>
                                        {this.props.clientes.map((cliente, index)=>{
                                            return <option key={index} value={cliente.idCliente}>{cliente.nombre}</option>
                                        })}
                                    </select>
                                </div>
                            </div>

                            {/* Local */}
                            <div className="form-group">
                                <label className="col-sm-4 control-label" htmlFor="idLocal">Local</label>
                                <div className="col-sm-8">
                                    <select className="form-control" name="idLocal" onChange={this.localSelected.bind(this)} value={this.state.selectedLocalId}>
                                        <option value="-1" disabled>--</option>
                                        {this.state.locales.map((local, index)=>{
                                            return <option key={index} value={local.idLocal}>{local.nombre}</option>
                                        })}
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div className="col-sm-8">
                            {/* Region / Comuna */}
                            <div className="row">
                                <div className="col-sm-6">
                                    <div className="form-group">
                                        <label className="col-sm-4 control-label" htmlFor="region">Region</label>
                                        <div className="col-sm-8">
                                            <input className="form-control" type="text" name="region" value={this.state.selectedLocal.region} readOnly/>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-sm-6">
                                    <div className="form-group">
                                        <label className="col-sm-3 control-label" htmlFor="comuna">Comuna</label>
                                        <div className="col-sm-9">
                                            <input className="form-control" type="text" name="comuna" value={this.state.selectedLocal.comuna} readOnly/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Direccion */}
                            <div className="row">
                                <div className="col-sm-12">
                                    <div className="form-group">
                                        <label className="col-sm-2 control-label" htmlFor="direccion">Dirección</label>
                                        <div className="col-sm-10">
                                            <input className="form-control" type="text" name="direccion" value={this.state.selectedLocal.direccion} readOnly/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Hora cierre y Hora llegada */}
                            <div className="row">
                                <div className="col-sm-4">
                                    <div className="form-group">
                                        <label className="col-sm-6 control-label" htmlFor="horaCierre">Hr.Cierre</label>
                                        <div className="col-sm-6">
                                            <input className="form-control" type="text" name="horaCierre" readOnly value={this.state.selectedLocal.horaCierre}/>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-sm-8">
                                    <div className="form-group">
                                        <label className="col-sm-3 control-label" htmlFor="horaLlegada">Hr.Llegada</label>
                                        <div className="col-sm-9">
                                            <div className="input-group">
                                                <div className="input-group-addon">Sugerido: <b>{this.state.selectedLocal.horaLlegadaSugerida}</b></div>
                                                <input className="form-control" type="time" name="horaLlegada" step="10"
                                                       value={this.state.selectedHoraLlegada} onChange={this.horaLlegadaSelected.bind(this)}
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* ####### Stock y Producción ####### */}
                    <div className="row">
                        <h4 className="page-header" style={{margin: '10px'}}>Stock y Producción</h4>
                    </div>
                    <div className="row">
                        <div className="col-sm-6">
                            {/* Stock (y fecha de stock) */}
                            <div className="form-group">
                                <label className="col-sm-4 control-label" htmlFor="stockTeorico">Stock teorico</label>
                                <div className="col-sm-8">
                                    <div className="input-group">
                                        <div className="input-group-addon"><small>al {this.state.selectedLocal.fechaStock}</small></div>
                                        <input className="form-control" type="number" name="stockTeorico" value={this.state.selectedLocal.stock} readOnly/>
                                    </div>
                                </div>
                            </div>

                            {/* Produccion */}
                            <div className="form-group">
                                <label className="col-sm-4 control-label" htmlFor="produccionAsignada">Producción</label>
                                <div className="col-sm-8">
                                    <div className="input-group">
                                        <div className="input-group-addon"><small>Sugerido:</small> <b>{this.state.selectedLocal.produccionSugerida}</b></div>
                                        <input className="form-control" type="number" name="produccionAsignada" value={this.state.selectedLocal.produccion} />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="col-sm-6">
                            {/* Jornada */}
                            <div className="form-group">
                                <label className="col-sm-4 control-label" htmlFor="idJornada">Jornada </label>
                                <div className="col-sm-8">
                                    <select className="form-control" name="idJornada" onChange={this.jornadaSelected.bind(this)} value={this.state.selectedJornada.idJornada}>
                                        <option value="1">no definido</option>
                                        <option value="2">dia</option>
                                        <option value="3">noche</option>
                                        <option value="4">día y noche</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* ####### Nominas ####### */}
                    <div className="row">
                        {/* Nomina de dia */}
                        <NominaInventario
                            titulo="Nomina de Día"
                            stockInicial={111111}
                            dotacionSugerida={11}
                            habilitado={this.state.selectedJornada.dia==="1"}
                        />

                        {/* Nomina de noche */}
                        <NominaInventario
                            titulo="Nomina de Noche"
                            //stockInicial={22222}
                            dotacionSugerida={22}
                            habilitado={this.state.selectedJornada.noche==="1"}
                        />

                    </div>

                    <div className="row">
                        <div className="form-group">
                            <button type="submit" className="btn btn-lg btn-primary">
                                <i className="fa fa-plus"></i> Agregar Inventario
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        )
    }
}
InventarioForm.propTypes = {
    // inventario es opcional
    inventario: PropTypes.object,
    clientes: PropTypes.array.isRequired
}


export default InventarioForm