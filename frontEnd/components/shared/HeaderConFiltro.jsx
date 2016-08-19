import React from 'react'
let PropTypes = React.PropTypes

// Styles
import * as css from './HeaderConFiltro.css'

class HeaderConFiltro extends React.Component {
    constructor(props){
        super(props)
        this.state = {
            open: false,
            filtro: []
        }
    }
    componentWillReceiveProps(nextProps){
        // actualizar el filtro cuando se reciban nuevas propiedades
        this.setState({filtro: nextProps.filtro})
    }

    // Metodos para controlar el display del menu, ocultar y mostrar los elementos
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
            filtro: this.props.filtro
        })
    }
    cerrarMenu_aplicarCambios(){
        // ocultar el menu e informar la actualizacion del filtro al padre
        this.setState({open: false})
        this.props.actualizarFiltro(this.state.filtro)
    }

    // Elementos seleccionados
    estanTodosSeleccionados(){
        // todos seleccionados = el total de '.selecconado=false' es cero
        return this.state.filtro.filter(opc=>opc.seleccionado===false).length===0
    }
    toggleTodos(){
        let todosSeleccionados = this.estanTodosSeleccionados()
        this.setState({
            filtro: this.state.filtro.map(opcion=>{
                return {
                    // si estan todos seleccionados, marcar ninguno
                    // si falta uno por marcar, se marcan todos
                    seleccionado: !todosSeleccionados,
                    texto: opcion.texto,
                    valor: opcion.valor
                }
            })
        })
    }

    seleccionarOpcion(valorSeleccionado){
        this.setState({
            filtro: this.state.filtro.map(opcion=>{
                return {
                    // si es la opcion que estamos buscando, se cambia su estado 'seleccionado', si no lo dejamos igual
                    seleccionado: opcion.valor==valorSeleccionado? !opcion.seleccionado : opcion.seleccionado,
                    texto: opcion.texto,
                    valor: opcion.valor
                }
            })
        })
    }

    // Se busca un elemento a travez del campo InputTexto
    onKeyDownInputTexto(evt){
        // 13 = enter
        if(evt.keyCode===13){
            // tanto al texto buscado, como el de los input, se la quitan los espacios y se pone en minusculas
            let texto = this.refInputTexto.value.trim().toLowerCase()

            let filtroActualizado = this.state.filtro.map(opcion=>{
                let textoOpcion = opcion.texto.trim().toLowerCase()

                // Si se realiza una "busqueda exacta" se comparan los string, de lo contrario
                // busca que este incluido en el texto
                opcion.seleccionado = this.props.busquedaExacta? textoOpcion==texto : textoOpcion.includes(texto)
                return opcion
            })
            // luego de buscar, se oculta la busqueda
            this.refInputTexto.value = ''
            // this.closeMe nu()
            this.setState({open: false})

            // informar la actualizacion al padre
            this.props.actualizarFiltro(filtroActualizado)
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
                    {this.props.ordenarLista?
                        <span className={'glyphicon glyphicon-sort-by-attributes '+css.spanOrdenar}
                              onClick={this.props.ordenarLista}
                        /> : null
                    }
                     <span className={
                              (this.estanTodosSeleccionados()? '': css.filtroColoreado)+
                              (this.state.open? ' glyphicon glyphicon-triangle-top ': ' glyphicon glyphicon-triangle-bottom ')
                            }
                           onClick={this.toggleMenu.bind(this)}
                     />
                </div>
                <div className={css.menu} style={{display: this.state.open? '': 'none'}}>
                    {/* Buscar por texto */}
                    <input type="text" className={css.inputTexto}
                           placeholder="Filtrar por..."
                           ref={ref=>this.refInputTexto=ref}
                           onKeyDown={this.onKeyDownInputTexto.bind(this)}
                    />
                    {/* Lista de Opciones */}
                    <div className={css.contenedorValores}>
                        {this.state.filtro.map((opcion, index)=>
                            <label key={index}>
                                <input type="checkbox"
                                       onChange={this.seleccionarOpcion.bind(this, opcion.valor)}
                                       checked={!!opcion.seleccionado}
                                /> {opcion.texto}
                            </label>
                        )}
                    </div>
                    {/* Seleccionar todos */}
                    <label>
                        <input type="checkbox"
                               onChange={this.toggleTodos.bind(this)}
                               checked={this.estanTodosSeleccionados.call(this)}
                        />Todos
                    </label>
                    {/* Boton Buscar */}
                    <a href="#"
                       className="btn btn-block btn-sm btn-primary"
                       onClick={this.cerrarMenu_aplicarCambios.bind(this)}>
                        Aceptar
                    </a>
                </div>
            </div>
        )
    }
}

HeaderConFiltro.propTypes = {
    nombre: PropTypes.string,
    busquedaExacta: PropTypes.bool,
    filtro: PropTypes.arrayOf(PropTypes.object).isRequired,
    // Metodos
    actualizarFiltro: PropTypes.func.isRequired,
    ordenarLista: PropTypes.func
}
HeaderConFiltro.defaultProps = {
    nombre: '[sin nombre]',
    busquedaExacta: false,
    ordenarLista: null
}
export default HeaderConFiltro