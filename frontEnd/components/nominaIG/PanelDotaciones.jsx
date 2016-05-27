// Librearias
import React from 'react'
let PropTypes = React.PropTypes
import _ from 'lodash'
// Modulos
import * as css from './PanelDotaciones.css'
import { RowOperador } from './RowOperador.jsx'

export class PanelDotaciones extends React.Component {
    render() {
        return <div className="row">
            {/* Dotacion Titular */}
            <section className="col-sm-5">
                <div className="panel panel-primary">
                    <div className={'panel-heading '+css.panelHeading_compacto}>Personal Asignado</div>
                    <table className={'table table-striped table-bordered table-hover table-condensed '+css.tablaDotacion}>
                        <colgroup>
                            <col className={css.colCorrelativo}/>
                            <col className={css.colUsuarioRUN}/>
                            <col className={css.colUsuarioDV}/>
                            <col className={css.colNombre}/>
                            <col className={css.colCargo}/>
                            <col className={css.colOpciones}/>
                        </colgroup>
                        <thead>
                        <tr>
                            <th>N°</th>
                            <th>RUN</th>
                            <th>DV</th>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th>Opciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        {/* Lider */}
                        <RowOperador
                            editable={this.props.dotacionEditable}
                            correlativo={"L"}
                            operador={ this.props.lider }
                            cargo="Lider"
                            agregarUsuario={this.props.agregarLider}
                            quitarUsuario={this.props.quitarLider}
                        />
                        {/* Supervisor */}
                        <RowOperador
                            editable={this.props.dotacionEditable}
                            correlativo={"S"}
                            operador={ this.props.supervisor }
                            cargo="Supervisor"
                            agregarUsuario={this.props.agregarSupervisor}
                            quitarUsuario={this.props.quitarSupervisor}
                        />
                        {/* Dotación */}
                        {_.range(0, this.props.dotacionAsignada).map(index=>{
                            let operador = this.props.dotacionTitular[index]
                            return <RowOperador
                                editable={this.props.dotacionEditable}
                                key={index}
                                correlativo={""+(index+1)}
                                operador={operador}
                                cargo="Operador"
                                agregarUsuario={this.props.agregarOperadorTitular}
                                quitarUsuario={this.props.quitarOperador}
                            />
                        })}
                        </tbody>
                    </table>
                </div>
            </section>

            {/* Dotacion Reemplazo */}
            <section className="col-sm-5">
                <div className="panel panel-primary">
                    <div className={'panel-heading '+css.panelHeading_compacto}>Personal Reemplazo</div>
                    <table className={'table table-striped table-bordered table-hover table-condensed '+css.tablaDotacion}>
                        <colgroup>
                            <col className={css.colCorrelativo}/>
                            <col className={css.colUsuarioRUN}/>
                            <col className={css.colUsuarioDV}/>
                            <col className={css.colNombre}/>
                            <col className={css.colCargo}/>
                            <col className={css.colOpciones}/>
                        </colgroup>
                        <thead>
                        <tr>
                            <th>N°</th>
                            <th>RUN</th>
                            <th>DV</th>
                            <th>Nombre</th>
                            <th>Cargo</th>
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
                                correlativo={""+(index+1)}
                                operador={ operador }
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
    dotacionAsignada: PropTypes.string.isRequired,
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