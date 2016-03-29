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
        // apenas se reciba una nueva propiedad, reemplazar el estado independiente de su contenido
        let dotacion2 = nextProps.asignada
        this.setState({
            dotacion: dotacion2,
            dirty: false,
            valid: true
        })
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
                className={classname + " " + this.props.className}
                style={this.props.style}
                ref={ref=>this.inputDotacion=ref}
                type="number"
                value={this.state.dotacion}
                onKeyDown={this.inputOnKeyDown.bind(this)}
                onChange={this.onInputChange.bind(this)}
                onBlur={()=>this.props.onGuardar()}
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
    onGuardar: React.PropTypes.func.isRequired
}
export default InputDotacion