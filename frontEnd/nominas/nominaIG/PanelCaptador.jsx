// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import { RowOperador } from './RowOperador.jsx'
import * as css from './PanelCaptador.css'

export class PanelCaptadorSEI extends React.Component{
    render(){
        let totalAgregados = this.props.captadorSEI.dotacionTitular.length
        let rowsTitulares  = this.props.maximoOperadores>totalAgregados? this.props.maximoOperadores : totalAgregados

        let totalReemplazos = this.props.captadorSEI.dotacionReemplazo.length
        let rowsReemplazo = totalReemplazos<3? 3: totalReemplazos+1

        return <div className="row">
            <h2>
                {this.props.captadorSEI.nombre}
                <small> {totalAgregados} de {this.props.maximoOperadores} operadores agregados</small>
            </h2>

            {/* Dotacion titular */}
            <div className="col-md-6">
                <PanelUsuarios titulo="Dotación Títular">
                    {/* Lider */}
                    <RowOperador
                        editable={this.props.editarLiderSupervisor}
                        correlativo={"L"}
                        personal={ this.props.lider }
                        cargo="Lider"
                        agregarUsuario={this.context.agregarLider}
                        quitarUsuario={this.context.quitarLider}
                    />
                    {/* Supervisor */}
                    <RowOperador
                        editable={this.props.editarLiderSupervisor}
                        correlativo={"S"}
                        personal={ this.props.supervisor }
                        cargo="Supervisor"
                        agregarUsuario={this.context.agregarSupervisor}
                        quitarUsuario={this.context.quitarSupervisor}
                    />
                    {_.range(0, rowsTitulares).map(index=>{
                        let operador = this.props.captadorSEI.dotacionTitular[index]
                        return <RowOperador
                            editable={this.props.editarOperadores}
                            key={operador? operador.id : 't'+index}
                            correlativo={''+(index+1)}
                            personal={operador}
                            cargo="Operador"
                            agregarUsuario={this.context.agregarOperador.bind(this, true, this.props.captadorSEI.idCaptador)}
                            quitarUsuario={this.context.quitarOperador}
                        />
                    })}
                </PanelUsuarios>
            </div>

            {/* Dotacion Reemplazo */}
            <div className="col-md-6">
                {/* Dotacion reemplazo */}
                <PanelUsuarios titulo="Dotación Reemplazo">
                    {_.range(0, rowsReemplazo).map(index=>{
                        let operador = this.props.captadorSEI.dotacionReemplazo[index]
                        return <RowOperador
                            editable={this.props.editarOperadores}
                            key={operador? operador.id : 'r'+index}
                            correlativo={'R'}
                            personal={operador}
                            cargo="Operador"
                            agregarUsuario={this.context.agregarOperador.bind(this, false, this.props.captadorSEI.idCaptador)}
                            quitarUsuario={this.context.quitarOperador}
                        />
                    })}
                </PanelUsuarios>
            </div>
        </div>
    }
}
PanelCaptadorSEI.contextTypes = {
    agregarLider: PropTypes.func,
    quitarLider: PropTypes.func,
    agregarSupervisor: PropTypes.func,
    quitarSupervisor: PropTypes.func,
    agregarOperador: PropTypes.func,
    quitarOperador: PropTypes.func
}


export class PanelCaptador extends React.Component {
    render(){
        let operadoresAsignados = this.props.captador.operadoresAsignados
        let totalAgregados = this.props.captador.dotacionTitular.length
        let rowsTitulares  = operadoresAsignados>totalAgregados? operadoresAsignados : totalAgregados

        let totalReemplazos = this.props.captador.dotacionReemplazo.length
        let rowsReemplazo = totalReemplazos<3? 3: totalReemplazos+1

        return (
            <div className="row">
                <h2>
                    {this.props.captador.nombre}
                    <small> {totalAgregados} de {operadoresAsignados} operadores agregados</small>
                </h2>

                {/* Dotacion titular */}
                <div className="col-md-6">
                    <PanelUsuarios titulo="Dotación Títular">
                        {_.range(0, rowsTitulares).map(index=>{
                            let operador = this.props.captador.dotacionTitular[index]
                            return <RowOperador
                                editable={this.props.editarOperadores}
                                key={operador? operador.id : 't'+index}
                                correlativo={''+(index+1)}
                                personal={operador}
                                cargo="Operador"
                                agregarUsuario={this.context.agregarOperador.bind(this, true, this.props.captador.idCaptador)}
                                quitarUsuario={this.context.quitarOperador}
                            />
                        })}
                    </PanelUsuarios>
                </div>

                {/* Dotacion reemplazo */}
                <div className="col-md-6">
                    <PanelUsuarios titulo="Dotación Reemplazo">
                        {_.range(0, rowsReemplazo).map(index=>{
                            let operador = this.props.captador.dotacionReemplazo[index]
                            return <RowOperador
                                editable={this.props.editarOperadores}
                                key={operador? operador.id : 'r'+index}
                                correlativo={'R'}
                                personal={operador}
                                cargo="Operador"
                                agregarUsuario={this.context.agregarOperador.bind(this, false, this.props.captador.idCaptador)}
                                quitarUsuario={this.context.quitarOperador}
                            />
                        })}
                    </PanelUsuarios>
                </div>

            </div>
        )
    }
}
PanelCaptador.contextTypes = {
    agregarOperador: PropTypes.func,
    quitarOperador: PropTypes.func
}


let PanelUsuarios = ({titulo, children})=>
    <div className="panel panel-primary">
        <div className={'panel-heading '+css.panelHeading_compacto}>{titulo}</div>
        <table className={'table table-striped table-bordered table-hover table-condensed '+css.tablaDotacion}>
            <colgroup>
                <col className={css.colCorrelativo}/>
                <col className={css.colUsuarioRUN}/>
                <col className={css.colUsuarioDV}/>
                <col className={css.colNombre}/>
                <col className={css.colCargo}/>
                <col className={css.colCaptador}/>
                <col className={css.colExperiencia}/>
                <col className={css.colOpciones}/>
            </colgroup>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>RUN</th>
                    <th className={css.thUsuarioDV}>DV</th>
                    <th>Nombre</th>
                    <th>Cargo</th>
                    <th>Captador</th>
                    <th>Experiencia <small>Lider/Superv./Opera.</small></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                {children}
            </tbody>
        </table>
    </div>
