import React from 'react'
import numeral from 'numeral'

// Componentes
import Tooltip from 'react-bootstrap/lib/Tooltip'
import OverlayTrigger from 'react-bootstrap/lib/OverlayTrigger'

// Styles
import styles from './TablaProgramas.css'
import styleShared from '../shared/shared.css'

const ESTADO = {
    OCULTAR: {
        mensaje: 'Fecha Pendiente',
        className: '',
        tooltipClass: styleShared.tooltipHide
    },
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
            fechaValida: false,
            estado: {}
        }
        // Refs disponibles: this.inputDia, this.inputMes, this.inputAnno, this.inputDotacion, this.inputJornada
    }
    componentDidMount(){
        // fijar la dotacionSugerida
        let dotacionSugerida = this.props.inventario.local.dotacionSugerida
        let dotacionAsignada = this.props.inventario.dotacionAsignada
        this.inputDotacion.value = dotacionAsignada? dotacionAsignada : dotacionSugerida

        // fijar la fecha
        let [anno, mes, dia] = this.props.inventario.fechaProgramada.split('-')
        this.inputDia.value = dia
        this.inputMes.value = mes
        this.inputAnno.value = anno

        // fijar la jornada
        this.inputJornada.value = this.props.inventario.idJornada

        this.validarDatos()
    }
    componentWillReceiveProps(nextProps){
         //Actualizar dotacion
        //if(!this.inputDotacion.value || this.inputDotacion.value=='' || this.inputDotacion.value==0){
            // fijar la dotacionSugerida
            let dotacionSugerida = nextProps.inventario.local.dotacionSugerida
            let dotacionAsignada = nextProps.inventario.dotacionAsignada
            this.inputDotacion.value = dotacionAsignada? dotacionAsignada : dotacionSugerida
        //}

        // actualizar la fecha
        let [anno, mes, dia] = this.props.inventario.fechaProgramada.split('-')
        this.inputDia.value = dia
        this.inputMes.value = mes
        this.inputAnno.value = anno

        // actualizar la jornada
        let jornadaInventario = nextProps.inventario.idJornada
        let jornadaLocal = nextProps.inventario.local.idJornadaSugerida
        this.inputJornada.value = jornadaInventario? jornadaInventario : jornadaLocal
    }

    focusElemento(elemento){
        if(elemento==='dia'){
            this.inputDia.focus()
        }else if(elemento==='dotacion'){
            this.inputDotacion.focus()
        }
    }
    onFocusInputDia(){
        // si al hacer foco en el dia, el valor es "00", dejar el campo vacio con ""
        if(this.inputDia.value==0){
            this.inputDia.value = ''
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

    validarDatos(){
        let dia = this.inputDia.value
        if((dia=="00" || dia=="0") && this.props.inventario.idInventario===null){
            this.setState({fechaValida: false, estado: ESTADO.FECHA_PENDIENTE})
            return false
        }
        if(dia<1 || dia>31){
            this.setState({fechaValida: false, estado: ESTADO.FECHA_INVALIDA})
            return false
        }

        this.setState({fechaValida: true, estado: ESTADO.OCULTAR})
        return true
    }
    guardarOCrear(){
        let dotacion = this.inputDotacion.value
        let jornada = this.inputJornada.value
        let anno = this.inputAnno.value
        let mes = this.inputMes.value
        let dia = this.inputDia.value
        if(dia==''){
            this.inputDia.value = '00'
            dia = '00'
        }

        if(this.validarDatos()){
            let fecha = `${anno}-${mes}-${dia}`
            console.log(`fecha ${fecha}, dotacion ${dotacion}, jornada ${jornada}`)

            this.props.guardarOCrearInventario({
                idInventario: this.props.inventario.idInventario,
                idDummy: this.props.inventario.idDummy,
                idLocal: this.props.inventario.local.idLocal,
                idJornada: jornada,
                fechaProgramada: fecha,
                horaLlegada: this.props.inventario.horaLlegada,
                stockTeorico: this.props.inventario.local.stock,
                dotacionAsignada: dotacion
            })
                //.then(res=>{
                //    this.setState({
                //        //guardado: true,
                //        fechaValida: true,
                //        estado: ESTADO.GUARDADO
                //    })
        }else{
            console.log('datos invalidos')
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
                    {this.state.estado!=={}?

                        <Tooltip placement="left" positionLeft={-120} id="xxxx" style={{width: '120px', zIndex: 0}}
                        className={"in "+this.state.estado.tooltipClass}>
                            {this.state.estado.mensaje}
                        </Tooltip>
                        : null
                    }
                    <input className={this.state.fechaValida? styles.inputDia : styles.inputDiaInvalido}
                           type="number" min={0} max={31}
                           ref={ref=>this.inputDia=ref}
                           onKeyDown={this.inputOnKeyDown.bind(this, 'dia')}
                           onFocus={this.onFocusInputDia.bind(this)}
                           onBlur={this.guardarOCrear.bind(this)}/>
                    <input className={styles.inputMes} type="number" disabled
                           ref={ref=>this.inputMes=ref}/>
                    <input className={styles.inputAnno} type="number" disabled
                           ref={ref=>this.inputAnno=ref}/>
                </td>
                {/* Cliente*/}
                <td className={styles.tdCliente}>
                    <p><small>{this.props.inventario.local.nombreCliente}</small></p>
                </td>
                {/* CECO */}
                <td className={styles.tdCeco}>
                    <p><small><b>{this.props.inventario.local.numero}</b></small></p>
                </td>
                {/* Local */}
                <td className={styles.tdLocal}>
                    <p><small><b>{this.props.inventario.local.nombre}</b></small></p>
                </td>
                {/* Region*/}
                <td className={styles.tdRegion}>
                    <p style={{margin:0}}><small>{this.props.inventario.local.nombreRegion}</small></p>
                </td>
                {/* Comuna */}
                <td className={styles.tdComuna}>
                    <p style={{margin:0}}><b><small>{this.props.inventario.local.nombreComuna}</small></b></p>
                </td>
                {/* Stock */}
                <td className={styles.tdStock}>
                    <OverlayTrigger
                        placement="left"
                        delay={0}
                        overlay={<Tooltip id="yyy">{'Stock al '+(this.props.inventario.local.fechaStock)}</Tooltip>}>
                        <p><small>{numeral(this.props.inventario.local.stock).format('0,0')}</small></p>

                    </OverlayTrigger>
                </td>
                {/* Dotación */}
                <td className={styles.tdDotacion}>
                    <OverlayTrigger
                        placement="left"
                        delay={0}
                        overlay={<Tooltip id="yyy">{'Produción '+this.props.inventario.local.formato_local.produccionSugerida}</Tooltip>}>

                        <input className={styles.inputDotacionIngresada} type="number"
                               ref={ref=>this.inputDotacion=ref}
                               onKeyDown={this.inputOnKeyDown.bind(this, 'dotacion')}
                               onBlur={this.guardarOCrear.bind(this)}/>

                    </OverlayTrigger>
                </td>
                {/* Jornada */}
                <td className={styles.tdJornada}>
                    <select onChange={this.guardarOCrear.bind(this)} ref={ref=>this.inputJornada=ref}>
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

RowInventario.propTypes = {
    // Objetos
    index: React.PropTypes.number.isRequired,
    inventario: React.PropTypes.object.isRequired,
    // Metodos
    focusFilaSiguiente: React.PropTypes.func.isRequired,
    focusFilaAnterior: React.PropTypes.func.isRequired,
    guardarOCrearInventario: React.PropTypes.func.isRequired
}

export default RowInventario