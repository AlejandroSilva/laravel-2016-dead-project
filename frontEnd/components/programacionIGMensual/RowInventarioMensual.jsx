import React from 'react'
import numeral from 'numeral'
import moment from 'moment'
moment.locale('es')

// Componentes
import Tooltip from 'react-bootstrap/lib/Tooltip'
import OverlayTrigger from 'react-bootstrap/lib/OverlayTrigger'

// Styles
import * as cssTabla from './TablaMensual.css'
import * as cssInput from './Inputs.css'

class RowInventarioMensual extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            inputDia: 0,
            inputMes: 0,
            inputAnno: 0,
            inputDotacion: 0,
            selectJornada: 4,
            diaValido: this._diaValido(0),
            mesValido: this._mesValido(0),
            dotacionValida: this._dotacionValida(0)
        }
        // Refs disponibles: this.inputDia, this.inputDotacion
    }

    componentWillMount(){
        // al montar el componente, fijas su state "inicial"
        let [anno, mes, dia] = this.props.inventario.fechaProgramada.split('-')
        let dotacionSugerida = this.props.inventario.local.dotacionSugerida
        let dotacionAsignadaTotal = this.props.inventario.dotacionAsignadaTotal
        let jornadaInventario = this.props.inventario.idJornada
        let jornadaLocal = this.props.inventario.local.idJornadaSugerida
        this.setState({
            inputDia: dia,
            inputMes: mes,
            inputAnno: anno,
            inputDotacion: dotacionAsignadaTotal || dotacionSugerida,
            selectJornada: jornadaInventario || jornadaLocal,
            diaValido: this._diaValido(dia),
            mesValido: this._mesValido(mes),
            dotacionValida: this._dotacionValida(dotacionAsignadaTotal || dotacionSugerida)
        })
    }

    componentWillReceiveProps(nextProps){
      // Si el inventario cambio, se vuelven a poner los valores "por defecto"
        let [anno, mes, dia] = nextProps.inventario.fechaProgramada.split('-')
        let dotacionAsignadaTotal = nextProps.inventario.dotacionAsignadaTotal
        let dotacionSugerida = nextProps.inventario.local.dotacionSugerida
        let jornadaInventario = nextProps.inventario.idJornada
        let jornadaLocal = nextProps.inventario.local.idJornadaSugerida
        this.setState({
            inputDia: dia,
            inputMes: mes,
            inputAnno: anno,
            inputDotacion: dotacionAsignadaTotal || dotacionSugerida,
            selectJornada: jornadaInventario || jornadaLocal,
            diaValido: this._diaValido(dia),
            mesValido: this._mesValido(mes),
            dotacionValida: this._dotacionValida(dotacionAsignadaTotal || dotacionSugerida)
        })
    }

    focusElemento(elemento){
        if(elemento==='dia'){
            this.inputDia.focus()
        }else if(elemento==='dotacion'){
            this.inputDotacion.focus()
        }
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
    onInputDotacionChange(evt){
        let dotacion = evt.target.value
        this.setState({
            inputDotacion: dotacion,
            dotacionValida: this._dotacionValida(dotacion)
        })
    }
    onSelectJornadaChange(evt){
        let jornada = evt.target.value
        this.setState({selectJornada: jornada}, ()=>{
            this.guardarOCrear()
        })
    }

    _diaValido(dia){
        return dia>0 && dia<32
    }
    _mesValido(mes){
        console.log(mes, mes>=1, mes<=12)
        return mes>=1 && mes<=12
    }
    _dotacionValida(dotacion){
        return dotacion>0
    }
    isDirty(){
        let [anno, mes, dia] = this.props.inventario.fechaProgramada.split('-')
        let isDirty = (
            this.state.inputDia!=dia ||
            this.state.inputMes!=mes ||
            this.state.inputDotacion!=(this.props.inventario.dotacionAsignadaTotal || this.props.inventario.local.dotacionSugerida) ||
            this.state.selectJornada!=(this.props.inventario.idJornada || this.props.inventario.local.idJornadaSugerida)
        )
        console.log("isDirty ", isDirty)
        return isDirty
    }
    guardarOCrear(){
        if(!this.props.puedeModificar)
            return alert("no tiene permitido modificar el inventario")

        // si no es dirty (no hay cambios) no se hace nada
        if(this.isDirty()===false)
            return

        let dotacion = this.state.inputDotacion
        let jornada = this.state.selectJornada
        let anno = this.state.inputAnno
        let mes = this.state.inputMes
        let dia = this.state.inputDia

        if(this.state.diaValido && this.state.mesValido && this.state.dotacionValida){
            let fecha = `${anno}-${mes}-${dia}`

            this.props.guardarOCrearInventario({
                idInventario: this.props.inventario.idInventario,
                idDummy: this.props.inventario.idDummy,
                idLocal: this.props.inventario.local.idLocal,
                idJornada: jornada,
                fechaProgramada: fecha,
                //horaLlegada: this.props.inventario.horaLlegada || this.props.inventario.local.horaLlegadaSugerida,
                stockTeorico: this.props.inventario.local.stock,
                dotacionAsignadaTotal: dotacion
            })
        }else{
            console.log('datos invalidos')
        }
    }
    quitarInventario(){
        this.props.quitarInventario(this.props.inventario.idDummy)
    }
    eliminarInventario(){
        if(!this.props.puedeModificar)
            return alert("no tiene permitido eliminar el inventario")
        this.props.eliminarInventario(this.props.inventario)
    }

    render(){
        let nombreCliente = this.props.inventario.local.nombreCliente || this.props.inventario.local.cliente.nombreCorto
        let nombreRegion = this.props.inventario.local.nombreRegion || this.props.inventario.local.direccion.comuna.provincia.region.numero
        let nombreComuna = this.props.inventario.local.nombreComuna || this.props.inventario.local.direccion.comuna.nombre
        let diaSemana = moment(this.props.inventario.fechaProgramada).format('dddd')
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
                               onBlur={this.guardarOCrear.bind(this)}
                               onFocus={()=>{this.inputDia.select()}}
                               disabled={this.props.puedeModificar? '':'disabled'}
                        />
                        <input className={cssInput.inputMes} type="number"
                               className={this.state.mesValido? cssInput.inputDia : cssInput.inputDiaInvalido}
                               type="number" min={1} max={12}
                               ref={ref=>this.inputMes=ref}
                               value={this.state.inputMes}
                               disabled/>
                        <input className={cssInput.inputAnno} type="number"
                               disabled
                               value={this.state.inputAnno}/>
                    </div>
                </td>
                {/* Cliente*/}
                <td className={cssTabla.tdCliente}>
                    <p><small>{ nombreCliente }</small></p>
                </td>
                {/* CECO */}
                <td className={cssTabla.tdCeco}>
                    <OverlayTrigger
                        placement="left"
                        delay={0}
                        overlay={<Tooltip id="yyy">{`Tipo de local: ${this.props.inventario.local.formato_local.nombre}`}</Tooltip>}>
                        <p><small><b>{this.props.inventario.local.numero}</b></small></p>
                    </OverlayTrigger>
                </td>
                {/* Local */}
                <td className={cssTabla.tdLocal}>
                    <p><small><b>{this.props.inventario.local.nombre}</b></small></p>
                </td>
                {/* Region*/}
                <td className={cssTabla.tdRegion}>
                    <p style={{margin:0}}><small>{ nombreRegion }</small></p>
                </td>
                {/* Comuna */}
                <td className={cssTabla.tdComuna}>
                    <OverlayTrigger
                        placement="left"
                        delay={0}
                        overlay={<Tooltip id="yyy">{'Dirección: '+(this.props.inventario.local.direccion.direccion)}</Tooltip>}>
                        <p style={{margin:0}}><b><small>{ nombreComuna }</small></b></p>
                    </OverlayTrigger>

                </td>
                {/* Stock */}
                <td className={cssTabla.tdStock}>
                    <OverlayTrigger
                        placement="left"
                        delay={0}
                        overlay={<Tooltip id="yyy">{'Stock al '+(this.props.inventario.fechaStock)}</Tooltip>}>
                        <p><small>{numeral(this.props.inventario.stockTeorico).format('0,0')}</small></p>
                    </OverlayTrigger>
                </td>
                {/* Dotación */}
                <td className={cssTabla.tdDotacion}>
                    <OverlayTrigger
                        placement="right"
                        delay={0}
                        overlay={<Tooltip id="yyy">{'Produción '+this.props.inventario.local.formato_local.produccionSugerida}</Tooltip>}>

                        <input className={this.state.dotacionValida? cssInput.inputDotacion : cssInput.inputDotacionInvalida } type="number"
                               value={this.state.inputDotacion}
                               onChange={this.onInputDotacionChange.bind(this)}
                               ref={ref=>this.inputDotacion=ref}
                               onKeyDown={this.inputOnKeyDown.bind(this, 'dotacion')}
                               onBlur={this.guardarOCrear.bind(this)}
                               disabled={this.props.puedeModificar? '':'disabled'}/>
                    </OverlayTrigger>
                </td>
                {/* Jornada */}
                {/*
                <td className={cssTabla.tdJornada}>
                    <select onChange={this.onSelectJornadaChange.bind(this)} value={this.state.selectJornada}>
                        <option value="1">no definido</option>
                        <option value="2">día</option>
                        <option value="3">noche</option>
                        <option value="4">día y noche</option>
                    </select>
                </td>
                */}
                {/* Opciones    */}
                <td className={cssTabla.tdOpciones}>
                    {
                        this.props.inventario.idInventario ? (
                            // si esta creado, puede eliminar el inventario
                            this.props.puedeModificar===true?
                                <button className="btn btn-xs btn-primary"
                                        tabIndex="-1"
                                        onClick={this.eliminarInventario.bind(this)}
                                        disabled>
                                    Eliminar inventario
                                </button>
                                :
                                null
                        )
                        :
                            // si no esta creado, solo puede quitarlo de la lista
                            <button className="btn btn-xs btn-danger"
                                      tabIndex="-1"
                                      onClick={this.quitarInventario.bind(this)}>
                                X
                            </button>
                     }
                </td>
            </tr>
        )
    }
}

RowInventarioMensual.propTypes = {
    // Objetos
    index: React.PropTypes.number.isRequired,
    inventario: React.PropTypes.object.isRequired,
    mostrarSeparador: React.PropTypes.bool.isRequired,
    puedeModificar: React.PropTypes.bool.isRequired,
    // Metodos
    focusFilaSiguiente: React.PropTypes.func.isRequired,
    focusFilaAnterior: React.PropTypes.func.isRequired,
    guardarOCrearInventario: React.PropTypes.func.isRequired,
    quitarInventario: React.PropTypes.func.isRequired,
    eliminarInventario: React.PropTypes.func.isRequired
}
RowInventarioMensual.defaultProps = {
    mostrarSeparador: false
}

export default RowInventarioMensual