import React from 'react'
let PropTypes = React.PropTypes
import numeral from 'numeral'

// Componentes
import Tooltip from 'react-bootstrap/lib/Tooltip'
import OverlayTrigger from 'react-bootstrap/lib/OverlayTrigger'

// Styles
import styles from './TablaProgramas.css'
import styleShared from '../shared/shared.css'

const ESTADO = {
    FECHA_PENDIENTE: {
        mensaje: 'Fecha Pendiente',
        className: 'label-danger',
        tooltipClass: styleShared.tooltipWarning
    },
    FECHA_INVALIDA: {
        mensaje: 'Fecha Invalida',
        className: 'label-danger',
        tooltipClass: styleShared.tooltipDanger
    },
    GUARDADO: {
        mensaje: 'Guardado',
        className: 'label-success'
    }
}

class RowInventario extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            inputDotacion: 1,
            inputDia: '',
            guardado: false,
            fechaValida: false,
            estado: ESTADO.FECHA_PENDIENTE
        }
        this.guardarOCrear = this.guardarOCrear.bind(this)
    }
    componentWillReceiveProps(nextProps){
        // si la dotacion anteriormente era undefined, pero ahora se recibe el valor, se actualiza el state
        // esto pasa cuando se reciben los datos luego de una peticion json
        if(!this.props.local.dotacionSugerida && nextProps.local.dotacionSugerida){
            this.setState({
                inputDotacion: nextProps.local.dotacionSugerida
            })
        }
        console.log('nextProps', nextProps)
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
    inputDiaHandler(evt){
        this.setState({inputDia: evt.target.value})
    }
    inputJornadaHandler(evt){
        console.log(`opcion ${evt.target.value} seleccionada`)
        this.guardarOCrear()
    }
    inputDotacionHandler(evt){
        this.setState({inputDotacion: evt.target.value})
    }

    guardarOCrear(evt){
        if(evt) evt.preventDefault()

        let jornada  = this.inputJornada.value
        console.log("guardar o crear: ", jornada)

        const fechaEsValida = this.state.inputDia>=1 && this.state.inputDia<=this.props.ultimoDiaMes
        if(fechaEsValida){
            // ToDo: llamar al API
            this.props.guardarOCrear({
                idLocal: this.props.local.idLocal,
                idJornada: jornada,
                fechaProgramada: `${this.props.annoProgramado}-${this.props.mesProgramado}-${this.state.inputDia}`,
                horaLlegada: '00:00',
                stockTeorico: this.props.local.stock,
                dotacionAsignada: this.state.inputDotacion
            }).then(res=>{
                this.setState({
                    //guardado: true,
                    fechaValida: true,
                    estado: ESTADO.GUARDADO
                })
            }).catch(err=>{
                console.error(err)
            })
        }else {
            this.setState({
                //guardado: true,
                fechaValida: false,
                estado: ESTADO.FECHA_INVALIDA
            })
            console.log(`dia ${this.state.inputDia} incorrecto`)
        }
    }
    render(){
        return (
            <tr>
                {/* Correlativo */}
                <td className={styles.tdCorrelativo}>
                    {this.props.index}
                </td>
                {/* Fecha */}
                <td className={styles.tdFecha}>
                    {this.state.estado!==ESTADO.GUARDADO?

                        <Tooltip placement="left" positionLeft={-120} id="xxxx" style={{width: '120px'}}
                        className={"in "+this.state.estado.tooltipClass}>
                            {this.state.estado.mensaje}
                        </Tooltip>
                        : null
                    }
                    <input className={this.state.fechaValida? styles.inputDia : styles.inputDiaInvalido}
                           type="number" min={0} max={this.props.ultimoDiaMes}
                           ref={ref=>this.inputDia=ref}
                           value={this.state.inputDia}
                           onChange={this.inputDiaHandler.bind(this)}
                           onKeyDown={this.inputOnKeyDown.bind(this, 'dia')}
                           onBlur={this.guardarOCrear}/>
                    <input className={styles.inputMes} type="number" defaultValue={this.props.mesProgramado} disabled/>
                    <input className={styles.inputAnno} type="number" defaultValue={this.props.annoProgramado} disabled/>
                </td>
                {/* Cliente*/}
                <td className={styles.tdCliente}>
                    <p><small>{this.props.nombreCliente}</small></p>
                </td>
                {/* CECO */}
                <td className={styles.tdCeco}>
                    <p><small><b>{this.props.local.numero || '-'}</b></small></p>
                </td>
                {/* Local */}
                <td className={styles.tdLocal}>
                    <p><small><b>{this.props.local.nombre || '-'}</b></small></p>
                </td>
                {/* Region*/}
                <td className={styles.tdRegion}>
                    <p style={{margin:0}}><small>{this.props.region}</small></p>
                </td>
                {/* Comuna */}
                <td className={styles.tdComuna}>
                    <p style={{margin:0}}><b><small>{this.props.comuna}</small></b></p>
                </td>
                {/* Stock */}
                <td className={styles.tdStock}>
                    <OverlayTrigger
                        placement="left"
                        delay={0}
                        overlay={<Tooltip id="yyy">{'Stock al '+(this.props.local.fechaStock || '??-??-????')}</Tooltip>}>
                        <p><small>{numeral(this.props.local.stock || 0).format('0,0')}</small></p>

                    </OverlayTrigger>
                </td>
                {/* Dotación */}
                <td className={styles.tdDotacion}>
                    <OverlayTrigger
                        placement="left"
                        delay={0}
                        overlay={<Tooltip id="yyy">{this.props.local.formato_local? 'Produción '+this.props.local.formato_local.produccionSugerida : ''}</Tooltip>}>

                        <input className={styles.inputDotacionIngresada} type="number"
                               ref={ref=>this.inputDotacion=ref}
                               value={this.state.inputDotacion}
                               onChange={this.inputDotacionHandler.bind(this)}
                               onKeyDown={this.inputOnKeyDown.bind(this, 'dotacion')}
                               onBlur={this.guardarOCrear}/>

                    </OverlayTrigger>
                </td>
                {/* Jornada */}
                <td className={styles.tdJornada}>
                    <select onChange={this.inputJornadaHandler.bind(this)} ref={ref=>this.inputJornada=ref}>
                        <option value="1">día</option>
                        <option value="2">noche</option>
                        <option value="3">día y noche</option>
                        <option value="4">no definido</option>
                    </select>
                </td>
                {/* Estado    */}
                {/*
                <td className={styles.tdEstado}>
                    <span className={'label '+ this.state.estado.className}>{this.state.estado.mensaje}</span>
                </td>
                */}
                {/* Opciones    */}
                <td className={styles.tdOpciones}>
                    <button className="btn btn-xs btn-primary" tabIndex="-1">Editar local</button>
                    {this.props.idInventario?
                        <button className="btn btn-xs btn-primary" tabIndex="-1">Editar inventario</button>
                        : null
                    }
                </td>
            </tr>
        )
    }
}

RowInventario.protoTypes = {
    index: PropTypes.number.required,
    mesProgramado: PropTypes.string.required,
    annoProgramado: PropTypes.string.required,
    nombreCliente: PropTypes.string.required,
    region: PropTypes.string.required,
    comuna: PropTypes.string.required,
    stock: PropTypes.number.required,
    dotacionSugerida: PropTypes.number.required,
    //jornada: PropTypes.number.required,
    focusFilaSiguiente: PropTypes.func.required,
    focusFilaAnterior: PropTypes.func.required,
    guardarOCrear: PropTypes.func.required,

    local: PropTypes.object.required
}

export default RowInventario