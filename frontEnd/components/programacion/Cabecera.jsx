import React from 'react'
let PropTypes = React.PropTypes

// Componentes

// Styles
import style from './Cabecera.css'

class Cabecera extends React.Component {
    constructor(props){
        super(props)
        // mapear prop.opciones (arreglo de string) a state.opciones (string + selected)
        let opciones = this.props.opciones.map(opcion=> ({texto: opcion, seleccionado: true}) )

        this.state = {
            open: false,
            opciones,
            todosSeleccionados: true
        }

        // display del menu
        this.onClickHandler = this.onClickHandler.bind(this)
        this.closeMenu = this.closeMenu.bind(this)
        this.toggleMenu = this.toggleMenu.bind(this)
        // elementos seleccionados
        this.revisarTodosSeleccionados = this.revisarTodosSeleccionados.bind(this)
        this.toggleTodos = this.toggleTodos.bind(this)
        this.toggleTodos = this.toggleTodos.bind(this)
    }
    // Metodos para controlar el display del menu, ocultar y mostrar los elementos
    componentDidMount() {
        document.addEventListener('click', this.onClickHandler);
    }
    componentWillUnMount() {
        document.removeEventListener('click', this.onClickHandler);
    }
    onClickHandler({ target }) {
        // detecta cuando se hace un click fuera de este componente, cuando eso pasa y el menu es visible, lo oculta
        if(!this.node.contains(target) && this.state.open)
            this.closeMenu()
    }
    closeMenu(){
        this.setState({open: false})
    }
    toggleMenu(){
        this.setState({open: !this.state.open})
    }

    componentWillReceiveProps(nextProps){
        // cuando se actualizan (o reciben los datos), se debe actualizar la lista de opciones, pero mantener su seleccion
        let nextOpciones = nextProps.opciones
        let opcionesActualizadas = nextOpciones.map(nextOpc=>{
            let opcion = this.state.opciones.find(opc=>opc.texto===nextOpc)
            // si existe, se mantienen los datos, si no, se crea la opcion
            return opcion? opcion : {
                texto: nextOpc,
                seleccionado: true
            }
        })

        console.log('x ', this.revisarTodosSeleccionados(opcionesActualizadas) )
        this.setState({
            opciones: opcionesActualizadas,
            todosSeleccionados: this.revisarTodosSeleccionados(opcionesActualizadas)
        })
    }

    // Elementos seleccionados
    revisarTodosSeleccionados(opciones){
        let noSeleccionados = opciones.filter(opc=>!opc.seleccionado)
        return noSeleccionados.length===0
    }
    toggleTodos(){
        // des-seleccionar todos los elementos
        if(this.state.todosSeleccionados){
            this.setState({
                opciones: this.state.opciones.map(opcion=>({...opcion, seleccionado: false}) ),
                todosSeleccionados: false
            })
        }else{
            this.setState({
                opciones: this.state.opciones.map(opcion=>({...opcion, seleccionado: true}) ),
                todosSeleccionados: true
            })
        }
    }

    checkboxSeleccionado(opcionSeleccionada){
        let opcionesActualizadas = this.state.opciones.map(opcion=>{
            if(opcion===opcionSeleccionada)
                opcion.seleccionado = !opcion.seleccionado
            return opcion
        })

        console.log('y ', this.revisarTodosSeleccionados(opcionesActualizadas) )
        // cuando se selecciona una opcion, se cambia su campo 'seleccionado'
        this.setState({
            opciones: opcionesActualizadas,
            todosSeleccionados: this.revisarTodosSeleccionados(opcionesActualizadas)
        })
        // chequear si estan todos seleccionados
    }

    render(){
        console.log(this.state.opciones)
        return (
            <div className={style.container} ref={ref=>this.node=ref}>
                <div className={style.cell} onClick={this.toggleMenu}>
                    {this.props.nombre}
                    <span className={"glyphicon pull-right "+(this.state.open? 'glyphicon-triangle-top': 'glyphicon-triangle-bottom')}></span>
                </div>
                <div className={style.menu} style={{display: this.state.open? '': 'none'}}>
                    <div className={style.contenedorValores}>
                        {this.state.opciones.map((opcion, index)=>
                            <label key={index}>
                                <input type="checkbox"
                                       onChange={this.checkboxSeleccionado.bind(this, opcion)}
                                       checked={opcion.seleccionado}
                                /> {opcion.texto}
                            </label>
                        )}
                        {/*
                        <label><input type="checkbox" ref={ref=>this.maule=ref} defaultValue="asdasdasd"/>Maule</label>
                        <label><input type="checkbox"/>Santiago</label>
                        <label><input type="checkbox"/>Norte Grande</label>
                        <label><input type="checkbox"/>Iquique</label>
                        <label><input type="checkbox"/>Arica</label>
                        <label><input type="checkbox"/>Osorno</label>
                         */}
                    </div>
                    <label><input type="checkbox" onChange={this.toggleTodos} checked={this.state.todosSeleccionados}/>Todos</label>
                    <input type="text" placeholder="..."/>
                    <button className="btn btn-sm btn-block btn-default">Aceptar</button>
                </div>
            </div>
        )
    }
}

export default Cabecera