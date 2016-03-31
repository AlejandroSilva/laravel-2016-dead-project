// Librerias
import React from 'react'
// Estilos
import css from './InputFecha.css'

class InputFecha extends React.Component{
    constructor(props) {
        super(props)
        this.state = {
            dia: '-',
            mes: '-',
            anno: '-',
            diaDirty: false, mesDirty: false, annoDirty: false,
            diaValid: true,  mesValid: true,  annoValid: true
        }
    }
    componentWillMount(){
        let [anno1, mes1, dia1] = this.props.fecha.split('-')
        this.setState({
            dia: dia1,
            mes: mes1,
            anno: anno1,
            diaDirty: false, mesDirty: false, annoDirty: false,
            diaValid: true,  mesValid: true,  annoValid: true
        })
    }
    componentWillReceiveProps(nextProps){
        // apenas se reciba una nueva propiedad, reemplazar el estado independiente de su contenido
        let [anno2, mes2, dia2] = nextProps.fecha.split('-')
            this.setState({
                dia: dia2,
                mes: mes2,
                anno: anno2,
                diaDirty: false, mesDirty: false, annoDirty: false,
                diaValid: true,  mesValid: true,  annoValid: true
            })
    }

    getEstado(){
        return {
            dirty: this.state.diaDirty || this.state.mesDirty || this.state.annoDirty,
            valid: this.state.diaValid && this.state.mesValid && this.state.annoValid,
            fecha: `${this.state.anno}-${this.state.mes}-${this.state.dia}`
        }
    }
    focus(){
        this.inputDia.focus()
        this.inputDia.select()
    }

    inputOnKeyDown(evt){
        if((evt.keyCode===9 && evt.shiftKey===false) || evt.keyCode===40 || evt.keyCode===13){
            // 9 = tab, flechaAbajo = 40,  13 = enter
            evt.preventDefault()
            this.props.onGuardar()
            this.props.focusRowSiguiente()

        }else if((evt.keyCode===9 && evt.shiftKey===true) || evt.keyCode===38) {
            // flechaArriba = 38, shift+tab
            evt.preventDefault()
            this.props.onGuardar()
            this.props.focusRowAnterior()
        }
    }


    _diaValido(dia){
        return dia>=0 && dia<32
    }
    _mesValido(mes){
        return mes=>0 && mes<=12
    }

    onDiaChange(evt){
        let dia = evt.target.value
        let diaOriginal = this.props.fecha.split('-')[2]
        this.setState({
            dia: dia,
            diaDirty: dia!=diaOriginal,
            diaValid: this._diaValido(dia)
        })
    }
    onMesChange(evt){
        let mes = evt.target.value
        let mesOriginal = this.props.fecha.split('-')[1]
        this.setState({
            mes: mes,
            mesDirty: mes!=mesOriginal,
            mesValid: this._mesValido(mes)
        })
    }

    render(){
        let classnameDia = this.state.diaValid
            ? (this.state.diaDirty? css.inputDiaDirty : css.inputDia)
            : css.inputDiaInvalido
        return(
            <div>
                <input className={classnameDia}
                       ref={ref=>this.inputDia=ref}
                       value={this.state.dia}
                       onKeyDown={this.inputOnKeyDown.bind(this)}
                       onChange={this.onDiaChange.bind(this)}
                       onBlur={()=>this.props.onGuardar()}
                       onFocus={()=>{ this.inputDia.select() }}             // seleccionar el texto cuando se hace focus
                />
                <input className={css.inputMes} type="number"
                       ref={ref=>this.inputMes=ref}
                       value={this.state.mes}
                       onKeyDown={this.inputOnKeyDown.bind(this)}
                       onChange={this.onMesChange.bind(this)}
                       onBlur={()=>this.props.onGuardar()}
                       onFocus={()=>{ this.inputMes.select() }}             // seleccionar el texto cuando se hace focus
                />
                <input className={css.inputAnno} type="number" disabled
                       value={this.state.anno}/>
            </div>
        )
    }
}
InputFecha.propTypes = {
    // Objetos
    fecha: React.PropTypes.string.isRequired,
    // Metodos
    focusRowAnterior: React.PropTypes.func.isRequired,
    focusRowSiguiente: React.PropTypes.func.isRequired,
    onGuardar: React.PropTypes.func.isRequired
}
export default InputFecha