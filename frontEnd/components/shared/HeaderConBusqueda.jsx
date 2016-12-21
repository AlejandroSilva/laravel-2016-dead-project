import React from 'react'
let PropTypes = React.PropTypes

// Styles
import * as css from './HeaderConBusqueda.css'

export class HeaderConBusqueda extends React.Component {
    constructor(props){
        super(props)
        this.state = {
            open: false,
            busqueda: this.props.busqueda
        }
    }
    componentWillReceiveProps(nextProps){
        this.setState({busqueda: nextProps.busqueda})
    }
    componentDidMount() {
        document.addEventListener('click', this.onClickHandler.bind(this))
    }
    componentWillUnmount() {
        document.removeEventListener('click', this.onClickHandler.bind(this))
    }
    onClickHandler(evt) {
        // detecta cuando se hace un click fuera de este componente, cuando eso pasa y el menu es visible, lo oculta
        if(!this.node.contains(evt.target) && this.state.open)
            this.cerrarMenu_descartarCambios()
    }
    toggleMenu(){
        // si el menu se oculta se descartan los cambios
        if(this.state.open)
            this.cerrarMenu_descartarCambios()
        else
            this.setState({open: true})
    }
    cerrarMenu_descartarCambios(){
        this.setState({
            open: false,
            busqueda: this.props.busqueda
        })
    }
    cerrarMenu_aplicarCambios(){
        // ocultar el menu e informar la actualizacion del filtro al padre
        this.setState({open: false, busqueda: ''})
        this.props.realizarBusqueda(this.state.busqueda)
    }

    // Elementos seleccionados
    destacarTriangulo(){
        return this.state.busqueda.trim()!==''
    }
    onInputChange(evt){
        this.setState({busqueda: evt.target.value})
    }
    onKeyDownInputTexto(evt){
        if(evt.keyCode===13){
            this.cerrarMenu_aplicarCambios()
        }
    }

    render(){
        return (
            <div className={css.container}
                 ref={ref=>this.node=ref}>
                <div className={css.divTitulo}
                     onClick={this.toggleMenu.bind(this)}>
                    {this.props.nombre}
                </div>
                <div className={'pull-right'}>
                    <span className={
                        (this.destacarTriangulo()? css.filtroColoreado : '')+
                        (this.state.open? ' glyphicon glyphicon-triangle-top ': ' glyphicon glyphicon-triangle-bottom ')
                    }
                          onClick={this.toggleMenu.bind(this)}
                    />
                </div>
                <div className={css.menu} style={{display: this.state.open? '': 'none'}}>
                    {/* Buscar por texto */}
                    <input type="text" className={css.inputTexto}
                           placeholder="Buscar por..."
                           value={this.state.busqueda}
                           onChange={this.onInputChange.bind(this)}
                           onKeyDown={this.onKeyDownInputTexto.bind(this)}
                    />
                    {/* Boton Buscar */}
                    <a href="#"
                       className="btn btn-block btn-xs btn-primary"
                       onClick={this.cerrarMenu_aplicarCambios.bind(this)}>
                        Buscar
                    </a>
                </div>
            </div>
        )
    }
}
HeaderConBusqueda.propTypes = {
    nombre: PropTypes.string,
    busqueda: PropTypes.string,
    realizarBusqueda: PropTypes.func.isRequired,
}
HeaderConBusqueda.defaultProps = {
    nombre: '[sin nombre]'
}