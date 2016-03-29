// Librerias
import React from 'react'

// Estilos
import css from './InputHora.css'

class InputHora extends React.Component{
    constructor(props) {
        super(props)
        this.state = {
            hora: '01:01:00',
            dirty: false
        }
    }
    componentWillMount(){
        this.setState({
            hora: this.props.asignada,
            dirty: false
        })
    }
    componentWillReceiveProps(nextProps){
        // apenas se reciba una nueva propiedad, reemplazar el estado independiente de su contenido
        let hora2 = nextProps.asignada
        this.setState({
            hora: hora2,
            dirty: false
        })
    }

    inputOnKeyDown(evt){
        if((evt.keyCode===9 && evt.shiftKey===false) || /*evt.keyCode===40 ||*/ evt.keyCode===13){
            // 9 = tab, flechaAbajo = 40,  13 = enter
            evt.preventDefault()
            this.props.onGuardar()
            this.props.focusRowSiguiente()

        }else if((evt.keyCode===9 && evt.shiftKey===true) /*|| evt.keyCode===38*/) {
            // flechaArriba = 38, shift+tab
            evt.preventDefault()
            this.props.onGuardar()
            this.props.focusRowAnterior()
        }
    }
    onInputChange(evt){
        evt.preventDefault()
        let hora = evt.target.value
        this.setState({
            hora: hora,
            dirty: this.props.asignada!=hora
        })
    }
    getEstado(){
        return this.state
    }
    focus(){
        this.inputHora.focus()
    }
    render(){
        return(
            <input
                type="time"
                className={(this.state.dirty? css.inputHoraDirty : css.inputHora)+' '+this.props.className}
                style={this.props.style}
                ref={ref=>this.inputHora=ref}
                value={this.state.hora}
                onKeyDown={this.inputOnKeyDown.bind(this)}
                onChange={this.onInputChange.bind(this)}
                onBlur={()=>this.props.onGuardar()}
            />
        )
    }
}
InputHora.propTypes = {
    // Objetos
    asignada: React.PropTypes.string.isRequired,
    // Metodos
    focusRowAnterior: React.PropTypes.func.isRequired,
    focusRowSiguiente: React.PropTypes.func.isRequired,
    onGuardar: React.PropTypes.func.isRequired
}
export default InputHora