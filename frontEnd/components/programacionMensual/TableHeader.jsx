import React from 'react'
let PropTypes = React.PropTypes

// Styles
import * as style from './TableHeader.css'

class TableHeader extends React.Component {
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
        // todo mejorar esto

        let noSeleccionados = this.props.filtro.filter(opc=>!opc.seleccionado)
        //console.log("todos seleccionados: ", noSeleccionados.length===0)
        return noSeleccionados.length===0
    }
    toggleTodos(){
        let todosSeleccionados = this.revisarTodosSeleccionados()

        // si estan todos seleccionados, marcar ninguno
        // si falta uno por marcar, se marcan todos
        let filtroActualizado = this.props.filtro.map(opcion=>({texto: opcion.texto, seleccionado: !todosSeleccionados}))

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
            <div className={style.container} ref={ref=>this.node=ref}>
                <div className={style.cell} onClick={this.toggleMenu.bind(this)}>
                    {this.props.nombre}
                    <span className={"glyphicon pull-right "+(this.state.open? 'glyphicon-triangle-top': 'glyphicon-triangle-bottom')}></span>
                </div>
                <div className={style.menu} style={{display: this.state.open? '': 'none'}}>
                    <div className={style.contenedorValores}>
                        {this.props.filtro.map((opcion, index)=>
                            <label key={index}>
                                <input type="checkbox"
                                       onChange={this.checkboxSeleccionado.bind(this, opcion)}
                                       checked={opcion.seleccionado}
                                /> {opcion.texto}
                            </label>
                        )}
                    </div>
                    <label><input type="checkbox" onChange={this.toggleTodos.bind(this)} checked={this.revisarTodosSeleccionados.call(this)}/>Todos</label>
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

TableHeader.propTypes = {
    nombre: PropTypes.string,
    filtro: PropTypes.arrayOf(PropTypes.object).isRequired,
    // Metodos
    actualizarFiltro: PropTypes.func.isRequired
}
TableHeader.defaultProps = {
    nombre: '[sin nombre]'
}
export default TableHeader