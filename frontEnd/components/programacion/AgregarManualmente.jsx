// Libs
import React from 'react'
let PropTypes = React.PropTypes

class AgregarManualmente extends React.Component {
    constructor(props) {
        super(props)

        this.state = {
            inputIdCliente_error: '',
            inputNumeroLocal_error: '',
            locales: []
        }
        this.inputIdClienteChanged = this.inputIdClienteChanged.bind(this)
        this.inputNumeroLocalChanged = this.inputNumeroLocalChanged.bind(this)
        this.submitAgregarLocal = this.submitAgregarLocal.bind(this)
    }
    /**
     * Cambio en los campos del formulario
     */
    inputIdClienteChanged(event){
        // al seleccionar un cliente, se deben cargar los locales el el <select> de clientes
        let idClienteSeleccionado = event.target.value

        // buscamos el cliente seleccionado
        let selectedClient = this.props.clientes.find(cliente=>cliente.idCliente==idClienteSeleccionado)

        // lo marcados como seleccionado y mostramos la lista de locales
        this.setState({
            locales: selectedClient? selectedClient.locales : [],   // mostrar la lista de locales que tiene el cliente seleccionado
            inputIdCliente_error: '',
            inputNumeroLocal_error: '',
            // Todo: limpiar el campo de locales
            inputNumeroLocal: ''
        })
    }
    inputNumeroLocalChanged(event){
        this.setState({
            inputNumeroLocal_error: ''
        })
    }
    /**
     * Form Submit
     */
    submitAgregarLocal(evt){
        evt.preventDefault()

        // ToDo: validar que exista un cliente seleccionado
        if(this.inputIdCliente.value==="-1"){
            this.setState({
                inputIdCliente_error: 'Seleccione un Cliente de la lista'
            })
        }

        // validar que el local exista
        let numeroLocal = this.inputNumeroLocal.value
        let local = this.state.locales.find(local=>local.numero==numeroLocal)
        if(local===undefined){
            this.setState({
                inputNumeroLocal_error: numeroLocal===''? 'Ingrese un numero de local' : `El local '${numeroLocal}' no existe`
            })
            this.inputNumeroLocal.value = ''
            return
        }

        // limpiar el formulario
        let localCreado = this.props.onFormSubmit(local, this.inputMesAnno.value)
        this.setState({
            inputNumeroLocal_error: localCreado? '' : `El local ${numeroLocal} ya ha sido agendado`
        })
        this.inputNumeroLocal.value = ''


        console.log("FORMULARIO ENVIADO")
        console.log("cliente ", this.inputIdCliente.value, "  ", this.props.clientes[this.inputIdCliente.value-1].nombre)
        console.log(local)
    }

    render(){
        return <form className="form" onSubmit={this.submitAgregarLocal}>
            <div className="row">
                <h4 className="page-header" style={{marginTop: '1em'}}>Agregar locales a la programación:</h4>
            </div>
            <div className="row">
                {/* Cliente */}
                <div className={'col-sm-2 form-group ' + (this.state.inputIdCliente_error!==''? 'has-error':'')}>
                    <label className="control-label" htmlFor="cliente">Cliente</label>
                    <select className="form-control" name="cliente" defaultValue="-1" ref={ref=>this.inputIdCliente=ref} onChange={this.inputIdClienteChanged}>
                        <option value="-1" disabled>--</option>
                        {this.props.clientes.map((cliente, index)=>{
                            return <option key={index} value={cliente.idCliente}> {cliente.nombreCorto} - {cliente.nombre}</option>
                        })}
                    </select>
                    <span className="help-block">{this.state.inputIdCliente_error}</span>
                </div>
                {/* Mes-Año  */}
                <div className="col-sm-2 form-group">
                    <label className="control-label" htmlFor="fechaProgramada">Mes</label>
                    <select className="form-control" name="fechaProgramada" ref={ref=>this.inputMesAnno=ref}>
                        {this.props.meses.map((mes,i)=>{
                            return <option key={i} value={mes.valor}>{mes.texto}</option>
                        })}
                    </select>
                </div>
                {/* Locales */}
                <div className={'col-sm-2 form-group ' + (this.state.inputNumeroLocal_error!==''? 'has-error':'')}>
                    <label className="control-label" htmlFor="locales">Locales <span className="glyphicon glyphicon-paste"></span></label>
                    <input className="form-control" type="number" min='1' max='9999' name="locales" ref={ref=>this.inputNumeroLocal=ref} onChange={this.inputNumeroLocalChanged}  />
                    <span className="help-block">{this.state.inputNumeroLocal_error}</span>
                </div>
                {/* Boton Agregar */}
                <div className="col-sm-2 form-group">
                    <label className="control-label">.</label>
                    <input type="submit" className="form-control btn btn-primary" value="Agregar local"/>
                </div>
            </div>
        </form>
    }
}

export default AgregarManualmente

{/*<Multiselect
 duration={0}                // sin animacion
 defaultValue={[]}
 value={this.state.localesEnMultiselect}
 data={this.state.locales}
 valueField="idLocal"        // el valor retornado es loca.idLocal
 textField="nombre"          // el texto es local.nombre
 filter="contains"           // muestra los locales que tengan la palabra buscada
 onChange={this.localSelected}
 />*/}