// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes

export class MantenedorUsuarios extends React.Component {
    constructor(props) {
        super(props)
    }
    render(){
        return (
            <h4>MantenedorUsuarios</h4>
        )
    }
}

MantenedorUsuarios.propTypes = {
    numero: PropTypes.number.isRequired,
    texto: PropTypes.string.isRequired,
    objeto: PropTypes.object.isRequired,
    arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}