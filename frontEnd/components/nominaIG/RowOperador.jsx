// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import * as css from './nominaIG.css'
import { InputRun } from './InputRun.jsx'
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
                        Cargo
                    </td>
                    <td>
                        <button className="btn btn-xs btn-warning"
                                onClick={()=>{ this.props.quitarUsuario(this.props.operador.usuarioRUN) }}
                        >Quitar</button>
                    </td>
                </tr>
            )
        }else{
            // si no esta definido, mostrar el formulario para agregarlo
            return (
                <tr key={2}>
                    <td>{this.props.correlativo}</td>
                    <td className={css.tdUsuarioRUN}>
                        <InputRun
                            onPressEnter={this.props.agregarUsuario}
                            onRUNChange={this.onRUNChange.bind(this)}
                        />
                    </td>
                    <td className={css.tdUsuarioDV}>
                        <input type="text" value={''} disabled ref={ref=>this.ref_usuarioDV=ref}/>
                    </td>
                    <td className={css.tdNombre}>
                        <input type="text" value={''} disabled/>
                    </td>
                    <td>
                        Cargo
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
    correlativo: PropTypes.number.isRequired,
    operador: PropTypes.object,
    agregarUsuario: PropTypes.func.isRequired,
    quitarUsuario: PropTypes.func.isRequired
    // comunas: PropTypes.arrayOf(PropTypes.object).isRequired
}
RowOperador.defaultProps = {
    // usuario: {}
}