// Librerias
import React from 'react'
// Estilos
import css from './InputFecha.css'

class InputFecha extends React.Component{
    constructor(props) {
        super(props)
        this.state = {
            dia: '-',
            dirty: false,
            valid: true
        }
    }
    componentWillMount(){
        let [anno1, mes1, dia1] = this.props.fecha.split('-')
        this.setState({
            dia: dia1,
            dirty: false,
            valid: true
        })
    }
    componentWillReceiveProps(nextProps){
        // let [anno1, mes1, dia1] = this.props.fecha.split('-')
        let [anno2, mes2, dia2] = nextProps.fecha.split('-')
        // if(dia1!==dia2){
            // si esto cambia, entonces se debe hacer un "reset" del estado con los nuevos valores
            this.setState({
                dia: dia2,
                dirty: false,
                valid: true
            })
        //    console.log("la fecha cambio ", dia1, dia2)
        // }
    }

    inputOnKeyDown(evt){
        if((evt.keyCode===9 && evt.shiftKey===false) || evt.keyCode===40 || evt.keyCode===13){
            // 9 = tab, flechaAbajo = 40,  13 = enter
            evt.preventDefault()
            this.props.guardarOCrear()
            this.props.focusRowSiguiente()

        }else if((evt.keyCode===9 && evt.shiftKey===true) || evt.keyCode===38) {
            // flechaArriba = 38, shift+tab
            evt.preventDefault()
            this.props.guardarOCrear()
            this.props.focusRowAnterior()
        }
    }

    onInputChange(evt){
        let dia = evt.target.value
        let [anno1, mes1, dia1] = this.props.fecha.split('-')
        this.setState({
            dia: dia,
            dirty: dia1!=dia,
            valid: this._diaValido(dia)
        })
    }
    _diaValido(dia){
        return dia>0 && dia<32
    }
    getEstado(){
        return this.state
    }
    focus(){
        this.inputDia.focus()
    }
    render(){
        let [anno, mes, dia] = this.props.fecha.split('-')
        let classname = this.state.valid
            ? (this.state.dirty? css.inputDiaDirty : css.inputDia)
            : css.inputDiaInvalido
        return(
            <div>
                <input className={classname}
                       ref={ref=>this.inputDia=ref}
                       value={this.state.dia}
                       onKeyDown={this.inputOnKeyDown.bind(this)}
                       onChange={this.onInputChange.bind(this)}
                       onBlur={()=>this.props.guardarOCrear()}
                />
                <input className={css.inputMes} type="number" disabled
                       value={mes}/>
                <input className={css.inputAnno} type="number" disabled
                       value={anno}/>
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
    guardarOCrear: React.PropTypes.func.isRequired
}
export default InputFecha