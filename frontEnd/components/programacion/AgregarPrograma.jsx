// Libs
import React from 'react'
// Styles
import style from './AgregarPrograma.css'

class AgregarPrograma extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            clienteSeleccionado: {},
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
        // seleccionar el primer mes por defecto
        this.props.onSeleccionarMes(this.props.meses[0].valor)
    }
    onSeleccionarMes(evt){
        this.props.onSeleccionarMes(evt.target.value)
    }

    setModoIngreso(modo){
       if(modo!==this.state.modoIngreso){
           this.setState({modoIngreso: this.state.modoIngreso==='manual'? 'excel' : 'manual'})
       }
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
            errores: {}
        })
    }
    inputNumeroLocalChanged(evt){
        this.setState({
            errores:{errorNumeroLocal: ''}
        })
    }
    /**
     * Enviar Manualmente
     */
    agregarLocalManualmente(evt) {
        evt.preventDefault()
        let idCliente = this.inputIdCliente.value
        let numeroLocal = this.inputNumeroLocal.value
        let annoMesDia = this.inputAnnoMesDia.value

        this.inputNumeroLocal.value = ''
        let [errores, objeto] = this.props.agregarInventario(idCliente, numeroLocal, annoMesDia)
        this.setState({
            errores: errores || {}
        })
    }

    /**
     * Enviar Al pegar
     */
    agregarLocalesAlPegar(event){
        event.preventDefault()

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
            let numerosLocales = rows.map(row=>{
                return row.trim().split('\t')[0]
            })


            let resultadoPegar = this.props.agregarGrupoInventarios(idCliente, numerosLocales, this.inputAnnoMesDia.value)
            // guardar el resultado de agregar los elementos
            this.setState({
                pegados: resultadoPegar
            })
            console.log('AgregarPrograma.... resultado:', resultadoPegar)
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
                <div className={'col-sm-2 form-group ' + (this.state.errores.errorIdCliente? 'has-error':'')}>
                    <label className="control-label" htmlFor="cliente">Cliente</label>
                    <select className="form-control"  name="cliente" defaultValue="-1" ref={ref=>this.inputIdCliente=ref} onChange={this.inputIdClienteChanged.bind(this)}>
                        <option value="-1" disabled>--</option>
                        {this.props.clientes.map((cliente, index)=>{
                            //return <option key={index} value={cliente.idCliente}>{"asdas" + cliente.nombre}</option
                            return <option key={index} value={cliente.idCliente}>{`${cliente.nombreCorto} - ${cliente.nombre}`}</option>
                        })}
                    </select>
                    <span className="help-block" style={{position:'absolute'}}>{this.state.errores.errorIdCliente}</span>
                </div>
                {/* Mes-Año  */}
                <div className="col-sm-2 form-group">
                    <label className="control-label" htmlFor="fechaProgramada">Mes</label>
                    <select className="form-control" name="fechaProgramada" ref={ref=>this.inputAnnoMesDia=ref} onChange={this.onSeleccionarMes.bind(this)}>
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
                        <input className="form-control" type="number" min='1' max='9999' name="locales" ref={ref=>this.inputNumeroLocal=ref} onChange={this.inputNumeroLocalChanged.bind(this)}  />
                        <span className="help-block" style={{position:'absolute'}}>{this.state.errores.errorNumeroLocal}</span>
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
                    <div className='col-sm-3 form-group '>
                        <label className="control-label" htmlFor="locales">Locales</label>
                        <input className="form-control" type="text" name="locales" placeholder="pegar aca los datos de Excel" onPaste={this.agregarLocalesAlPegar.bind(this)}/>

                        <p style={{marginTop: '1em'}}>Abra Excel, seleccione los datos de la columna <b>CECO</b>, <b>COPIE</b> el contendo y <b>PEGUELO</b> en la cuandro de texto.</p>
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
                                {this.state.pegados.pegadoConProblemas.map((error, index)=>{
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

AgregarPrograma.propTypes = {
    clientes: React.PropTypes.array.isRequired,
    meses: React.PropTypes.array.isRequired,
    // Metodos
    agregarInventario: React.PropTypes.func.isRequired,
    agregarGrupoInventarios: React.PropTypes.func.isRequired,
    onSeleccionarMes: React.PropTypes.func.isRequired
}

export default AgregarPrograma