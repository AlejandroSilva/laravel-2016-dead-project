// Librerias
import React from 'react'

// Estilos
//import css from './InputDotacion.css'

class SelectCaptador extends React.Component{
    render(){
        return(
            <select name="" style={this.props.style}>
                {this.props.captadores.map((cap, index)=>
                    <option key={index} value={cap.id}>{`${cap.nombre1} ${cap.apellidoPaterno}`}</option>
                )}
            </select>
        )
    }
}
SelectCaptador.propTypes = {
    // Objetos
    captadores: React.PropTypes.array.isRequired
}
export default SelectCaptador