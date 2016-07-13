// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import * as css from './PanelDotaciones.css'
import { InputRun } from '../../shared/input/InputRun.jsx'
// Validador Rut

export class RowOperador extends React.Component {
    onRUNChange(usuarioRUN, usuarioDV){
        // a medida que escriben el rut, se ira actualizando el DV
        this.ref_usuarioDV.value = usuarioDV
    }
    render(){
        var operador = this.props.operador

        if(operador){
            // si el operador esta definido, mostrar sus datos
            return (
                <tr key={1}>
                    <td>{this.props.correlativo}</td>
                    <td className={css.tdUsuarioRUN}>
                        <input type="text" value={operador.usuarioRUN} disabled/>
                    </td>
                    <td className={css.tdUsuarioDV}>
                        <input type="text" value={operador.usuarioDV} disabled/>
                    </td>
                    <td className={css.tdNombre}>
                        <input type="text" value={operador.nombre} disabled/>
                    </td>
                    <td>
                        {this.props.cargo}
                    </td>
                    <td>
                        {this.props.editable ?
                            <button className="btn btn-xs btn-warning"
                                    onClick={()=>{ this.props.quitarUsuario(this.props.operador.usuarioRUN) }}
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
                                onPressEnter={this.props.agregarUsuario}
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
    operador: PropTypes.object,
    cargo: PropTypes.string,
    agregarUsuario: PropTypes.func.isRequired,
    quitarUsuario: PropTypes.func.isRequired
    // comunas: PropTypes.arrayOf(PropTypes.object).isRequired
}
RowOperador.defaultProps = {
    // usuario: {}
}