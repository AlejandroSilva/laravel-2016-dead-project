// Librerias
import React from 'react'
import Tooltip from 'react-bootstrap/lib/Tooltip'
import OverlayTrigger from 'react-bootstrap/lib/OverlayTrigger'

// Estilos
import * as css from './InputTexto.css'

class InputTexto extends React.Component{
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
            valor: this.props.asignada,
            dirty: false,
            valid: true
        })
    }
    componentWillReceiveProps(nextProps){
        // apenas se reciba una nueva propiedad, reemplazar el estado independiente de su contenido
        let valor2 = nextProps.asignada
        this.setState({
            valor: valor2,
            dirty: false,
            valid: true
        })
    }

    inputOnKeyDown(evt){
        if((evt.keyCode===9 && evt.shiftKey===false) || evt.keyCode===40 || evt.keyCode===13){
            // 9 = tab, flechaAbajo = 40,  13 = enter
            evt.preventDefault()
            this.props.onGuardar()
            this.props.focusRowSiguiente()

        }else if((evt.keyCode===9 && evt.shiftKey===true) || evt.keyCode===38) {
            // flechaArriba = 38, shift+tab
            evt.preventDefault()
            this.props.onGuardar()
            this.props.focusRowAnterior()
        }
    }
    onInputChange(evt){
        let valorUsuario = evt.target.value
        this.setState({
            valor: valorUsuario,
            dirty: this.props.asignada!=valorUsuario,
            valid: this.props.validador(valorUsuario)
        })
    }
    getEstado(){
        return this.state
    }
    focus(){
        this.inputText.focus()
        this.inputText.select()
    }

    render(){
        // prioridad del style: valido > dirty > input
        let classname = this.state.valid
            ? (this.state.dirty? css.inputDirty : css.input)
            : css.inputInvalido

        return this.props.tooltipText ?
            /****
             *  WIP: terminar los input para modificar la informacion de los locales
             *
             */
            // <OverlayTrigger
            //     placement="left"
            //     delay={0}
            //     overlay={<Tooltip id="yyy">{this.props.tooltipText}</Tooltip>}>
            //     <input
            //         className={classname + " " + this.props.className}
            //         style={this.props.style}
            //         ref={ref=>this.inputText=ref}
            //         type="number"
            //         value={this.state.valor}
            //         onKeyDown={this.inputOnKeyDown.bind(this)}
            //         onChange={this.onInputChange.bind(this)}
            //         onBlur={()=>this.props.onGuardar()}
            //         onFocus={()=>{ this.inputText.select() }}             // seleccionar el texto cuando se hace focus
            //         disabled={this.props.puedeModificar? '' : 'disabled'}
            //     />
            // </OverlayTrigger>
            null
            :
            <input
                className={classname + " " + this.props.className}
                style={this.props.style}
                ref={ref=>this.inputText=ref}
                type="number"
                value={this.state.valor}
                onKeyDown={this.inputOnKeyDown.bind(this)}
                onChange={this.onInputChange.bind(this)}
                onBlur={()=>this.props.onGuardar()}
                onFocus={()=>{ this.inputText.select() }}             // seleccionar el texto cuando se hace focus
                disabled={this.props.puedeModificar? '':'disabled'}
            />
    }
}
InputTexto.propTypes = {
    // Objetos
    puedeModificar: React.PropTypes.bool.isRequired,
    asignada: React.PropTypes.string.isRequired,
    tooltipText: React.PropTypes.string,
    // Metodos
    focusRowAnterior: React.PropTypes.func.isRequired,
    focusRowSiguiente: React.PropTypes.func.isRequired,
    onGuardar: React.PropTypes.func.isRequired,
    validador: React.PropTypes.func.isRequired
}
InputTexto.defaultProps = {
    puedeModificar: false,
    validador: (texto)=>{return true}   // por defecto, el texto siempre es valido
}
export default InputTexto