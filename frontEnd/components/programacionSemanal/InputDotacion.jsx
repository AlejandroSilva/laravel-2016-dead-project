// Librerias
import React from 'react'

// Estilos
import css from './InputDotacion.css'

class InputDotacion extends React.Component{
    constructor(props) {
        super(props)
        this.state = {
            dotacion: '-',
            dirty: false,
            valid: true
        }
    }
    componentWillMount(){
        this.setState({
            dotacion: this.props.asignada,
            dirty: false,
            valid: true
        })
    }
    componentWillReceiveProps(nextProps){
        let dotacion1 = this.props.asignada
        let dotacion2 = nextProps.asignada
        if(dotacion1!==dotacion2){
            // si esto cambia, entonces se debe hacer un "reset" del estado con los nuevos valores
            this.setState({
                dotacion: dotacion2,
                dirty: false,
                valid: true
            })
        }
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
        let dotacion = evt.target.value
        this.setState({
            dotacion: dotacion,
            dirty: this.props.asignada!=dotacion,
            valid: dotacion>0
        })
    }
    getEstado(){
        return this.state
    }
    focus(){
        this.inputDotacion.focus()
    }
    render(){
        let classname = this.state.valid
            ? (this.state.dirty? css.inputDotacionDirty : css.inputDotacion)
            : css.inputDotacionInvalida
        return(
            <input
                style={this.props.style}
                className={classname}
                ref={ref=>this.inputDotacion=ref}
                type="number"
                value={this.state.dotacion}
                onKeyDown={this.inputOnKeyDown.bind(this)}
                onChange={this.onInputChange.bind(this)}
                onBlur={()=>this.props.guardarOCrear()}
            />
        )
    }
}
InputDotacion.propTypes = {
    // Objetos
    asignada: React.PropTypes.string.isRequired,
    // Metodos
    focusRowAnterior: React.PropTypes.func.isRequired,
    focusRowSiguiente: React.PropTypes.func.isRequired,
    guardarOCrear: React.PropTypes.func.isRequired
}
export default InputDotacion