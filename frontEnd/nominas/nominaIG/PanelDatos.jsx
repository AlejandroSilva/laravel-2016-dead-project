// Librearias
import React from 'react'
let PropTypes = React.PropTypes
// Modulos
import * as css from './PanelDatos.css'

export class PanelDatos extends React.Component {
    
    render(){
        return <div className="row">
            <div className="col-sm-3">
                {/* Datos generales del inventario */}
                <div className="panel panel-default">
                    <div className={'panel-heading '+css.panelDatos_heading}>Inventario</div>
                    <table className={'table table-compact table-striped '+css.tablaDatos}>
                        <tbody>
                            <tr>
                                <td>Cliente</td><td>{this.props.nomina.cliente_nombreCorto}</td>
                            </tr>
                            <tr>
                                <td>Local</td><td>({this.props.nomina.local_numero}) {this.props.nomina.local_nombre}</td>
                            </tr>
                            <tr>
                                <td>Fecha programada</td><td>{this.props.nomina.inv_fechaProgramadaF}</td>
                            </tr>
                            <tr>
                                <td>Hr. llegada lider</td><td>{this.props.nomina.horaPresentacionLiderF}</td>
                            </tr>
                            <tr>
                                <td>Hr. llegada equipo</td><td>{this.props.nomina.horaPresentacionEquipoF}</td>
                            </tr>
                            <tr>
                                <td>Dotación Operadores</td><td>{this.props.nomina.dotacionOperadores}</td>
                            </tr>
                            <tr>
                                <td>Dotación Total</td><td>{this.props.nomina.dotacionTotal}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div className="col-sm-4">
                {/* Datos generales del local*/}
                <div className="panel panel-default">
                    <div className={'panel-heading '+css.panelDatos_heading}>Local</div>
                    <table className={'table table-compact '+css.tablaDatos}>
                        <tbody>
                            <tr>
                                <td>Dirección</td>
                                <td>{this.props.nomina.local_direccion}</td>
                            </tr>
                            <tr>
                                <td>Comuna</td>
                                <td>{this.props.nomina.local_comuna}</td>
                            </tr>
                            <tr>
                                <td>Región</td>
                                <td>{this.props.nomina.local_region}</td>
                            </tr>
                            <tr>
                                <td>Formato</td>
                                <td>{this.props.nomina.local_formato}</td>
                            </tr>
                            <tr>
                                <td>Horario</td>
                                <td>Apertura: {this.props.nomina.local_horaAperturaF}  Cierre: {this.props.nomina.local_horaCierreF}</td>
                            </tr>
                            <tr>
                                <td>Teléfonos</td><td>{this.props.nomina.local_telefono1} {this.props.nomina.local_telefono2}</td>
                            </tr>
                            <tr>
                                <td>Correo</td><td>{this.props.nomina.local_emailContacto}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div className="col-sm-2">
                {/* Datos generales del Nomina*/}
                <div className="panel panel-default">
                    <div className={'panel-heading '+css.panelDatos_heading}>Nómina</div>
                    <table className={'table table-compact '+css.tablaDatos}>
                        <tbody>
                            <tr>
                                <td>Turno</td>
                                <td>{this.props.nomina.turno}</td>
                            </tr>
                            <tr>
                                <td>Excel</td>
                                <td><a href={`/programacionIG/nomina/${this.props.nomina.idNominaPublica}/excel`} className="btn btn-xs">Descargar</a></td>
                            </tr>
                            <tr>
                                <td>PDF</td>
                                <td><a href={`/programacionIG/nomina/${this.props.nomina.idNominaPublica}/pdf`} className="btn btn-xs">Descargar</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <section className="col-sm-2">
                <div className="panel panel-primary">
                    <div className={'panel-heading '+css.panelDatos_heading}>Lider</div>
                    {this.props.nomina.lider && this.props.nomina.lider.imagenPerfil?
                        <img className={"img-responsive center-block"}
                             src={`/imagenPerfil/${this.props.nomina.lider.imagenPerfil}`} alt=""/>
                        :
                        <p>Sin imagen</p>
                    }
                </div>
            </section>
        </div>
    }
    
}
PanelDatos.propTypes = {
    nomina: PropTypes.object.isRequired
}