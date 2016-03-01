// Libs
import React from 'react'
let PropTypes = React.PropTypes
// Styles
import style from './AgregarManualmente.css'


class AgregarManualmente extends React.Component {
    constructor(props) {
        super(props)

        this.state = {
            clienteSeleccionado: {},
            inputIdCliente_error: '',
            inputNumeroLocal_error: '',
            inputPegar_error: '',
            pegadoConProblemas: [],
            pegadoConteoTotal: 0,
            pegadoConteoCorrectos: 0,
            pegadoConteoProblemas: 0,
            locales: [],
            modoIngreso: 'manual' // 'manual'/'excel'
        }
        this.inputIdClienteChanged = this.inputIdClienteChanged.bind(this)
        this.inputNumeroLocalChanged = this.inputNumeroLocalChanged.bind(this)
        this.submitAgregarLocal = this.submitAgregarLocal.bind(this)
    }
    setModoIngreso(modo){
       if(modo!==this.state.modoIngreso){
           this.setState({
               modoIngreso: this.state.modoIngreso==='manual'? 'excel' : 'manual'
           })
       }
    }
    testOnPaste(event){
        event.preventDefault()

        // validar que exista un cliente seleccionado
        if(this.inputIdCliente.value==='-1'){
            this.setState({
                pegadoConProblemas: [],
                pegadoConteoTotal: 0,
                pegadoConteoCorrectos: 0,
                pegadoConteoProblemas: 0,
                inputIdCliente_error: 'Seleccione un Cliente',
                inputPegar_error: 'Seleccione un Cliente'
            })
            return
        }

        // cuando se pegue un elemento, separar separar las filas por el "line feed" '\n'
        event.clipboardData.items[0].getAsString(texto=>{
            // separar cada una de las filas '\n'
            let rows = texto.trim().split('\n')
            // quitar las filas vacias, y separar sus valores por el caracter tabulador
            rows = rows.filter(row=>row!=='')
            let celdas = rows.map(row=>row.trim().split('\t'))

            // Agregar los locales
            let resultado = celdas.map(row=>{
                let numeroLocal = row[0]

                // buscar el local
                let local = this.state.locales.find(local=>local.numero===numeroLocal)
                if(local){
                    // buscar los datos
                    let localCreado = this.props.onFormSubmit(local, this.inputMesAnno.value)
                    // cliente, local, estado final
                    return {
                        cliente: this.state.clienteSeleccionado.nombreCorto,
                        numeroLocal: numeroLocal,
                        mensaje: localCreado? 'Ok' : 'Ya agendado',
                        error: localCreado
                    }
                }else{
                    return {
                        cliente: this.state.clienteSeleccionado.nombreCorto,
                        numeroLocal: numeroLocal,
                        mensaje: 'No existe',
                        error: true
                    }
                }
            })
            // quitar los que fueron correctamente agregados
            let pegadoConProblemas = resultado.filter(res=> res.mensaje!=='Ok')

            // guardar el resultado de agregar los elementos
            this.setState({
                pegadoConProblemas,
                pegadoConteoTotal: resultado.length,
                pegadoConteoCorrectos: resultado.length - pegadoConProblemas.length,
                pegadoConteoProblemas: pegadoConProblemas.length
            })
        })
    }


