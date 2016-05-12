import React from 'react'
let PropTypes = React.PropTypes

export class FormularioUsuario extends React.Component {
// constructor(props) {
//     super(props)
// }
    render(){
        
    }
    
}

FormularioUsuario.propTypes = {
    showModal: PropTypes.bool.isRequired,
    onAccept: PropTypes.func.isRequired,
    onCancel: PropTypes.func.isRequired
}