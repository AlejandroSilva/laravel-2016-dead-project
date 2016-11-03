// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import * as css from './PanelCaptador.css'
import { InputRun } from '../../shared/input/InputRun.jsx'
// Validador Rut

export class RowOperador extends React.Component {
    onPressEnter(run){
        this.ref_usuarioDV.value = ''
        this.props.agregarUsuario(run)
    }
    onRUNChange(usuarioRUN, usuarioDV){
        // a medida que escriben el rut, se ira actualizando el DV
        this.ref_usuarioDV.value = usuarioDV
    }
    render(){
        if(this.props.personal){
            // si el operador esta definido, mostrar sus datos
            return (
                <tr key={1}>
                    <td>{this.props.correlativo}</td>
                    <td className={css.tdUsuarioRUN}>
                        <input type="text" value={this.props.personal.usuarioRUN} disabled/>
                    </td>
                    <td className={css.tdUsuarioDV}>
                        <input type="text" value={this.props.personal.usuarioDV} disabled/>
                    </td>
                    <td className={css.tdNombre}>
                        <input type="text" value={this.props.personal.nombreCompleto} disabled/>
                    </td>
                    <td>
                        {this.props.cargo}
                    </td>
                    <td>
                        {this.props.personal.captador}
                    </td>
                    <td>
                        {`Lid:${this.props.personal.experienciaComoLider} Sup:${this.props.personal.experienciaComoSupervisor} Ope:${this.props.personal.experienciaComoOperador}`}
                    </td>
                    <td>
                        {this.props.editable ?
                            <button className="btn btn-xs btn-warning"
                                    onClick={()=>{ this.props.quitarUsuario(this.props.personal.id) }}
                            >Quitar</button>
                            :
                            null
                        }
                    </td>
                </tr>
            )
        }else{
            // si no esta definido, mostrar el formulario para agregarlo
            return (
                <tr key={2}>
                    <td>{this.props.correlativo}</td>
                    <td className={css.tdUsuarioRUN}>
                        {this.props.editable ?
                            <InputRun key={1}
                                onPressEnter={this.onPressEnter.bind(this)}
                                onRUNChange={this.onRUNChange.bind(this)}
                            />
                            :
                            <input type="text" key={2} disabled/>
                        }
                    </td>
                    <td className={css.tdUsuarioDV}>
                        <input type="text" value={''} disabled ref={ref=>this.ref_usuarioDV=ref}/>
                    </td>
                    <td className={css.tdNombre}>
                        <input type="text" value={''} disabled/>
                    </td>
                    <td>
                        {this.props.cargo}
                    </td>
                    <td>
                        {/* Captador */}
                    </td>
                    <td>
                        {/* experiencia */}
                    </td>
                    <td>
                        {/*<a href="#" className="btn btn-small">Quitar</a>*/}
                    </td>
                </tr>
            )
        }
    }
}

RowOperador.propTypes = {
    editable: PropTypes.bool.isRequired,
    correlativo: PropTypes.string.isRequired,
    personal: PropTypes.object,
    cargo: PropTypes.string,
    agregarUsuario: PropTypes.func.isRequired,
    quitarUsuario: PropTypes.func.isRequired
}