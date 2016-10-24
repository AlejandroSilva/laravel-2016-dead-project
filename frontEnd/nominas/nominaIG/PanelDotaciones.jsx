// Librearias
import React from 'react'
let PropTypes = React.PropTypes
import _ from 'lodash'
// Modulos
import * as css from './PanelDotaciones.css'
import { RowOperador } from './RowOperador.jsx'

export class PanelDotaciones extends React.Component {
    render() {
        // el total de operadors no siempre es igual a los operadores asignados (this.props.dotacionOperadores),
        // PUEDE PASAR que esten asignados mas operadores que el limite
        // (por ejemplo: se asignan 10 operadores, y al actualizar el stock la cantidad baja a 7, entonces hay 3 operadores de mas)
        let operadoresAsignados = this.props.dotacionOperadores
        // si esta asignado el supervisor, se agrega un operador menos
        if(this.props.supervisor){
            operadoresAsignados--
        }
        let operadoresReales = this.props.dotacionTitular.length
        let totalOperadores = operadoresAsignados>operadoresReales? operadoresAsignados : operadoresReales

        return <div className="row">
            <section className="col-sm-10">
                {/* Dotacion Titular */}
                <div className="panel panel-primary">
                    <div className={'panel-heading '+css.panelHeading_compacto}>Personal Asignado</div>
                    <table className={'table table-striped table-bordered table-hover table-condensed '+css.tablaDotacion}>
                        <colgroup>
                            <col className={css.colCorrelativo}/>
                            <col className={css.colUsuarioRUN}/>
                            <col className={css.colUsuarioDV}/>
                            <col className={css.colNombre}/>
                            <col className={css.colCargo}/>
                            <col className={css.colExperiencia}/>
                            <col className={css.colOpciones}/>
                        </colgroup>
                        <thead>
                        <tr>
                            <th>N°</th>
                            <th>RUN</th>
                            <th>DV</th>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th>Experiencia</th>
                            <th>Opciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        {/* Lider */}
                        <RowOperador
                            editable={this.props.liderEditable}
                            correlativo={"L"}
                            personal={ this.props.lider }
                            cargo="Lider"
                            agregarUsuario={this.props.agregarLider}
                            quitarUsuario={this.props.quitarLider}
                        />
                        {/* Supervisor */}
                        <RowOperador
                            editable={this.props.supervisorEditable}
                            correlativo={"S"}
                            personal={ this.props.supervisor }
                            cargo="Supervisor"
                            agregarUsuario={this.props.agregarSupervisor}
                            quitarUsuario={this.props.quitarSupervisor}
                        />
                        {/* Dotación */}
                        {_.range(0, totalOperadores).map(index=>{
                            let operador = this.props.dotacionTitular[index]
                            return <RowOperador
                                editable={this.props.dotacionEditable}
                                key={index}
                                correlativo={`${index+1}`}
                                personal={operador}
                                cargo="Operador"
                                agregarUsuario={this.props.agregarOperadorTitular}
                                quitarUsuario={this.props.quitarOperador}
                            />
                        })}
                        </tbody>
                    </table>
                </div>

                {/* Dotacion Reemplazo */}
                <div className="panel panel-primary">
                    <div className={'panel-heading '+css.panelHeading_compacto}>Personal Reemplazo</div>
                    <table className={'table table-striped table-bordered table-hover table-condensed '+css.tablaDotacion}>
                        <colgroup>
                            <col className={css.colCorrelativo}/>
                            <col className={css.colUsuarioRUN}/>
                            <col className={css.colUsuarioDV}/>
                            <col className={css.colNombre}/>
                            <col className={css.colCargo}/>
                            <col className={css.colExperiencia}/>
                            <col className={css.colOpciones}/>
                        </colgroup>
                        <thead>
                        <tr>
                            <th>N°</th>
                            <th>RUN</th>
                            <th>DV</th>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th>Experiencia</th>
                            <th>Opciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        {/* Dotación Reserva, por defecto dejar siempre 3 operadores de reserva */}
                        {_.range(0, 3).map(index=>{
                            let operador = this.props.dotacionReemplazo[index]
                            return <RowOperador
                                editable={this.props.dotacionEditable}
                                key={index}
                                correlativo={`${index+1}`}
                                personal={ operador }
                                cargo="Operador"
                                agregarUsuario={this.props.agregarOperadorReemplazo}
                                quitarUsuario={this.props.quitarOperador}
                            />
                        })}
                        </tbody>
                    </table>
                </div>
            </section>

            {/* Imagen Lider */}
            <section className="col-sm-2">
                <div className="panel panel-primary">
                    <div className={'panel-heading '+css.panelHeading_compacto}>Lider</div>
                    {this.props.lider && this.props.lider.imagenPerfil?
                        <img className={"img-responsive "+css.imgCentrada}
                             src={`/imagenPerfil/${this.props.lider.imagenPerfil}`} alt=""/>
                        :
                        <p>Sin imagen</p>
                    }
                </div>
            </section>
        </div>
    }
}

PanelDotaciones.propTypes = {
    // datos
    dotacionOperadores: PropTypes.number.isRequired,
    liderEditable: PropTypes.bool.isRequired,
    supervisorEditable: PropTypes.bool.isRequired,
    dotacionEditable: PropTypes.bool.isRequired,
    // dotacion
    lider: PropTypes.object,  // opcional
    supervisor: PropTypes.object,  // opcional
    dotacionTitular: PropTypes.arrayOf(PropTypes.object).isRequired,
    dotacionReemplazo: PropTypes.arrayOf(PropTypes.object).isRequired,
    // metodos
    agregarOperadorTitular: PropTypes.func.isRequired,
    agregarOperadorReemplazo: PropTypes.func.isRequired,
    quitarOperador: PropTypes.func.isRequired,
    agregarLider: PropTypes.func.isRequired,
    quitarLider: PropTypes.func.isRequired,
    agregarSupervisor: PropTypes.func.isRequired,
    quitarSupervisor: PropTypes.func.isRequired
}