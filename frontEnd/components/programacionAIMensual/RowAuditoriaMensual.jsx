import React from 'react'
import numeral from 'numeral'
import moment from 'moment'
moment.locale('es')

// Componentes
import Tooltip from 'react-bootstrap/lib/Tooltip'
import OverlayTrigger from 'react-bootstrap/lib/OverlayTrigger'
import Select from './Select.jsx'

// Styles
import * as cssTabla from './TablaMensualAI.css'
import * as cssInput from './Inputs.css'

class RowAuditoriaMensual extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            inputDia: 0,
            inputMes: 0,
            inputAnno: 0,
            // inputDotacion: 0,
            // selectJornada: 4,
            diaValido: this._diaValido(0),
            mesValido: this._mesValido(0)
            // dotacionValida: this._dotacionValida(0)
        }
        // Refs disponibles: this.inputDia, this.inputDotacion
    }

    componentWillMount(){
        // al montar el componente, fijas su state "inicial"
        let [anno, mes, dia] = this.props.auditoria.fechaProgramada.split('-')
        // let dotacionSugerida = this.props.auditoria.local.dotacionSugerida
        // let dotacionAsignadaTotal = this.props.auditoria.dotacionAsignadaTotal
        // let jornadaInventario = this.props.auditoria.idJornada
        // let jornadaLocal = this.props.auditoria.local.idJornadaSugerida
        this.setState({
            inputDia: dia,
            inputMes: mes,
            inputAnno: anno,
            // inputDotacion: dotacionAsignadaTotal || dotacionSugerida,
            // selectJornada: jornadaInventario || jornadaLocal,
            diaValido: this._diaValido(dia),
            mesValido: this._mesValido(mes)
            // dotacionValida: this._dotacionValida(dotacionAsignadaTotal || dotacionSugerida)
        })
    }

    componentWillReceiveProps(nextProps){
/*
        let mismoInventario = this.props.auditoria.idDummy===nextProps.inventario.idDummy
        if(mismoInventario){
            //if(this.props.auditoria.idDummy==9) console.log("actualizando componente 9", this.props.auditoria.fechaProgramada, nextProps.inventario.fechaProgramada)
            //console.log("nuevas props para ", this.props.auditoria.idDummy)

            // Si es el mismo inventario, se revisa si se han actualizado los datos (y se reemplaza el state actual del usuario)

            // se recibio una nueva fecha?
            //let [anno1, mes1, dia1] = this.props.auditoria.fechaProgramada.split('-')
            let [anno2, mes2, dia2] = nextProps.inventario.fechaProgramada.split('-')
            this.setState({
                inputDia: dia2, inputMes: mes2, inputAnno: anno2,
                diaValido: this._diaValido(dia2), mesValido: this._mesValido(mes2)
            })

            // se recibio una nueva dotacion?
            //let dotacion1 = this.props.auditoria.dotacionAsignadaTotal || this.props.auditoria.local.dotacionSugerida
            let dotacion2 = nextProps.inventario.dotacionAsignadaTotal || nextProps.inventario.local.dotacionSugerida

            //if(dotacion1!==dotacion2 || this.state.inputDotacion!==dotacion2)
            this.setState({inputDotacion: dotacion2, dotacionValida: this._dotacionValida(dotacion2)})


            // se recibio una nueva jornada?
            //let jornada1 = this.props.auditoria.idJornada || this.props.auditoria.local.idJornadaSugerida
            let jornada2 = nextProps.inventario.idJornada || nextProps.inventario.local.idJornadaSugerida
            this.setState({selectJornada: jornada2})

        }else{
*/
            // Si el inventario cambio, se vuelven a poner los valores "por defecto"
            let [anno, mes, dia] = nextProps.auditoria.fechaProgramada.split('-')
            // let dotacionAsignadaTotal = nextProps.inventario.dotacionAsignadaTotal
            // let dotacionSugerida = nextProps.inventario.local.dotacionSugerida
            // let jornadaInventario = nextProps.inventario.idJornada
            // let jornadaLocal = nextProps.inventario.local.idJornadaSugerida
            this.setState({
                inputDia: dia,
                inputMes: mes,
                inputAnno: anno,
                // inputDotacion: dotacionAsignadaTotal || dotacionSugerida,
                // selectJornada: jornadaInventario || jornadaLocal,
                diaValido: this._diaValido(dia),
                mesValido: this._mesValido(mes)
                // dotacionValida: this._dotacionValida(dotacionAsignadaTotal || dotacionSugerida)
            })
/*        }*/
    }

    focusElemento(elemento){
        if(elemento==='dia'){
            this.inputDia.focus()
        }
        // else if(elemento==='dotacion'){
        //     this.inputDotacion.focus()
        // }
    }
    inputOnKeyDown(elemento, evt){
        if((evt.keyCode===9 && evt.shiftKey===false) || evt.keyCode===40 || evt.keyCode===13){
            // 9 = tab, flechaAbajo = 40,  13 = enter
            evt.preventDefault()
            this.props.focusFilaSiguiente(this.props.index, elemento)

        }else if((evt.keyCode===9 && evt.shiftKey===true) || evt.keyCode===38) {
            // flechaArriba = 38, shift+tab
            this.props.focusFilaAnterior(this.props.index, elemento)
            evt.preventDefault()
        }
    }

    onInputDiaChange(evt){
        let dia = evt.target.value
        this.setState({
            inputDia: dia,
            diaValido: this._diaValido(dia)
        })
    }
    onInputMesChange(evt){
        let mes = evt.target.value
        this.setState({
            inputMes: mes,
            mesValido: this._mesValido(mes)
        })
    }
    // onInputDotacionChange(evt){
    //     let dotacion = evt.target.value
    //     this.setState({
    //         inputDotacion: dotacion,
    //         dotacionValida: this._dotacionValida(dotacion)
    //     })
    // }
    // onSelectJornadaChange(evt){
    //     let jornada = evt.target.value
    //     this.setState({selectJornada: jornada}, ()=>{
    //         this.guardarAuditoria()
    //     })
    // }

    _diaValido(dia){
        return dia>0 && dia<32
    }
    _mesValido(mes){
        return mes>=1 && mes<=12
    }
    // _dotacionValida(dotacion){
    //     return dotacion>0
    // }
    isDirty(){
        let [anno, mes, dia] = this.props.auditoria.fechaProgramada.split('-')
        let isDirty = (
            this.state.inputDia!=dia ||
            this.state.inputMes!=mes
            // this.state.inputDotacion!=(this.props.auditoria.dotacionAsignadaTotal || this.props.auditoria.local.dotacionSugerida) ||
            // this.state.selectJornada!=(this.props.auditoria.idJornada || this.props.auditoria.local.idJornadaSugerida)
        )
        console.log("isDirty ", isDirty)
        return isDirty
    }
    guardarAuditoria(){
        if(!this.props.puedeModificar)
            return alert('no tiene permisos para realizar esta acción')

        let cambiosAuditoria = {}

        // la FECHA es valida y ha cambiado?
        console.log(this.state.diaValido, this.state.mesValido, this.isDirty())
        if(this.state.diaValido && this.state.mesValido && this.isDirty()) {
            let anno = this.state.inputAnno
            let mes = this.state.inputMes
            let dia = this.state.inputDia
            cambiosAuditoria.fechaProgramada = `${anno}-${mes}-${dia}`
        }

        // el AUDITOR ha cambiado?
        let estadoSelectAuditor = this.selectAuditor.getEstado()
        if(estadoSelectAuditor.dirty)
            cambiosAuditoria.idAuditor = estadoSelectAuditor.seleccionUsuario

        // almenos uno de los ementos debe estar "dirty" para guardar los cambios
        if(JSON.stringify(cambiosAuditoria)!=='{}'){
            console.log('cambiosAuditoria ', cambiosAuditoria)
            this.props.actualizarAuditoria(this.props.auditoria.idAuditoria, cambiosAuditoria, this.props.auditoria.idDummy)
        }else{
            console.log('auditoria sin cambios, no se actualiza')
        }
    }
    quitarAuditoria(){
        this.props.quitarAuditoria(this.props.auditoria.idDummy)
    }
    eliminarAuditoria(){
        if(!this.props.puedeModificar)
            return alert('no tiene permisos para realizar esta acción')

        this.props.eliminarAuditoria(this.props.auditoria)
    }

    render(){
        let diaSemana = moment(this.props.auditoria.fechaProgramada).format('dddd')
        const auditores = this.props.auditores.map(usuario=>{
            return {valor: usuario.id, texto:`${usuario.nombre1} ${usuario.apellidoPaterno}`}
        })

        return (
            <tr className={this.props.mostrarSeparador? cssTabla.trSeparador: ''}>
                {/* Correlativo */}
                <td className={cssTabla.tdCorrelativo}>
                    {this.props.index}
                </td>
                {/* Fecha */}
                <td className={cssTabla.tdFecha}>
                    <div className='pull-right'>
                        <p className={cssInput.diaSemana}>{diaSemana==='Invalid date'? '': diaSemana}</p>
                        <input className={this.state.diaValido? cssInput.inputDia : cssInput.inputDiaInvalido}
                               type="number" min={0} max={31}
                               ref={ref=>this.inputDia=ref}
                               value={this.state.inputDia}
                               onChange={this.onInputDiaChange.bind(this)}
                               onKeyDown={this.inputOnKeyDown.bind(this, 'dia')}
                               onBlur={this.guardarAuditoria.bind(this)}
                               onFocus={()=>{this.inputDia.select()}}
                               disabled={this.props.puedeModificar? '':'disabled'}/>
                        <input className={cssInput.inputMes} type="number"
                               className={this.state.mesValido? cssInput.inputDia : cssInput.inputDiaInvalido}
                               type="number" min={1} max={12}
                               ref={ref=>this.inputMes=ref}
                               value={this.state.inputMes}
                               disabled/>
                        <input className={cssInput.inputAnno} type="number"
                               value={this.state.inputAnno}
                               disabled/>
                    </div>
                </td>
                {/* Cliente*/}
                <td className={cssTabla.tdCliente}>
                    <p><small>{this.props.auditoria.local.cliente.nombreCorto}</small></p>
                </td>
                {/* CECO */}
                <td className={cssTabla.tdCeco}>
                    <p><small><b>{this.props.auditoria.local.numero}</b></small></p>
                </td>
                {/* Region*/}
                <td className={cssTabla.tdRegion}>
                    <p style={{margin:0}}><small>{ this.props.auditoria.local.direccion.comuna.provincia.region.numero }</small></p>
                </td>
                {/* Comuna */}
                <td className={cssTabla.tdComuna}>
                    <p style={{margin:0}}><b><small>{ this.props.auditoria.local.direccion.comuna.nombre }</small></b></p>
                </td>
                {/* Local */}
                <td className={cssTabla.tdLocal}>
                    <p><small><b>{this.props.auditoria.local.nombre}</b></small></p>
                </td>

                {/* Stock */}
                 <td className={cssTabla.tdStock}>
                     <OverlayTrigger
                         placement="left"
                         delay={0}
                         overlay={<Tooltip id="yyy">{'Stock al '+(this.props.auditoria.local.fechaStock)}</Tooltip>}>
                         <p><small>{numeral(this.props.auditoria.local.stock).format('0,0')}</small></p>
                     </OverlayTrigger>
                 </td>

                {/* Auditor */}
                <td className={cssTabla.tdLider}>
                    <Select
                            ref={ref=>this.selectAuditor=ref}
                            seleccionada={ this.props.auditoria.idAuditor || ''}
                            onSelect={this.guardarAuditoria.bind(this)}
                            opciones={auditores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                            puedeModificar={this.props.puedeModificar}
                    />
                </td>
                {/* Hora de Apertura del local */}
                <td className={cssTabla.tdAperturaCierre}>
                    <input type="time"
                           value={this.props.auditoria.local.horaApertura}
                           disabled/>
                </td>
                {/* hora de Cierre de local */}
                <td className={cssTabla.tdAperturaCierre}>
                    <input type="time"
                           value={this.props.auditoria.local.horaCierre}
                           disabled/>
                </td>

                {/* Direccion */}
                <td className={cssTabla.tdJornada}>
                    <p>{this.props.auditoria.local.direccion.direccion}</p>
                </td>
                {/* Opciones    */}
                <td className={cssTabla.tdOpciones}>
                    {this.props.puedeModificar===true?
                        <button className="btn btn-xs btn-primary"
                                tabIndex="-1"
                                onClick={this.eliminarAuditoria.bind(this)}>
                            Eliminar
                        </button>
                        :
                        null
                    }
                </td>
            </tr>
        )
    }
}

RowAuditoriaMensual.propTypes = {
    // Objetos
    puedeModificar: React.PropTypes.bool.isRequired,
    index: React.PropTypes.number.isRequired,
    auditoria: React.PropTypes.object.isRequired,
    mostrarSeparador: React.PropTypes.bool.isRequired,
    auditores: React.PropTypes.array.isRequired,
    // Metodos
    focusFilaSiguiente: React.PropTypes.func.isRequired,
    focusFilaAnterior: React.PropTypes.func.isRequired,
    actualizarAuditoria: React.PropTypes.func.isRequired,
    quitarAuditoria: React.PropTypes.func.isRequired,
    eliminarAuditoria: React.PropTypes.func.isRequired
}
RowAuditoriaMensual.defaultProps = {
    mostrarSeparador: false
}

export default RowAuditoriaMensual