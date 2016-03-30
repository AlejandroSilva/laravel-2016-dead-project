import React from 'react'
import numeral from 'numeral'

// Componentes
import Tooltip from 'react-bootstrap/lib/Tooltip'
import OverlayTrigger from 'react-bootstrap/lib/OverlayTrigger'

// Styles
import * as css from './RowInventarioMensual.css'

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
        console.log("will mount: dotacionAsignadaTotal, dotacionSugerida, (nombre) ", dotacionAsignadaTotal, dotacionSugerida, dotacionAsignadaTotal||dotacionSugerida)
        console.log("will mount comuna: ", this.props.inventario.local.nombreComuna)    // no tienen la informacion, como se esperaba
        this.setState({
            inputDia: dia,
            inputMes: mes,
            inputAnno: anno,
            inputDotacion: dotacionAsignadaTotal || dotacionSugerida,
            selectJornada: jornadaInventario || jornadaLocal,
            diaValido: this._diaValido(dia),
            dotacionValida: this._dotacionValida(dotacionAsignadaTotal || dotacionSugerida)
        })
    }

    componentWillReceiveProps(nextProps){
        console.log("will receibeprops")

        let mismoInventario = this.props.inventario.idDummy===nextProps.inventario.idDummy
        if(mismoInventario){
            let newState = {}
            //if(this.props.inventario.idDummy==9) console.log("actualizando componente 9", this.props.inventario.fechaProgramada, nextProps.inventario.fechaProgramada)
            //console.log("nuevas props para ", this.props.inventario.idDummy)
            console.log("AAA prop.dotacionSugerida, nextprop.dotacionSugerida, state.inputDOtacion", this.props.inventario.local.dotacionSugerida, nextProps.inventario.local.dotacionSugerida, this.state.inputDotacion);

            // Si es el mismo inventario, se revisa si se han actualizado los datos (y se reemplaza el state actual del usuario)

            // se recibio una nueva fecha?
            let [anno1, mes1, dia1] = this.props.inventario.fechaProgramada.split('-')
            let [anno2, mes2, dia2] = nextProps.inventario.fechaProgramada.split('-')
            //if(dia1!==dia2 || mes1!==mes2 || anno1!==anno2 || this.state.inputDia!=dia2, this.state.inputMes!=mes2, this.state.inputAnno!=anno2)
                this.setState({inputDia: dia2, inputMes: mes2, inputAnno: anno2, diaValido: this._diaValido(dia2)})

            // se recibio una nueva dotacion?
            let dotacion1 = this.props.inventario.dotacionAsignadaTotal || this.props.inventario.local.dotacionSugerida
            let dotacion2 = nextProps.inventario.dotacionAsignadaTotal || nextProps.inventario.local.dotacionSugerida
            console.log("AAAA dotacionAntigua, dotacionNueva,", dotacion1, dotacion2)
            //if(dotacion1!==dotacion2 || this.state.inputDotacion!==dotacion2)
                this.setState({inputDotacion: dotacion2, dotacionValida: this._dotacionValida(dotacion2)})


            // se recibio una nueva jornada?
            let jornada1 = this.props.inventario.idJornada || this.props.inventario.local.idJornadaSugerida
            let jornada2 = nextProps.inventario.idJornada || nextProps.inventario.local.idJornadaSugerida
            //if(jornada1!==jornada2 || this.state.selectJornada!==jornada2)
                this.setState({selectJornada: jornada2})

            this.setState(newState)

        }else{
            // Si el inventario cambio, se vuelven a poner los valores "por defecto"

            console.log("BBB", this.props.inventario.local.dotacionSugerida, nextProps.inventario.local.dotacionSugerida, this.state.inputDotacion);
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
                dotacionValida: this._dotacionValida(dotacionAsignadaTotal || dotacionSugerida)
            })
            console.log("BBBB dotacionAsignadaTotal, dotacionSugerida", dotacionAsignadaTotal, dotacionSugerida)
        }
        //console.log(this.props.inventario.idDummy, nextProps.inventario.idDummy, mismoInventario)
        //console.log(this.props.inventario.idDummy, dotacionAsignada, dotacionSugerida)
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
    _dotacionValida(dotacion){
        return dotacion>0
    }
    isDirty(){
        let [anno, mes, dia] = this.props.inventario.fechaProgramada.split('-')
        let isDirty = (
            this.state.inputDia!=dia ||
            this.state.inputDotacion!=(this.props.inventario.dotacionAsignadaTotal || this.props.inventario.local.dotacionSugerida) ||
            this.state.selectJornada!=(this.props.inventario.idJornada || this.props.inventario.local.idJornadaSugerida)
        )
        //console.log("dia ", this.state.inputDia, dia, this.state.inputDia!==dia)
        //console.log("dotac ", this.state.inputDotacion, (this.props.inventario.dotacionAsignadaTotal || this.props.inventario.local.dotacionSugerida), this.state.inputDotacion!==(this.props.inventario.dotacionAsignadaTotal || this.props.inventario.local.dotacionSugerida) )
        //console.log("jornada ", this.state.selectJornada, (this.props.inventario.idJornada || this.props.inventario.local.idJornadaSugerida), this.state.selectJornada!==(this.props.inventario.idJornada || this.props.inventario.local.idJornadaSugerida))
        console.log("isDirty ", isDirty)
        return isDirty
    }
    guardarOCrear(){
        // si no es dirty (no hay cambios) no se hace nada
        if(this.isDirty()===false)
            return

        let dotacion = this.state.inputDotacion
        let jornada = this.state.selectJornada
        let anno = this.state.inputAnno
        let mes = this.state.inputMes
        let dia = this.state.inputDia

        if(this.state.diaValido && this.state.dotacionValida){
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

    render(){
        console.log("render: prop.local.dotSug, state.inputDot", this.props.inventario.local.dotacionSugerida, this.state.inputDotacion)
        return (
            <tr>
                {/* Correlativo */}
                <td className={css.tdCorrelativo}>
                    { /*this.props.inventario.idDummy*/this.props.index}
                </td>
                {/* Fecha */}
                <td className={css.tdFecha}>
                    <input className={this.state.diaValido? css.inputDia : css.inputDiaInvalido}
                           type="number" min={0} max={31}
                           ref={ref=>this.inputDia=ref}
                           value={this.state.inputDia}
                           onChange={this.onInputDiaChange.bind(this)}
                           onKeyDown={this.inputOnKeyDown.bind(this, 'dia')}
                           onBlur={this.guardarOCrear.bind(this)}/>
                    <input className={css.inputMes} type="number" disabled
                           value={this.state.inputMes}/>
                    <input className={css.inputAnno} type="number" disabled
                           value={this.state.inputAnno}/>
                </td>
                {/* Cliente*/}
                <td className={css.tdCliente}>
                    <p><small>{this.props.inventario.local.nombreCliente}</small></p>
                </td>
                {/* CECO */}
                <td className={css.tdCeco}>
                    <p><small><b>{this.props.inventario.local.numero}</b></small></p>
                </td>
                {/* Local */}
                <td className={css.tdLocal}>
                    <p><small><b>{this.props.inventario.local.nombre}</b></small></p>
                </td>
                {/* Region*/}
                <td className={css.tdRegion}>
                    <p style={{margin:0}}><small>{this.props.inventario.local.nombreRegion}</small></p>
                </td>
                {/* Comuna */}
                <td className={css.tdComuna}>
                    <p style={{margin:0}}><b><small>{this.props.inventario.local.nombreComuna}</small></b></p>
                </td>
                {/* Stock */}
                <td className={css.tdStock}>
                    <OverlayTrigger
                        placement="left"
                        delay={0}
                        overlay={<Tooltip id="yyy">{'Stock al '+(this.props.inventario.local.fechaStock)}</Tooltip>}>
                        <p><small>{numeral(this.props.inventario.local.stock).format('0,0')}</small></p>

                    </OverlayTrigger>
                </td>
                {/* Dotación */}
                <td className={css.tdDotacion}>
                    <OverlayTrigger
                        placement="right"
                        delay={0}
                        overlay={<Tooltip id="yyy">{'Produción '+this.props.inventario.local.formato_local.produccionSugerida}</Tooltip>}>

                        <input className={this.state.dotacionValida? css.inputDotacion : css.inputDotacionInvalida } type="number"
                               value={this.state.inputDotacion}
                               onChange={this.onInputDotacionChange.bind(this)}
                               ref={ref=>this.inputDotacion=ref}
                               onKeyDown={this.inputOnKeyDown.bind(this, 'dotacion')}
                               onBlur={this.guardarOCrear.bind(this)}/>

                    </OverlayTrigger>
                </td>
                {/* Jornada */}
                {/*
                <td className={css.tdJornada}>
                    <select onChange={this.onSelectJornadaChange.bind(this)} value={this.state.selectJornada}>
                        <option value="1">no definido</option>
                        <option value="2">día</option>
                        <option value="3">noche</option>
                        <option value="4">día y noche</option>
                    </select>
                </td>
                */}
                {/* Opciones    */}
                <td className={css.tdOpciones}>
                    <button className="btn btn-xs btn-primary" tabIndex="-1">Editar local</button>
                    {this.props.inventario.idInventario
                        ? <button className="btn btn-xs btn-primary" tabIndex="-1">Editar inventario</button>
                        : <button className="btn btn-xs btn-danger" tabIndex="-1" onClick={this.quitarInventario.bind(this)}>X</button>
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
    // Metodos
    focusFilaSiguiente: React.PropTypes.func.isRequired,
    focusFilaAnterior: React.PropTypes.func.isRequired,
    guardarOCrearInventario: React.PropTypes.func.isRequired,
    quitarInventario: React.PropTypes.func.isRequired
}

export default RowInventarioMensual