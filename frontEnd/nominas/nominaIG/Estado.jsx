import React from 'react'
let PropTypes = React.PropTypes
// Modulos
import * as css from './Estado.css'
import classNames from 'classnames/bind'
let cx = classNames.bind(css)

export class Estado extends React.Component {
    constructor(props){
        super(props)
        this.state = {
            bloqueado: false
        }
    }
    onClick(metodo){
        // mientras no se reciba una respuesta del servidor, el control debe estar bloqueado
        this.setState({bloqueado: true})
        metodo()
            .then(resp=>{
                this.setState({bloqueado: false})
            })
            .catch(err=>{
                this.setState({bloqueado: false})
            })
    }
    render(){
        return <div className={cx('col-md-3', 'step')}>
            <div className={cx('panel', this.props.activo? 'panel-primary' : 'panel-default')}>
                <div className="panel-heading">
                    <h4>{this.props.titulo}</h4>
                    <p>{this.props.descripcion}</p>
                </div>
                {this.props.activo?
                    <div className="panel-body">
                        {this.props.acciones.map((accion, index)=>
                            <button className="btn btn-block btn-sm btn-primary "
                                    key={index}
                                    onClick={this.onClick.bind(this, accion.onclick)}
                                    // el boton esta deshabilitado si: la accion esta deshabilitada, o se
                                    // esta esperando una respuesta del servidor
                                    disabled={!accion.habilitado || this.state.bloqueado}>
                                {accion.texto}
                            </button>
                        )}
                    </div>
                    :
                    null
                }
            </div>
        </div>
    }
}

Estado.propTypes = {
    titulo: PropTypes.string.isRequired,
    descripcion: PropTypes.string.isRequired,
    activo: PropTypes.bool.isRequired,
    acciones: PropTypes.arrayOf(PropTypes.object).isRequired
}   