// Librearias
import React from 'react'
let PropTypes = React.PropTypes
// Modulos
import * as css from './PanelDatos.css'

export class PanelDatos extends React.Component {
    
    render(){
        return <div className="row">
            <div className="col-sm-6">
                {/* Datos generales del inventario */}
                <div className="panel panel-default">
                    <div className={'panel-heading '+css.panelDatos_heading}>Inventario</div>
                    <table className={'table table-compact table-striped '+css.tablaDatos}>
                        <tbody>
                        <tr>
                            <td>Cliente</td><td>{this.props.inventario.local.cliente.nombreCorto}</td>
                            <td>Dotación asignada</td><td>{this.props.nomina.dotacionAsignada}</td>
                        </tr>
                        <tr>
                            <td>Local</td><td>({this.props.inventario.local.numero}) {this.props.inventario.local.nombre}</td>
                            <td></td><td></td>
                        </tr>
                        <tr>
                            <td>Fecha programada</td><td>{this.props.inventario.inventario_fechaProgramada}</td>
                            <td></td><td></td>
                        </tr>
                        <tr>
                            <td>Hr. llegada lider</td><td>{this.props.nomina.horaPresentacionLider}</td>
                            <td></td><td></td>
                        </tr>
                        <tr>
                            <td>Hr. llegada equipo</td><td>{this.props.nomina.horaPresentacionEquipo}</td>
                            <td></td><td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div className="col-sm-6">
                {/* Datos generales del local*/}
                <div className="panel panel-default">
                    <div className={'panel-heading '+css.panelDatos_heading}>Local</div>
                    <table className={'table table-compact '+css.tablaDatos}>
                        <tbody>
                        <tr>
                            <td>Dirección</td><td>{this.props.inventario.local.direccion}</td>
                            <td>Hr.Apertura</td><td>{this.props.inventario.local.horaApertura}</td>
                        </tr>
                        <tr>
                            <td>Comuna</td><td>{this.props.inventario.local.comuna_nombre}</td>
                            <td>Hr.Cierre</td><td>{this.props.inventario.local.horaCierre}</td>
                        </tr>
                        <tr>
                            <td>Región</td><td>{this.props.inventario.local.region_numero}</td>
                            <td>Teléfono 1</td><td>{this.props.inventario.local.telefono1}</td>
                        </tr>
                        <tr>
                            <td>Formato Local</td><td>{this.props.inventario.local.formatoLocal_nombre}</td>
                            <td>Teléfono 2</td><td>{this.props.inventario.local.telefono2}</td>
                        </tr>
                        <tr>
                            <td></td><td></td>
                            <td>Correo</td><td>{this.props.inventario.local.emailContacto}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    }
    
}
PanelDatos.propTypes = {
    inventario: PropTypes.object.isRequired,
    nomina: PropTypes.object.isRequired
}