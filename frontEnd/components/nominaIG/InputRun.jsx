import React from 'react'
let PropTypes = React.PropTypes

// Componentes
//import * as css from './nominaIG.css'

export class InputRun extends React.Component {
    constructor(props) {
        super(props)
    }

    render(){
        return (
            <input type="text"
                // sad
                value={'16589615'}
            />
        )
    }
}

InputRun.propTypes = {
    // inventario: PropTypes.object.isRequired,
    // nomina: PropTypes.object.isRequired,
}
InputRun.defaultProps = {
    // habilitado: true
}