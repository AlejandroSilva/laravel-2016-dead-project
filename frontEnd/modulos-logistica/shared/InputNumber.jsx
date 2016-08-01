// Librerias
import React from 'react'
// Estilos
import * as css from './inputs.css'

export class InputNumber extends React.Component{
    constructor(props) {
        super(props)
        this.state = {
            valor: '',
            dirty: false,
            valid: true
        }
    }
    componentWillMount(){
        this.setState({
            valor: this.props.asignada,
            dirty: false,
            valid: true
        })
    }
    componentWillReceiveProps(nextProps){
        // apenas se reciba una nueva propiedad, reemplazar el estado independiente de su contenido
        let valor2 = nextProps.asignada
        this.setState({
            valor: valor2,
            dirty: false,
            valid: true
        })
    }

    inputOnKeyDown(evt){
        if((evt.keyCode===9 && evt.shiftKey===false) || evt.keyCode===40 || evt.keyCode===13){
            // 9 = tab, flechaAbajo = 40,  13 = enter
            evt.preventDefault()
            this.props.focusRowSiguiente()

        }else if((evt.keyCode===9 && evt.shiftKey===true) || evt.keyCode===38) {
            // flechaArriba = 38, shift+tab
            evt.preventDefault()
            this.props.focusRowAnterior()
        }
    }
    onInputChange(evt){
        let valorUsuario = evt.target.value
        this.setState({
            valor: valorUsuario,
            dirty: this.props.asignada!=valorUsuario,
            valid: this.props.validador(valorUsuario)
        })
    }
    getEstado(){
        return this.state
    }
    focus(){
        this.inputText.focus()
        this.inputText.select()
    }

    render(){
        // prioridad del style: valido > dirty > input
        let classname = this.state.valid
            ? (this.state.dirty? css.inputDirty : css.input)
            : css.inputInvalido

        return (
            <input
                className={classname + " " + this.props.className}
                style={this.props.style}
                ref={ref=>this.inputText=ref}
                type="number"
                value={this.state.valor}
                onKeyDown={this.inputOnKeyDown.bind(this)}
                onChange={this.onInputChange.bind(this)}
                onBlur={()=>this.props.onGuardar(this.state)}
                onFocus={()=>{ this.inputText.select() }}             // seleccionar el texto cuando se hace focus
                disabled={this.props.editable? '':'disabled'}
            />
        )
    }
}
InputNumber.propTypes = {
    // Objetos
    editable: React.PropTypes.bool.isRequired,
    asignada: React.PropTypes.number.isRequired,
    // Metodos
    focusRowAnterior: React.PropTypes.func.isRequired,
    focusRowSiguiente: React.PropTypes.func.isRequired,
    onGuardar: React.PropTypes.func.isRequired,
    validador: React.PropTypes.func.isRequired
}
InputNumber.defaultProps = {
    editable: false,
    validador: (texto)=>{return true}   // por defecto, el texto siempre es valido
}
export default InputNumber