import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import Tooltip from 'react-bootstrap/lib/Tooltip'
import OverlayTrigger from 'react-bootstrap/lib/OverlayTrigger'

// Styles
import styles from './TablaLocalesMensual.css'

const ESTADO = {
    FECHA_PENDIENTE: {
        mensaje: 'Fecha Pendiente',
        className: 'label-danger'
    },
    FECHA_INVALIDA: {
        mensaje: 'Fecha Invalida',
        className: 'label-danger'
    },
    GUARDADO: {
        mensaje: 'Guardado',
        className: 'label-success'
    }
}

class RowLocales extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            guardado: false,
            fechaValida: false,
            estado: ESTADO.FECHA_PENDIENTE
        }
        this.inputFechaOnKeyDown = this.inputFechaOnKeyDown.bind(this)
        this.guardarOCrear = this.guardarOCrear.bind(this)
    }
    focusFecha(){
        this.inputFecha.focus()
    }
    inputFechaOnKeyDown(evt){
        if((evt.keyCode===9 && evt.shiftKey===false) || evt.keyCode===40 || evt.keyCode===13){
            // 9 = tab, flechaAbajo = 40,  13 = enter
            evt.preventDefault()
            this.props.focusFilaSiguiente(this.props.index)

        }else if((evt.keyCode===9 && evt.shiftKey===true) || evt.keyCode===38) {
            // flechaArriba = 38, shift+tab
            this.props.focusFilaAnterior(this.props.index)
            evt.preventDefault()
        }
    }
    guardarOCrear(evt){
        let dia = this.inputFecha.value
        const fechaEsValida = dia>=1 && dia<=this.props.ultimoDiaMes
        if(fechaEsValida){
            console.log(`dia ${dia} valido, guardado/actualizado`)
            // ToDo: llamar al API
            this.props.guardarOCrear({
                idLocal: 99,
                idJornada: 3,
                fechaProgramada: '1-2-2016',
                horaLlegada: '11:12',
                stockTeorico: '123456',
                dotacionAsignada: 97
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
            console.log(`dia ${dia} incorrecto`)
        }
    }
    render(){
        return (
            <tr>
                <td className={styles.tdCorrelativo}>
                    {/* Correlativo */}
                    {this.props.index}
                </td>
                <td className={styles.tdFecha}>
                    {/* Fecha */}
                    {this.state.estado!==ESTADO.GUARDADO?

                        <Tooltip placement="left" className="in" positionLeft={-120} id="xxxx" style={{width: '120px'}}>
                            {this.state.estado.mensaje}
                        </Tooltip>
                        : null
                    }
                    <input className={this.state.fechaValida? styles.inputDia : styles.inputDiaInvalido} type="number" min={0} max={this.props.ultimoDiaMes}
                           ref={ref=>this.inputFecha=ref}
                           onKeyDown={this.inputFechaOnKeyDown}
                           onBlur={this.guardarOCrear}
                    />
                    <input className={styles.inputMes} type="number" defaultValue={this.props.mesProgramado} disabled/>
                    <input className={styles.inputAnno} type="number" defaultValue={this.props.annoProgramado} disabled/>
                </td>
                <td className={styles.tdCliente}>
                    {/* Cliente*/}
                    <p><small>{this.props.nombreCliente}</small></p>
                </td>
                <td className={styles.tdCeco}>
                    {/* CECO */}
                    <p><small><b>{this.props.ceco}</b></small></p>
                </td>
                <td className={styles.tdLocal}>
                    {/* Local */}
                    <p><small><b>{this.props.nombreLocal}</b></small></p>
                </td>
                <td className={styles.tdZonaSei}>
                    {/* Zona */}
                    <p style={{margin:0}}><small>{this.props.zona}</small></p>
                </td>
                <td className={styles.tdRegion}>
                    {/* Region*/}
                    <p style={{margin:0}}><small>{this.props.region}</small></p>
                </td>
                <td className={styles.tdComuna}>
                    {/* Comuna */}
                    <p style={{margin:0}}><b><small>{this.props.comuna}</small></b></p>
                </td>
                <td className={styles.tdStock}>
                    {/* Stock */}
                    <p><small>{this.props.stock}</small></p>
                </td>
                <td className={styles.tdDotacion}>
                    {/* Dotación */}
                    <input className={styles.inputDotacionSugerida} type="text" defaultValue={this.props.dotacionSugerida} disabled/>
                    <input className={styles.inputDotacionIngresada} type="number" tabIndex="-1"/>
                </td>
                <td className={styles.tdJornada}>
                    {/* Jornada */}
                    <p><small>{this.props.jornada}</small></p>
                </td>
                {/*
                <td className={styles.tdEstado}>
                    {/ * Estado    * /}
                    <span className={'label '+ this.state.estado.className}>{this.state.estado.mensaje}</span>
                </td>
                */}
                <td className={styles.tdOpciones}>
                    {/* Opciones    */}
                    <button className="btn btn-xs btn-primary" tabIndex="-1">Editar local</button>
                </td>
            </tr>
        )
    }
}

RowLocales.protoTypes = {
    index: PropTypes.number.required,
    mesProgramado: PropTypes.string.required,
    annoProgramado: PropTypes.string.required,
    nombreCliente: PropTypes.string.required,
    ceco: PropTypes.number.required,
    nombreLocal: PropTypes.string.required,
    zona: PropTypes.string.required,
    region: PropTypes.string.required,
    comuna: PropTypes.string.required,
    stock: PropTypes.number.required,
    dotacionSugerida: PropTypes.number.required,
    //jornada: PropTypes.number.required,
    focusFilaSiguiente: PropTypes.func.required,
    focusFilaAnterior: PropTypes.func.required,
    guardarOCrear: PropTypes.func.required
}

export default RowLocales