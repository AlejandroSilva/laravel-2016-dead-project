// Libs
import React from 'react'
// Styles
import * as style from './FormularioAgregarAuditoria.css'

export class FormularioAgregarAuditoria extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            idClienteSeleccionado: 2,
            mesSeleccionado: this.props.meses[0].valor,
            locales: [],
            errores:{
                errorIdCliente: '',
                errorNumeroLocal: ''
            },
            pegados: {
                pegadoConProblemas: [],
                conteoTotal: 0,
                conteoCorrectos: 0,
                conteoProblemas: 0
            },
            inputPegar_error: '',
            modoIngreso: 'manual' // 'manual'/'excel'
        }
    }
    componentWillMount(){
        this.props.buscarAuditorias(this.state.mesSeleccionado, this.state.idClienteSeleccionado)
    }

    /**
     * Cambio en los campos del formulario
     */
    setModoIngreso(modo){
       if(modo!==this.state.modoIngreso){
           this.setState({modoIngreso: this.state.modoIngreso==='manual'? 'excel' : 'manual'})
       }
    }
    selectMesChanged(evt){
        let annoMesDia = evt.target.value
        this.props.buscarAuditorias(annoMesDia, this.state.idClienteSeleccionado)
        this.setState({
            mesSeleccionado: annoMesDia
        })
    }
    selectClienteChanged(event){
        // al seleccionar un cliente, se deben cargar los locales el el <select> de clientes
        let idClienteSeleccionado = event.target.value

        // descargar las auditorias del cliente
        this.props.buscarAuditorias(this.state.mesSeleccionado, idClienteSeleccionado)

        // buscamos el cliente seleccionado
        let cliente = this.props.clientes.find(cliente=>cliente.idCliente==idClienteSeleccionado)

        // lo marcados como seleccionado y mostramos la lista de locales
        this.setState({
            idClienteSeleccionado: idClienteSeleccionado,
            locales: cliente? cliente.locales : [],   // mostrar la lista de locales que tiene el cliente seleccionado
            errores: {}
        })
    }
    inputNumeroLocalChanged(evt){
        this.setState({
            errores:{errorNumeroLocal: ''}
        })
    }
    getOpcionesSeleccionadas(){
        return {
            idCliente: this.state.idClienteSeleccionado,
            mes: this.state.mesSeleccionado
        }
    }
    
    /**
     * Agregar Locales
     */
    agregarLocalManualmente(evt) {
        evt.preventDefault()
        if(!this.props.puedeAgregar)
            return alert("no tiene los permisos necesarios para agregar una auditoria")

        let idCliente = this.inputIdCliente.value
        let numeroLocal = this.inputNumeroLocal.value
        let annoMesDia = this.inputAnnoMesDia.value

        this.inputNumeroLocal.value = ''
        let [errores, objeto] = this.props.agregarAuditoria(idCliente, numeroLocal, annoMesDia)
        this.setState({
            errores: errores || {}
        })
    }
    agregarLocalesAlPegar(event){
        event.preventDefault()
        if(!this.props.puedeAgregar)
            return alert("no tiene los permisos necesarios para agregar una auditoria")

        // validar que exista un cliente seleccionado
        let idCliente = this.inputIdCliente.value
        if(idCliente==='-1'){
            this.setState({
                pegados: {
                    conteoTotal: 0,
                    conteoCorrectos: 0,
                    conteoProblemas: 0,
                    pegadoConProblemas: []
                },
                errores:{
                    errorIdCliente: 'Seleccione un Cliente'
                },
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
            let datosAuditorias = rows.map(row=>{
                let datos = row.trim().split('\t')
                return {
                    idCliente: idCliente,
                    fecha: datos[0],
                    ceco: datos[1],
                    idAuditor: datos[2],
                }
            })
            
            let resultadoPegar = this.props.agregarGrupoInventarios(datosAuditorias)
            // guardar el resultado de agregar los elementos
            this.setState({
                pegados: resultadoPegar
            })
            console.log('FormularioAgregarAuditoria.... resultado:', resultadoPegar)
        })
    }

    limpiarProblemas(){
        this.setState({
            pegados: {
                conteoTotal: 0,
                conteoCorrectos: 0,
                conteoProblemas: 0,
                pegadoConProblemas: []
            },
            inputPegar_error: ''
        })
    }

    render(){
        return <div className="form">
            <div className="row" style={{marginBottom: '1em'}}>
                <div className='col-sm-offset-4 col-sm-3'>
                    <button type="button" className={'btn btn-sm btn-default '+(this.state.modoIngreso==='manual'? 'active':'')} onClick={this.setModoIngreso.bind(this, 'manual')}>Manualmente</button>
                    <button type="button" className={'btn btn-sm btn-default '+(this.state.modoIngreso==='excel'? 'active':'')} onClick={this.setModoIngreso.bind(this, 'excel')}>Desde Excel</button>
                </div>
            </div>

            <div className="row">
                {/* Cliente */}
                <div className={'col-sm-2 form-group ' + (this.state.errores.errorIdCliente? 'has-error':'')}>
                    <label className="control-label" htmlFor="cliente">Cliente</label>
                    <select className="form-control"  name="cliente"
                            ref={ref=>this.inputIdCliente=ref}
                            value={this.state.idClienteSeleccionado}
                            onChange={this.selectClienteChanged.bind(this)}>
                        <option value="0">Todos</option>
                        {this.props.clientes.map((cliente, index)=>{
                            return <option key={index} value={cliente.idCliente}>{`${cliente.nombreCorto} - ${cliente.nombre}`}</option>
                        })}
                    </select>
                    <span className="help-block" style={{position:'absolute'}}>{this.state.errores.errorIdCliente}</span>
                </div>
                {/* Mes-Año  */}
                <div className="col-sm-2 form-group">
                    <label className="control-label" htmlFor="fechaProgramada">Mes</label>
                    <select className="form-control" name="fechaProgramada"
                            ref={ref=>this.inputAnnoMesDia=ref}
                            value={this.state.mesSeleccionado}
                            onChange={this.selectMesChanged.bind(this)}>
                        {this.props.meses.map((mes,i)=>{
                            return <option key={i} value={mes.valor}>{mes.texto}</option>
                        })}
                    </select>
                </div>

                {/* ========= OPCIONES PARA AGREGAR MANUALMENTE ========= */}
                <form className="form" onSubmit={this.agregarLocalManualmente.bind(this)} style={{display: this.state.modoIngreso==='manual'? '':'none'}}>
                    {/* Locales */}
                    <div className={'col-sm-3 form-group ' + (this.state.errores.errorNumeroLocal? 'has-error':'')}>
                        <label className="control-label" htmlFor="locales">Local</label>
                        <input className="form-control" type="number" min='1' max='9999' name="locales"
                               ref={ref=>this.inputNumeroLocal=ref}
                               onChange={this.inputNumeroLocalChanged.bind(this)}
                               disabled={this.props.puedeAgregar? '':'disabled'}/>
                        <span className="help-block" style={{position:'absolute'}}>{this.state.errores.errorNumeroLocal}</span>
                    </div>
                    {/* Boton Agregar */}
                    <div className="col-sm-2 form-group">
                        <label className="control-label">{'\u00A0'}</label>
                        <input type="submit" className="form-control btn btn-primary" value="Agregar local"
                               disabled={this.props.puedeAgregar? '':'disabled'}/>
                    </div>
                </form>

                {/* ========= OPCIONES PARA AGREGAR DESDE EXCEL ========= */}
                <div style={{display: this.state.modoIngreso==='excel'? '':'none'}}>
                    {/* Locales */}
                    <div className='col-sm-3 form-group '>
                        <label className="control-label" htmlFor="locales">Locales</label>
                        <input className="form-control" type="text" name="locales" placeholder="pegar aca los datos de Excel"
                               onPaste={this.agregarLocalesAlPegar.bind(this)}
                               disabled={this.props.puedeAgregar? '':'disabled'}/>
                        <p style={{marginTop: '1em'}}>
                            Abra Excel, seleccione los datos de las columnas <b>Fecha</b> y <b>CECO</b>, <b>COPIE</b> el contendo y <b>PEGUELO</b> en la cuandro de texto.
                            La fecha debe tener el formato <b>AAAA-MM-DD</b>.
                        </p>
                    </div>

                    {/* Pegar datos */}
                    <div className={'col-sm-5 form-group '+(this.state.pegados.conteoTotal==0? 'hide':'')}>
                        <div className="col-sm-5">
                            <label className="control-label" htmlFor="locales">Resultado</label>
                            <table className="table table-bordered table-condensed">
                                <tbody>
                                <tr><td><small>Correctos</small></td><td>{this.state.pegados.conteoCorrectos}</td></tr>
                                <tr><td><small>Con Problemas</small></td><td>{this.state.pegados.conteoProblemas}</td></tr>
                                <tr><td><small>TOTAL</small></td><td><b>{this.state.pegados.conteoTotal}</b></td></tr>
                                </tbody>
                            </table>
                        </div>

                        <div className={"col-sm-7 "}>
                            <label className="control-label" htmlFor="locales">Detalle</label>
                            <button className="btn btn-xs btn-primary pull-right" tabIndex="-1"onClick={this.limpiarProblemas.bind(this)}>
                                Aceptar/Ocultar
                            </button>
                            <table className="table table-bordered table-condensed">
                                <thead>
                                <tr><th>CECO</th><th>Estado</th></tr>
                                </thead>
                                <tbody>
                                {this.state.pegados.pegadoConProblemas.length===0
                                    ? <tr><td colSpan="2">Sin problemas detectados</td></tr>
                                    : this.state.pegados.pegadoConProblemas.map((error, index)=>{
                                        return(
                                            <tr key={index}>
                                                {/*
                                                <td className={style.tableCell}>
                                                    <p>{error.cliente}</p>
                                                </td>
                                                */}
                                                <td className={style.tableCell}>
                                                    <p>{error.numeroLocal}</p>
                                                </td>
                                                <td className={style.tableCell}>
                                                    <p><small>{error.errorIdCliente || error.errorNumeroLocal}</small></p>
                                                </td>
                                            </tr>
                                        )
                                    })
                                }
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}

FormularioAgregarAuditoria.propTypes = {
    puedeAgregar: React.PropTypes.bool.isRequired,
    clientes: React.PropTypes.array.isRequired,
    meses: React.PropTypes.array.isRequired,
    // Metodos
    agregarAuditoria: React.PropTypes.func.isRequired,
    agregarGrupoInventarios: React.PropTypes.func.isRequired,
    buscarAuditorias: React.PropTypes.func.isRequired
}