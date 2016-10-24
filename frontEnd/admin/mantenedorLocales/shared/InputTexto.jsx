// Librerias
import React from 'react'

export class InputTexto extends React.Component{
    constructor(props) {
        super(props)
        this.state = {
            valor: '',
            dirty: false,
            valid: true
        }
    }
    componentWillMount(){
        this.setState({
            valor: this.props.valor,
            dirty: false,
            valid: true
        })
    }
    componentWillReceiveProps(nextProps){
        // apenas se reciba una nueva propiedad, reemplazar el estado independiente de su contenido
        let nuevoValor = nextProps.valor
        this.setState({
            valor: nuevoValor,
            dirty: false,
            valid: true
        })
    }

    getEstado(){
        return this.state
    }
    focus(){
        // this.inputText.focus()
        this.inputText.select() // al hacer focus, se selecciona el texto
    }

    inputOnKeyDown(evt){
        let code = evt.keyCode
        // buscar el callback dentro del array 'onKeyPress'
        if(this.props.onKeyPress[code]){
            this.props.onKeyPress[code]()
        }
    }
    onInputChange(evt){
        let valorUsuario = evt.target.value
        this.setState({
            valor: valorUsuario,
            dirty: this.props.valor!=valorUsuario,
            valid: this.props.validador(valorUsuario)
        })
    }

    render(){
        let { defaultClass, dirtyClass, errorClass, disabled } = this.props

        // prioridad del style: valido > dirty > input
        let classname =  defaultClass +' '+
            (!this.state.valid? errorClass : '' ) +' '+
            (this.state.dirty? dirtyClass : '')

        return (
            <input
                className={classname}
                ref={ref=>this.inputText=ref}
                value={this.state.valor}
                disabled={disabled}
                
                onKeyDown={this.inputOnKeyDown.bind(this)}
                onChange={this.onInputChange.bind(this)}
                onBlur={()=>this.props.onFocusLost()}
                onFocus={()=>{ this.inputText.select() }}             // seleccionar el texto cuando se hace focus
            />
        )
    }
}
InputTexto.propTypes = {
    // Objetos
    valor: React.PropTypes.string.isRequired,
    defaultClass: React.PropTypes.string.isRequired,
    dirtyClass: React.PropTypes.string.isRequired,
    errorClass: React.PropTypes.string.isRequired,
    // disabled
    onKeyPress: React.PropTypes.array.isRequired,

    // Metodos
    onFocusLost: React.PropTypes.func.isRequired,
    validador: React.PropTypes.func.isRequired
}
InputTexto.defaultProps = {
    validador: (texto)=>{return true}   // por defecto, el texto siempre es valido
}