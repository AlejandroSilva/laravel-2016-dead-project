// Librerias
import React from 'react'

export class Select extends React.Component{
    constructor(props) {
        super(props)
        this.state = {
            valor: '',
            dirty: false
        }
    }
    componentWillMount(){
        this.setState({
            valor: this.props.valor,
            dirty: false
        })
    }
    componentWillReceiveProps(nextProps){
        let nuevoValor = nextProps.valor
        // if(this.props.valor!==nuevoValor){
        //     // si esto cambia, entonces se debe hacer un "reset" del estado con los nuevos valores
        //     this.setState({valor: nuevoValor, dirty: false})
        // }
        // if(nuevoValor!==this.state.valor){
        //     // si se reciben propiedades distintas al "state" actual, se reemplazan por las propiedades
            this.setState({valor: nuevoValor, dirty: false})
        // }
    }
    onSelectChange(evt){
        evt.preventDefault()
        let valorSeleccionado = evt.target.value
        this.setState({
            valor: valorSeleccionado,
            dirty: this.props.valor!=valorSeleccionado
        }, ()=>{
            this.props.onSelectChange()
        })
    }

    getEstado(){
        return this.state
    }
    render(){
        let { defaultClass, dirtyClass, errorClass, disabled } = this.props
        
        // prioridad del style: valido > dirty > input
        let classname =  defaultClass +' '+
            (!this.state.valid? errorClass : '' ) +' '+
            (this.state.dirty? dirtyClass : '')
        
        return(
            <select name=""
                    className={classname}
                    value={this.state.valor}
                    onChange={this.onSelectChange.bind(this)}
                    disabled={disabled}>
                {this.props.children}
            </select>
        )
    }
}
Select.propTypes = {
    // Objetos
    valor: React.PropTypes.number.isRequired,
    // Metodos
    onSelectChange: React.PropTypes.func.isRequired
}