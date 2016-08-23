// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
// Estilos
import * as css from './SelectLider.css'
import classNames from 'classnames/bind'
let cx = classNames.bind(css)

export class SelectLider extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            open: false,
            options: [],
            complete: false,
            hoverIndex: null,
            selectedLabel: '',
            selectedValue: null,
            dirty: false
        }
        // Metodos del menu de opciones
        this.__onClickHandler__ = (evt)=>{
            // detecta cuando se hace un click fuera de este componente, cuando eso pasa y el menu es visible, lo oculta
            if(!this.node.contains(evt.target) && this.state.open)
                this._hideOptionsList()
                // this.cerrarMenu_descartarCambios()
        }
        this._toggleOptionsList = ()=>{
            if(this.state.open)
                this._hideOptionsList()
            else
                this._openOptionsList()
        }
        this._hideOptionsList = ()=>{
            this.setState({open: false})
        }
        this._openOptionsList = ()=>{
            this.setState({open: true})
            // retornamos un callback para que puedan cambiar el state
            this.props.onOpenList(({options, complete})=>{
                this.setState({options, complete})
            })
        }

        // Interacciones
        this._selectOption = (enabled, value)=>{
            // si la opcion esta deshabilitada, no hacer nada con este evento
            if(enabled==false)
                return;
            // si es la misma opcion que esta actualmente seleccionada, no hacer nada..
            if(this.state.selectedValue==value){
                this.setState({open: false})
                return;
            }

            let option = this.state.options.find(opt=> opt.value==value)
            this.setState({
                // seleccionar el 'value', modificar el 'label'
                selectedLabel: option? option.label : '-',
                selectedValue: value,
                // marcar como dirty solo si se selecciono una opcion diferente a la actualmente seleccionada
                dirty: this.selectedValue !== value,
                // ocultar la lista
                open: false
            }, ()=>{
                // informar al componente padre del cambio, solo una vez que se actualizo el state
                this.props.onChange(value)
            })
        }
        this.onMouseEnterOption = (hoverIndex)=>{
            this.setState({hoverIndex})
        }
        this.onMouseLeaveOption = ()=>{
            // puede haber problemas de "racing condition" con onMouseEnterOption
            this.setState({hoverIndex: null})
        }
    }
    // cargar los elementos por defecto
    componentWillMount(){
        this.setState({
            selectedValue: this.props.selectedValue,
            selectedLabel: this.props.selectedLabel,
            dirty: false
        })
    }
    // actualizar los items al recibir nuevas propiedades
    componentWillReceiveProps(nextProps){
        let selectedValue_old = this.props.selectedValue
        let selectedValue_new = nextProps.selectedValue
        let option = this.state.options.find(opt=> opt.value==selectedValue_new)
        if(selectedValue_old!==selectedValue_new){
            // si esto cambia, entonces se debe hacer un "reset" del estado con los nuevos valores
            this.setState({
                selectedLabel: option? option.label : '-',
                selectedValue: selectedValue_new,
                dirty: false
            })
        }
        if(selectedValue_new!==this.state.selectedValue){
            // si se reciben propiedades distintas al "state" actual, se reemplazan por las propiedades
            this.setState({
                selectedLabel: option? option.label : '-',
                selectedValue: selectedValue_new,
                dirty: false
            })
        }
    }
    // Metodos para controlar el display del menu, ocultar y mostrar los elementos
    componentDidMount() {
        document.addEventListener('click', this.__onClickHandler__)
    }
    componentWillUnmount() {
        document.removeEventListener('click', this.__onClickHandler__)
    }

    getEstado(){
        return {
            seleccionUsuario: this.state.selectedValue,
            dirty: this.state.dirty
        }
    }
    render(){
        return (
            <div className={cx('select-lider', {'hidden': this.props.visible==false})} ref={ref=>this.node=ref}>
                <div className={cx('select', {'select-dirty': this.state.dirty})}  onClick={this._toggleOptionsList}>
                    <div className={cx('select-text')} >
                        {this.state.selectedLabel}
                    </div>
                    <div className={cx('select-icon', {
                        'arrow-up': !this.state.open,
                        'arrow-down': this.state.open
                    })}>
                    </div>
                </div>
                <ul className={cx('options', {'options-hidden': !this.state.open})}>
                    {this.state.complete?
                        this.state.options.map((opt, index)=>
                            <li className={cx('option', 'no-select-text', {
                                'option-hover': this.state.hoverIndex==index,
                                'option-selected': this.state.selectedValue==opt.value,
                                'option-disabled': opt.enabled==false
                            })}
                                key={index}
                                onMouseEnter={this.onMouseEnterOption.bind(this, index)}
                                onMouseLeave={this.onMouseLeaveOption}
                                onClick={this._selectOption.bind(this, opt.enabled, opt.value)}
                            >
                                {opt.label}
                            </li>
                        )
                        :
                        <li className={cx('option-loading', 'no-select-text')} > cargando ...</li>
                    }
                </ul>
            </div>
        )
    }
}

SelectLider.propTypes = {
    visible: PropTypes.bool.isRequired,
    onOpenList: PropTypes.func.isRequired,
    onChange: PropTypes.func.isRequired,
    selectedLabel: PropTypes.string.isRequired,
    selectedValue: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}