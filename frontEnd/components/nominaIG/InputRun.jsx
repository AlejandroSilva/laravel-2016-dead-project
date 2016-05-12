import React from 'react'
let PropTypes = React.PropTypes

// Componentes
//import * as css from './nominaIG.css'

export class InputRun extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            RUN: ''
        }
    }

    onInputChange(evt){
        let input = evt.target.value
        if(input=='')
            return
        this.setState({
            RUN: parseInt(input)
        })
    }
    inputOnKeyDown(evt){
        if((evt.keyCode===9 && evt.shiftKey===false) || evt.keyCode===40 || evt.keyCode===13) {
            // 9 = tab, flechaAbajo = 40,  13 = enter
            if(this.state.RUN!==''){
                this.props.buscarUsuario(''+this.state.RUN)
                evt.preventDefault()
            }
        }else if((evt.keyCode===9 && evt.shiftKey===true) || evt.keyCode===38) {
            // flechaArriba = 38, shift+tab
            if(this.state.RUN!==''){
                this.props.buscarUsuario(''+this.state.RUN)
                evt.preventDefault()
            }
        }
    }

    render(){
        return (
            <input type="text"
                value={this.state.RUN}
                   onChange={this.onInputChange.bind(this)}
                   onKeyDown={this.inputOnKeyDown.bind(this)}
            />
        )
    }
}

InputRun.propTypes = {
    buscarUsuario: PropTypes.func.isRequired
    // nomina: PropTypes.object.isRequired,
}
InputRun.defaultProps = {
    // habilitado: true
}