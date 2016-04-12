// Librerias
import React from 'react'

// Estilos
import css from './Select.css'

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
                    className={(this.state.dirty? css.selectDirty : css.select)+' '+this.props.className}
                    value={this.state.seleccionUsuario}
                    onChange={this.onInputChange.bind(this)}
                    disabled={this.props.puedeModificar? '':'disabled'}
                    style={this.props.style}>
                {this.props.opcionNula
                    ? <option value={''} disabled={this.props.opcionNulaSeleccionable===false}>--</option>
                    : null
                }
                {this.props.opciones.map((opcion, index)=>
                    <option key={index} value={opcion.valor}>{opcion.texto}</option>
                )}
            </select>
        )
    }
}
Select.propTypes = {
    // Objetos
    puedeModificar: React.PropTypes.bool.isRequired,
    seleccionada: React.PropTypes.string.isRequired,
    opciones: React.PropTypes.array.isRequired,
    opcionNula: React.PropTypes.bool,
    opcionNulaSeleccionable: React.PropTypes.bool,
    // Metodos
    onSelect: React.PropTypes.func.isRequired
}
Select.defaultProps = {
    opcionNula: false,
    opcionNulaSeleccionable: false
}
export default Select