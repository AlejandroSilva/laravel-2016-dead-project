// Librerias
import React from 'react'

// Estilos
import css from './InputDotacion.css'
//import styleShared from '../shared/shared.css'

class InputDotacionCaptador extends React.Component{
    render(){
        return(
            <input style={this.props.style}
                className={css.inputDotacion}
                type="number"
                defaultValue={this.props.asignada}
                //onChange={this.onInputDotacionCaptadorChange.bind(this)}
                //onKeyDown={this.inputOnKeyDown.bind(this, 'asignada')}
                //onBlur={this.guardarOCrear.bind(this)}/>
            />
        )
    }
}
InputDotacionCaptador.propTypes = {
    // Objetos
    asignada: React.PropTypes.string.isRequired
}
export default InputDotacionCaptador