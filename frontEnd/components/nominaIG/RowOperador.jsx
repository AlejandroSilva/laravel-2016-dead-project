// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
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
            return <tr key={1}>
                <th>{this.props.correlativo}</th>
                <th>
                    <input type="text" value={operador.usuarioRUN} disabled/>
                </th>
                <th>
                    <input type="text" value={operador.usuarioDV} disabled/>
                </th>
                <th>
                    <input type="text" value={operador.nombre} disabled/>
                </th>
                <th>
                    Cargo
                </th>
                <th>
                    <button className="btn btn-sm btn-warning"
                            onClick={()=>{ this.props.quitarUsuario(this.props.operador.usuarioRUN) }}
                    >Quitar</button>
                </th>
            </tr>
        }else{
            // si no esta definido, mostrar el formulario para agregarlo
            return (
                <tr key={2}>
                    <th>{this.props.correlativo}</th>
                    <th>
                        <InputRun
                            onPressEnter={this.props.agregarUsuario}
                            onRUNChange={this.onRUNChange.bind(this)}
                        />
                    </th>
                    <th>
                        <input type="text" value={''} disabled ref={ref=>this.ref_usuarioDV=ref}/>
                    </th>
                    <th>
                        <input type="text" value={''} disabled/>
                    </th>
                    <th>
                        Cargo
                    </th>
                    <th>
                        {/*<a href="#" className="btn btn-small">Quitar</a>*/}
                    </th>
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