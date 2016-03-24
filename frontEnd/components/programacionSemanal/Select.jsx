// Librerias
import React from 'react'

// Estilos
//import css from './XXXXX.css'

class Select extends React.Component{
    constructor(props) {
        super(props)
        this.state = {
            seleccionUsuario: '-',
            dirty: false
        }
    }
    componentWillMount(){
        this.setState({
            seleccionUsuario: this.props.seleccionada,
            dirty: false
        })
    }
    componentWillReceiveProps(nextProps){
        let seleccionada1 = this.props.seleccionada
        let seleccionada2 = nextProps.seleccionada
        if(seleccionada1!==seleccionada2){
            // si esto cambia, entonces se debe hacer un "reset" del estado con los nuevos valores
            this.setState({seleccionUsuario: seleccionada2, dirty: false})
        }
        if(seleccionada2!==this.state.seleccionUsuario){
            // si se reciben propiedades distintas al "state" actual, se reemplazan por las propiedades
            this.setState({seleccionUsuario: seleccionada2, dirty: false})
        }
    }
    onInputChange(evt){
        evt.preventDefault()
        let seleccionUsuario = evt.target.value
        this.setState({
            seleccionUsuario: seleccionUsuario,
            dirty: this.props.seleccionada!=seleccionUsuario
        }, ()=>{
            this.props.onSelect()
        })
    }

    getEstado(){
        return this.state
    }
    render(){
        return(
            <select name=""
                    value={this.state.seleccionUsuario}
                    onChange={this.onInputChange.bind(this)}
                    style={this.props.style}>
                {this.props.opciones.map((opcion, index)=>
                    <option key={index} value={opcion.valor}>{opcion.texto}</option>
                )}
            </select>
        )
    }
}
Select.propTypes = {
    // Objetos
    seleccionada: React.PropTypes.string.isRequired,
    opciones: React.PropTypes.array.isRequired,
    // Metodos
    onSelect: React.PropTypes.func.isRequired
}
export default Select