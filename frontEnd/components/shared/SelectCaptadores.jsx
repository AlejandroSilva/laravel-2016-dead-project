// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes

// Styles
import * as css from './SelectCaptadores.css'
import classNames from 'classnames/bind'
let cx = classNames.bind(css)

export class SelectCaptadores extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            nuevoCaptadorVisible: false
        }
        this.mostrar_nuevoCaptador = ()=>{
            this.setState({nuevoCaptadorVisible: true})
        }
        this.ocultar_nuevoCaptador = ()=>{
            this.setState({nuevoCaptadorVisible: false})
        }
        this.captadorSeleccionado = (evt)=>{
            let idUsuario = evt.target.value
            this.props.agregarCaptador(idUsuario)
            this.ocultar_nuevoCaptador()
        }
    }
    render(){
        return this.props.visible?
            <div>
                {/* si no tiene ningun captador seleccionado, y el selector de nuevo captador no esta visible,
                    entonces se muestra un "captador-vacio", con la opcion de presionar en "+" y mostrar el selector */}
                {(this.props.captadores.length == 0 && this.state.nuevoCaptadorVisible == false) ?
                    <div className={cx('captador-vacio')} >
                        <div className={cx('captador-vacio-texto')}></div>
                        <div className={cx('captador-vacio-opcion')} onClick={this.mostrar_nuevoCaptador}></div>
                    </div>
                    :
                    null
                }

                {this.props.captadores.map(captador=>
                    <Captador
                        key={captador.idUsuario}
                        nombre={captador.nombre}
                        asignados={captador.asignados}
                        agregarCaptador={this.mostrar_nuevoCaptador}
                        quitarCaptador={this.props.quitarCaptador.bind(this, captador.idUsuario)}
                        cambiarAsignados={this.props.cambiarAsignados.bind(this, captador.idUsuario)}
                    />
                )}
                <NuevoCaptador
                    visible={this.state.nuevoCaptadorVisible}
                    captadoresDisponibles={this.props.captadoresDisponibles}
                    ocultar_nuevoCaptador={this.ocultar_nuevoCaptador}
                    captadorSeleccionado={this.captadorSeleccionado}
                />
            </div>
            :
            null
    }
}
SelectCaptadores.propTypes = {
    // objetos
    captadoresDisponibles: PropTypes.arrayOf(PropTypes.object).isRequired,
    // metodos
    agregarCaptador: PropTypes.func.isRequired,
    quitarCaptador: PropTypes.func.isRequired,
    cambiarAsignados: PropTypes.func.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}

/* ############################################### SELECTOR CAPTADOR ################################################ */
// todo: mostrar boton de confirmacion al tratar de eliminar
class Captador extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            optionsVisible: false,
            inputAsignados: this.props.asignados
        }
        // Metodos del menu de opciones
        this.__onClickHandler__ = (evt)=>{
            // detecta cuando se hace un click fuera de este componente, cuando eso pasa y el menu es visible, lo oculta
            let clickAfuera = !this.node.contains(evt.target) && !this.nodeTrigger.contains(evt.target)
            if(clickAfuera && this.state.optionsVisible)
                this._hideMenu()
        }
        this.toggleMenu = ()=> {
            if (this.state.optionsVisible)
                this._hideMenu()
            else
                this._showMenu()
        }
        this._showMenu = ()=> {
            this.setState({optionsVisible: true})
        }
        this._hideMenu = ()=> {
            this.setState({optionsVisible: false})
        }

        this.agregarCaptador = ()=>{
            this._hideMenu()
            this.props.agregarCaptador()
        }
        this.cambiarAsignados = ()=>{
            if(this.state.inputAsignados!=this.props.asignados)
                this.props.cambiarAsignados(this.state.inputAsignados)
            else
                console.log('asignado e inputAsignados son iguales, no se hace el cambio')
        }
        this._onChange = (evt)=>{
            this.setState({inputAsignados: evt.target.value})
        }
        this._onKeyDown = (evt)=>{
            if(evt.keyCode===13){
                this.cambiarAsignados()
            }
        }
    }
    componentDidMount() {
        document.addEventListener('click', this.__onClickHandler__)
    }
    componentWillUnmount() {
        document.removeEventListener('click', this.__onClickHandler__)
    }
    componentWillReceiveProps(nextProps){
        // cuando se recibe una nueva propiedad, se actualiza en el estado
        // interno (sirve cuando se actualiza un inventario/nomina/captador)
        this.setState({
            inputAsignados: nextProps.asignados
        })
    }

    render(){
        return (
            <div>
                <div className={cx('captador-div')}>
                    <div className={cx('captador-text')} >
                        {this.props.nombre}
                    </div>
                    <input className={cx('captador-ammount', {
                             'captador-ammount-dirty': this.state.inputAsignados!=this.props.asignados
                           })}
                           type="number"
                           value={this.state.inputAsignados}
                           onChange={this._onChange}
                           onKeyDown={this._onKeyDown}
                           onBlur={this.cambiarAsignados}
                    />
                    <div className={cx('dots-container')}
                         onClick={this.toggleMenu}
                         ref={ref=>this.nodeTrigger=ref}
                    >
                        <div className={cx('dots')}></div>
                    </div>
                </div>
                <div ref={ref=>this.node=ref}>
                    <ul className={cx('options', {'options-hidden': this.state.optionsVisible==false})}>
                        <li className={cx('option')} onClick={this.agregarCaptador}>
                            Agregar Captador
                        </li>
                        <li className={cx('option-blankspace')} >&nbsp;</li>
                        <li className={cx('option')} onClick={this.props.quitarCaptador}>
                            Quitar Captador
                        </li>
                    </ul>
                </div>
            </div>
        )
    }
}

/* ################################################# NUEVO CAPTADOR ################################################# */
class NuevoCaptador extends React.Component {
    render(){
        return this.props.visible?
            <div className={cx('captador-nuevo')} >
                {/* select con los diferentes captadores */}
                <select className={cx('captador-nuevo-select')} onChange={this.props.captadorSeleccionado}>
                    <option key={0} value={0}>--</option>
                    {this.props.captadoresDisponibles.map(captador=>
                        <option key={captador.valor} value={captador.valor}>{captador.texto}</option>
                    )}
                </select>
                <div className={cx('captador-nuevo-opcion')} onClick={this.props.ocultar_nuevoCaptador}></div>
            </div>
            :
            null
    }
}