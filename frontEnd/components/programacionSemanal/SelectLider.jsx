// Librerias
import React from 'react'

// Estilos
//import css from './SelectLider.css'

class SelectLider extends React.Component{
    render(){
        return(
            <div>
                <select name="" id="">
                    <option value="0">-</option>
                    <option value="1">Sup 1</option>
                    <option value="2">Sup 2</option>
                    <option value="3">Sup 3</option>
                </select>
            </div>
        )
    }
}
SelectLider.propTypes = {
    // Objetos
    //fecha: React.PropTypes.string.isRequired
}
export default SelectLider