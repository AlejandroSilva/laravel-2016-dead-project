// Libs
import React from 'react'
let PropTypes = React.PropTypes
import moment from 'moment'
moment.locale('es')

// Component
import Multiselect from 'react-widgets/lib/Multiselect'
import TablaLocalesMensual from './TablaLocalesMensual.jsx'

class ProgramacionMensual extends React.Component{
    constructor(props){
        super(props)
        // mostrar en el selector, los ultimos 12 meses
        let fechaActual = moment()
        let meses = [fechaActual.format('MM-YYYY')]
        for(let i=0; i<=11; i++)
            meses.push( fechaActual.subtract(1, 'month').format('MM-YYYY') )

        this.state = {
            meses,
            selectedMes: meses[0],
            selectedClientId: -1,
            locales: [],
            localesEnMultiselect: [],
            localesAgregados: []
        }
        this.mesSeleccionado = this.mesSeleccionado.bind(this)
        this.clienteSeleccionado = this.clienteSeleccionado.bind(this)
        this.localSelected = this.localSelected.bind(this)
        this.agregarLocales = this.agregarLocales.bind(this)
    }
    mesSeleccionado(event){
        this.setState({
            selectedMes: event.target.value
        })
    }
    clienteSeleccionado(event){
        // al seleccionar un cliente, se deben cargar los locales el el <select> de clientes
        let idClienteSeleccionado = event.target.value

        // buscamos el cliente seleccionado
        let selectedClient = this.props.clientes.find(cliente=>cliente.idCliente==idClienteSeleccionado)

        // lo marcados como seleccionado y mostramos la lista de locales
        this.setState({
            selectedClientId: idClienteSeleccionado,
            locales: selectedClient? selectedClient.locales : [],
            localesEnMultiselect: []
        })
    }
    localSelected(values){
        // actualizar la lista de locales seleccionados, guardandola en el state de la app
        this.setState({
            localesEnMultiselect: values
        })
    }
    agregarLocales(evt){
        evt.preventDefault()

        // los locales que se agregaran
        let localesAgregados = this.state.localesEnMultiselect
        this.tablaLocalesMensual.alertar("asdasd")

        this.setState({
            localesEnMultiselect: [],
            localesAgregados
        })
    }

    render(){
        return (
            <div>
                <h1>Programación mensual</h1>

                <form className="form" onSubmit={this.agregarLocales}>
                    <div className="row">
                        <h4 className="page-header">Agregar locales a la programación:</h4>
                    </div>
                    <div className="row">
                        {/* Cliente */}
                        <div className="col-sm-3 form-group">
                            <label className="control-label" htmlFor="cliente">Cliente</label>
                            <select className="form-control" name="cliente" value={this.state.selectedClientId} onChange={this.clienteSeleccionado}>
                                <option value="-1" disabled>--</option>
                                {this.props.clientes.map((cliente, index)=>{
                                    return <option key={index} value={cliente.idCliente}>{cliente.nombre}</option>
                                })}
                            </select>
                        </div>
                        {/*  Año / Mes */}
                        <div className="col-sm-3 form-group">
                            <label className="control-label" htmlFor="fechaProgramada">Mes</label>
                            <select className="form-control" name="fechaProgramada" value={this.state.selectedMes} onChange={this.mesSeleccionado}>
                                {this.state.meses.map((mes,i)=>{
                                    return <option key={i} value={mes}>{ moment(mes, 'MM-YYYY').format('MMMM YYYY')}</option>
                                })}
                            </select>
                        </div>
                        {/* Locales */}
                        <div className="col-sm-4 form-group">
                            <label className="control-label" htmlFor="locales">Locales</label>
                            <Multiselect
                                duration={0}                // sin animacion
                                defaultValue={[]}
                                value={this.state.localesEnMultiselect}
                                data={this.state.locales}
                                valueField="idLocal"        // el valor retornado es loca.idLocal
                                textField="nombre"          // el texto es local.nombre
                                filter="contains"           // muestra los locales que tengan la palabra buscada
                                onChange={this.localSelected}
                            />
                        </div>
                        {/* Boton Agregar */}
                        <div className="col-sm-2 form-group">
                            <label className="control-label">.</label>
                            <input type="submit" className="form-control btn btn-primary" value="Agregar locales"/>
                        </div>
                    </div>
                </form>

                <div className="row">
                    <h4 className="page-header">Locales programados:</h4>
                    <TablaLocalesMensual
                        // cada vez que se
                        localesAgregados={this.state.localesAgregados}
                        ref={ref=>this.tablaLocalesMensual=ref}

                    />
                </div>
            </div>
        )
    }
}

ProgramacionMensual.protoTypes = {
    clientes: PropTypes.array.isRequired
}

export default ProgramacionMensual