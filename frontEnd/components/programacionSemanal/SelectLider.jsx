// Librerias
import React from 'react'

// Estilos
//import css from './SelectLider.css'

class SelectLider extends React.Component{
    render(){
        return(
            <select name="" style={this.props.style}>
                {this.props.lideres.map((lid, index)=>
                    <option key={index} value={lid.id}>{`${lid.nombre1} ${lid.apellidoPaterno}`}</option>
                )}
            </select>
        )
    }
}
SelectLider.propTypes = {
    // Objetos
    lideres: React.PropTypes.array.isRequired
}
export default SelectLider