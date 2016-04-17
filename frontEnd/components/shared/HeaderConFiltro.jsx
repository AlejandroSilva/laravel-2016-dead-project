import React from 'react'
let PropTypes = React.PropTypes

// Styles
import * as css from './HeaderConFiltro.css'

class HeaderConFiltro extends React.Component {
    constructor(props){
        super(props)
        this.state = {
            open: false
        }
    }
    // Metodos para controlar el display del menu, ocultar y mostrar los elementos
    componentDidMount() {
        document.addEventListener('click', this.onClickHandler.bind(this))
    }
    componentWillUnMount() {
        document.removeEventListener('click', this.onClickHandler.bind(this))
    }
    onClickHandler(evt) {
        // detecta cuando se hace un click fuera de este componente, cuando eso pasa y el menu es visible, lo oculta
        if(!this.node.contains(evt.target) && this.state.open)
            this.closeMenu()
    }

    closeMenu(){
        this.setState({open: false})
    }
    toggleMenu(){
        this.setState({open: !this.state.open})
    }

    // Elementos seleccionados
    revisarTodosSeleccionados(/*opciones*/){
        return this.props.filtro.filter(opc=>opc.seleccionado===false).length===0
    }
    toggleTodos(){
        let todosSeleccionados = this.revisarTodosSeleccionados()
        // si estan todos seleccionados, marcar ninguno
        // si falta uno por marcar, se marcan todos
        let filtroActualizado = this.props.filtro.map(opcion=>{
            opcion.seleccionado = !todosSeleccionados
            return opcion
        })

        // informar la actualizacion al padre
        this.props.actualizarFiltro(filtroActualizado)
    }
    checkboxSeleccionado(opcionSeleccionada){
        let filtroActualizado = this.props.filtro.map(opcion=>{
            if(opcion===opcionSeleccionada)
                opcion.seleccionado = !opcion.seleccionado
            return opcion
        })
        // informar la actualizacion al padre
        this.props.actualizarFiltro(filtroActualizado)
    }

    render(){
        return (
            <div className={css.container}
                 ref={ref=>this.node=ref}>
                <div onClick={this.toggleMenu.bind(this)}>
                    {this.props.nombre}
                     <span className={'glyphicon pull-right '+
                        (this.state.open? 'glyphicon-triangle-top ': 'glyphicon-triangle-bottom ')+
                        (this.revisarTodosSeleccionados()? '': css.filtroColoreado)
                    }></span>
                </div>
                <div className={css.menu} style={{display: this.state.open? '': 'none'}}>
                    <div className={css.contenedorValores}>
                        {this.props.filtro.map((opcion, index)=>
                            <label key={index}>
                                <input type="checkbox"
                                       onChange={this.checkboxSeleccionado.bind(this, opcion)}
                                       checked={opcion.seleccionado}
                                /> {opcion.texto}
                            </label>
                        )}
                    </div>
                    <label>
                        <input type="checkbox"
                               onChange={this.toggleTodos.bind(this)}
                               checked={this.revisarTodosSeleccionados.call(this)}
                        />Todos
                    </label>
                    <a href="#"
                       className="btn btn-block btn-sm btn-primary"
                       onClick={this.closeMenu.bind(this)}>
                        Aceptar
                    </a>
                </div>
            </div>
        )
    }
}

HeaderConFiltro.propTypes = {
    nombre: PropTypes.string,
    filtro: PropTypes.arrayOf(PropTypes.object).isRequired,
    // Metodos
    actualizarFiltro: PropTypes.func.isRequired
}
HeaderConFiltro.defaultProps = {
    nombre: '[sin nombre]'
}
export default HeaderConFiltro