    /**
     * Cambio en los campos del formulario
     */
    inputIdClienteChanged(event){
        // al seleccionar un cliente, se deben cargar los locales el el <select> de clientes
        let idClienteSeleccionado = event.target.value

        // buscamos el cliente seleccionado
        let cliente = this.props.clientes.find(cliente=>cliente.idCliente==idClienteSeleccionado)

        // lo marcados como seleccionado y mostramos la lista de locales
        this.setState({
            clienteSeleccionado: cliente? cliente: {},
            locales: cliente? cliente.locales : [],   // mostrar la lista de locales que tiene el cliente seleccionado
            inputIdCliente_error: '',
            inputNumeroLocal_error: '',
            inputPegar_error: '',
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

        // validar que exista un cliente seleccionado
        if(this.inputIdCliente.value==='-1'){
            this.setState({
                inputIdCliente_error: 'Seleccione un Cliente'
            })
        }

        // validar que el local exista
        let numeroLocal = this.inputNumeroLocal.value
        let local = this.state.locales.find(local=>local.numero==numeroLocal)
        if(local===undefined){
            this.setState({
                inputNumeroLocal_error: numeroLocal===''? 'Digite un numero de local' : `El local '${numeroLocal}' no existe`
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
        console.log(local)
        console.log(this.state.clienteSeleccionado)
    }

    render(){
        return <div className="form">
            <div className="row">
                <h4 className="page-header" style={{marginTop: '1em'}}>Agregar locales a la programación:</h4>
            </div>
            <div className="row" style={{marginBottom: '1em'}}>
                <div className='col-sm-offset-4 col-sm-3'>
                    <button type="button" className={'btn btn-sm btn-default '+(this.state.modoIngreso==='manual'? 'active':'')} onClick={this.setModoIngreso.bind(this, 'manual')}>Manualmente</button>
                    <button type="button" className={'btn btn-sm btn-default '+(this.state.modoIngreso==='excel'? 'active':'')} onClick={this.setModoIngreso.bind(this, 'excel')}>Desde Excel</button>
                </div>
            </div>

            <div className="row">
                {/* Cliente */}
                <div className={'col-sm-2 form-group ' + (this.state.inputIdCliente_error!==''? 'has-error':'')}>
                    <label className="control-label" htmlFor="cliente">Cliente</label>
                    <select className="form-control" name="cliente" defaultValue="-1" ref={ref=>this.inputIdCliente=ref} onChange={this.inputIdClienteChanged}>
                        <option value="-1" disabled>--</option>
                        {this.props.clientes.map((cliente, index)=>{
                            //return <option key={index} value={cliente.idCliente}>{"asdas" + cliente.nombre}</option
                            return <option key={index} value={cliente.idCliente}>{`${cliente.nombreCorto} - ${cliente.nombre}`}</option>
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

                {/* ========= OPCIONES PARA AGRAGAR MANUALMENTE ========= */}
                <form className="form" onSubmit={this.submitAgregarLocal} style={{display: this.state.modoIngreso==='manual'? '':'none'}}>
                    {/* Locales */}
                    <div className={'col-sm-3 form-group ' + (this.state.inputNumeroLocal_error!==''? 'has-error':'')}>
                        <label className="control-label" htmlFor="locales">Local</label>
                        <input className="form-control" type="number" min='1' max='9999' name="locales" ref={ref=>this.inputNumeroLocal=ref} onChange={this.inputNumeroLocalChanged}  />
                        <span className="help-block">{this.state.inputNumeroLocal_error}</span>
                    </div>
                    {/* Boton Agregar */}
                    <div className="col-sm-2 form-group">
                        <label className="control-label">{'\u00A0'}</label>
                        <input type="submit" className="form-control btn btn-primary" value="Agregar local"/>
                    </div>
                </form>

                {/* ========= OPCIONES PARA AGREGAR DESDE EXCEL ========= */}
                <div style={{display: this.state.modoIngreso==='excel'? '':'none'}}>
                    {/* Locales */}
                    <div className={'col-sm-3 form-group ' + (this.state.inputPegar_error!==''? 'has-error':'')}>
                        <label className="control-label" htmlFor="locales">Locales</label>
                        <input className="form-control" type="text" name="locales" placeholder="pegar aca los datos de Excel" onPaste={this.testOnPaste.bind(this)}/>
                        <span className="help-block">{this.state.inputPegar_error}</span>

                        <p style={{marginTop: '1em'}}>Abra Excel, seleccione los datos de la columna <b>CECO</b>, <b>COPIE</b> el contendo y <b>PEGUELO</b> en la cuandro de texto.</p>
                    </div>

                    {/* Pegar datos */}
                    <div className={'col-sm-5 form-group '}>
                        <div className="col-sm-5">
                            <label className="control-label" htmlFor="locales">Resultado</label>
                            <table className="table table-bordered table-condensed">
                                <tbody>
                                <tr><td><small>Correctos</small></td><td>{this.state.pegadoConteoCorrectos}</td></tr>
                                <tr><td><small>Con Problemas</small></td><td>{this.state.pegadoConteoProblemas}</td></tr>
                                <tr><td><small>TOTAL</small></td><td><b>{this.state.pegadoConteoTotal}</b></td></tr>
                                </tbody>
                            </table>
                        </div>

                        <div className={"col-sm-7 " + (this.state.pegadoConteoTotal<=0? 'hide':'')}>
                            <label className="control-label" htmlFor="locales">Detalle</label>
                            <table className="table table-bordered table-condensed">
                                <thead>
                                <tr><th>Cliente</th><th>CECO</th><th>Estado</th></tr>
                                </thead>
                                <tbody>
                                {this.state.pegadoConProblemas.map((res, index)=>{
                                    return(
                                        <tr key={index}>
                                            <td className={style.tableCell}>
                                                <p>{res.cliente}</p>
                                            </td>
                                            <td className={style.tableCell}>
                                                <p>{res.numeroLocal}</p>
                                            </td>
                                            <td className={style.tableCell}>
                                                <p><small>{res.mensaje}</small></p>
                                            </td>
                                        </tr>
                                    )
                                })}
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}

export default AgregarManualmente