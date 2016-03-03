import React from 'react'
let PropTypes = React.PropTypes

// Componentes

// Styles
import style from './Cabecera.css'

class Cabecera extends React.Component {
    constructor(props){
        super(props)
        this.state = {
            open: false
        }
        this.onClickHandler = this.onClickHandler.bind(this)
        this.closeMenu = this.closeMenu.bind(this)
        this.toggleMenu = this.toggleMenu.bind(this)
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


    aplicarFiltro(){

    }
    render(){
        //console.log(this.props.valores)
        return (
            <div className={style.container} ref={ref=>this.node=ref}>
                <div className={style.cell} onClick={this.toggleMenu}>
                    {this.props.nombre}
                    <span className={"glyphicon pull-right "+(this.state.open? 'glyphicon-triangle-top': 'glyphicon-triangle-bottom')}></span>
                </div>
                <div className={style.menu} style={{display: this.state.open? '': 'none'}}>
                    <div className={style.contenedorValores}>
                        {this.props.valores.map((valor, index)=>
                            <label>
                                <input type="checkbox"/> {valor}
                            </label>
                        )}
                        <label><input type="checkbox"/>Maule</label>
                        <label><input type="checkbox"/>Santiago</label>
                        <label><input type="checkbox"/>Norte Grande</label>
                        <label><input type="checkbox"/>Iquique</label>
                        <label><input type="checkbox"/>Arica</label>
                        <label><input type="checkbox"/>Osorno</label>
                    </div>
                    <label><input type="checkbox"/>Todos</label>
                    <input type="text" placeholder="..."/>
                    <button className="btn btn-sm btn-block btn-default">Aceptar</button>
                </div>
            </div>
        )
    }
}

export default Cabecera