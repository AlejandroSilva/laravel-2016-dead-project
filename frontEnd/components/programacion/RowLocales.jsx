import React from 'react'
let PropTypes = React.PropTypes
import numeral from 'numeral'

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
        if(!this.props.local.dotacionSugerida && nextProps.local.dotacionSugerida){
            this.setState({
                inputDotacion: nextProps.local.dotacionSugerida
            })
        }
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
    inputDotacionHandler(evt){
        this.setState({inputDotacion: evt.target.value})
    }

    guardarOCrear(evt){
        const fechaEsValida = this.state.inputDia>=1 && this.state.inputDia<=this.props.ultimoDiaMes
        if(fechaEsValida){
            console.log(`dia ${this.state.inputDia} valido`)
            // ToDo: llamar al API
            this.props.guardarOCrear({
                idLocal: this.props.local.idLocal,
                idJornada: 3,//**
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
                <td className={styles.tdCliente}>
                    {/* Cliente*/}
                    <p><small>{this.props.nombreCliente}</small></p>
                </td>
                <td className={styles.tdCeco}>
                    {/* CECO */}
                    <p><small><b>{this.props.local.numero || '-'}</b></small></p>
                </td>
                <td className={styles.tdLocal}>
                    {/* Local */}
                    <p><small><b>{this.props.local.nombre || '-'}</b></small></p>
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
                    <OverlayTrigger
                        placement="left"
                        delay={0}
                        overlay={<Tooltip id="yyy">{'Stock al '+(this.props.local.fechaStock || '??-??-????')}</Tooltip>}>
                        <p><small>{numeral(this.props.local.stock || 0).format('0,0')}</small></p>

                    </OverlayTrigger>
                </td>
                <td className={styles.tdDotacion}>
                    {/* Dotación */}
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
                    <p>{this.props.idInventario || '----    '}</p>
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
    zona: PropTypes.string.required,
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

export default RowLocales