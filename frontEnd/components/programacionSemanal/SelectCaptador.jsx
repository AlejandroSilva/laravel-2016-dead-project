// Librerias
import React from 'react'

// Estilos
//import css from './InputDotacion.css'

class SelectCaptador extends React.Component{
    render(){
        return(
            <select name="" id="">
                <option value="0" disabled>-</option>
                <option value="1">Captador 1</option>
                <option value="2">Captador 2</option>
                <option value="3">Captador 3</option>
            </select>
        )
    }
}
SelectCaptador.propTypes = {
    // Objetos
    //fecha: React.PropTypes.string.isRequired
}
export default SelectCaptador