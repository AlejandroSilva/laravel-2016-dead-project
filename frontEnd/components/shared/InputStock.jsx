// Librerias
import React from 'react'

// Estilos
import * as css from './InputStock.css'

class InputStock extends React.Component{
    constructor(props) {
        super(props)
        this.state = {
            valor: '-',
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
        this.setState({
            valor: nextProps.asignada,
            dirty: false,
            valid: true
        })
    }

    inputOnKeyDown(evt){
        if((evt.keyCode===9 && evt.shiftKey===false) || evt.keyCode===40 || evt.keyCode===13){
            // 9 = tab, flechaAbajo = 40,  13 = enter
            evt.preventDefault()
            //this.props.onGuardar()    // se guarda al perder el focus
            this.props.focusRowSiguiente()

        }else if((evt.keyCode===9 && evt.shiftKey===true) || evt.keyCode===38) {
            // flechaArriba = 38, shift+tab
            evt.preventDefault()
            //this.props.onGuardar()    // se guarda al perder el focus
            this.props.focusRowAnterior()
        }
    }
    onInputChange(evt){
        let valorUsuario = evt.target.value
        this.setState({
            valor: valorUsuario,
            dirty: this.props.asignada!=valorUsuario,
            valid: valorUsuario>=0
        })
    }
    getEstado(){
        return this.state
    }
    focus(){
        this.inputStock.focus()
        this.inputStock.select()
    }
    render(){
        let classname = this.state.valid
            ? (this.state.dirty? css.inputStockDirty : css.inputStock)
            : css.inputStockInvalida
        return <div className={css.divConTooltip}>
            <input
                className={classname + " " + this.props.className}
                style={this.props.style}
                ref={ref=>this.inputStock=ref}
                type="number"
                value={this.state.valor}
                onKeyDown={this.inputOnKeyDown.bind(this)}
                onChange={this.onInputChange.bind(this)}
                onBlur={()=>this.props.onGuardar()}
                onFocus={()=>{ this.inputStock.select() }}             // seleccionar el texto cuando se hace focus
                disabled={this.props.puedeModificar? '':'disabled'}
            />
            {/* el tooltip es opcional */}
            {this.props.tooltipText?
                <div className={css.tooltip}>
                    {this.props.tooltipText}
                </div>
                :
                null
            }
        </div>
    }
}
InputStock.propTypes = {
    // Objetos
    puedeModificar: React.PropTypes.bool.isRequired,
    asignada: React.PropTypes.string.isRequired,
    tooltipText: React.PropTypes.string,
    // Metodos
    focusRowAnterior: React.PropTypes.func.isRequired,
    focusRowSiguiente: React.PropTypes.func.isRequired,
    onGuardar: React.PropTypes.func.isRequired
}
export default InputStock