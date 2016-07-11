import React from 'react'
let PropTypes = React.PropTypes
// Modulos
import * as css from './NominasCaptador.css'
import classNames from 'classnames/bind'
let cx = classNames.bind(css)

let labelNominas = [
    'label-default',    // 0 - nunca ocurre
    'label-default',    // 1 - Deshabilitada
    'label-default',    // 2 - Pendiente
    'label-success',    // 3 - Recibida (verde)
    'label-info',       // 4 - Aprobada (celeste)
    'label-primary',    // 5 - Informada (azul)
    'label-primary'     // 6 - Informada con Excel (plataforma antigua)
]

export class NominasCaptador extends React.Component {
    componentWillMount(){
        this.props.fetchNominasAsignadas()
    }
    render(){
        return <div className="panel panel-default">
            <div className="panel-heading">
                Proximas Nóminas
            </div>
            <div className="panel-body">
                <table className={cx("table table-bordered table-hover table-condensed", 'tablaNominas')}>
                    <thead>
                    <tr>
                        <th>N°</th>
                        {/* Inventario */}
                        <th>Fecha Programada</th>
                        <th>Cliente</th>
                        <th>CE</th>
                        <th>Local</th>
                        {/* Local */}
                        <th>Región</th>
                        <th>Comuna</th>
                        <th>Dirección</th>
                        <th>Turno</th>
                        {/* Estado */}
                        <th>Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    {this.props.nominas.length===0?
                        <tr><td colSpan="10" className="text-center">Sin nóminas pendientes</td></tr> : null
                    }
                    {this.props.nominas.map((nom, index)=>{
                        return <tr key={index}>
                            <td>{index+1}</td>
                            {/* Inventario */}
                            <td>{nom.inventario.inventario_fechaProgramadaF}</td>
                            <td>{nom.inventario.local.cliente.nombreCorto}</td>
                            <td>{nom.inventario.local.numero}</td>
                            <td>{nom.inventario.local.nombre}</td>
                            {/* Local */}
                            <td>{nom.inventario.local.region_numero}</td>
                            <td>{nom.inventario.local.comuna_nombre}</td>
                            <td>{nom.inventario.local.direccion}</td>
                            <td>{nom.turno}</td>
                            {/* Estado */}
                            <td>
                                <a href={`/programacionIG/nomina/${nom.idNomina}`} target="_blank"
                                   className={"label "+labelNominas[nom.estado.idEstadoNomina]}>
                                    {nom.estado.nombre}
                                </a>
                            </td>
                        </tr>
                    })}
                    </tbody>
                </table>
            </div>
        </div>
    }
}
NominasCaptador.propTypes = {
    // nominas: PropTypes.arrayOf(PropTypes.object).isRequired
    // descripcion: PropTypes.string.isRequired,
    // activo: PropTypes.bool.isRequired,
    // acciones: PropTypes.arrayOf(PropTypes.object).isRequired
}