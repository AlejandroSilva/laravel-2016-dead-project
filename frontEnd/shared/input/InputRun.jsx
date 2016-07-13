import React from 'react'
let PropTypes = React.PropTypes
// Validador Rut
import { obtenerVerificador } from '../ValidadorRUN'
// Componentes

export class InputRun extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            RUN: '',
            RV: ''
        }
    }

    onInputChange(evt){
        let RUN = evt.target.value

        // si los campos de textos no son validos, entonces no deja que los campos se modifiquen
        if(RUN===''){
            this.setState({RUN: '', DV: ''}, ()=>{
                this.props.onRUNChange('', '')
            })
        }
        // es valido si tiene de 1 a 8 digitos
        if(/^[0-9]{1,8}$/.test(RUN)){
            let DV = obtenerVerificador(RUN)
            this.setState({RUN, DV}, ()=>{
                this.props.onRUNChange(RUN, DV)
            })
        }
    }
    inputOnKeyDown(evt){
        if((evt.keyCode===9 && evt.shiftKey===false) || evt.keyCode===40 || evt.keyCode===13) {
            // 9 = tab, flechaAbajo = 40,  13 = enter
            this.props.onPressEnter(this.state.RUN)
            this.setState({RUN:'', DV:''})
            evt.preventDefault()
        }else if((evt.keyCode===9 && evt.shiftKey===true) || evt.keyCode===38) {
            // flechaArriba = 38, shift+tab
            this.props.onPressEnter(this.state.RUN)
            this.setState({RUN:'', DV:''})
            evt.preventDefault()
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
    onPressEnter: PropTypes.func.isRequired,
    onRUNChange: PropTypes.func.isRequired
    // nomina: PropTypes.object.isRequired,
}
InputRun.defaultProps = {
    // habilitado: true
}