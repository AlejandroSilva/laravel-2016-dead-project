 import React from 'react'
import * as css from './EstadoZonas.css'

export class EstadoZonas extends React.Component{
    getColorDiasParaTerminar(datosZona){
        let dif = datosZona.informado.diasParaTerminar_real - datosZona.informado.diasParaTerminar_esperado
        // 5 dias antes
        if(dif<-5)
            return css.celdaExcelente
        // 3 dias antes
        if(dif<-3)
            return css.celdaBien
        // 5 dias despues
        if(dif>=5)
            return css.celdaCritico
        // 2 dias despues
        if(dif>=1)
            return css.celdaMal
        return ' '
    }


    render() {
        return (
            <table className={'table table-bordered table-condensed '+css.tablaEstadoZonas}>
                <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th colSpan="2">Realizadas</th>
                    <th colSpan="2">Pendientes</th>
                    <th colSpan="2">Auditorias por día</th>
                    <th colSpan="2">Porcentaje Realizado</th>
                    <th colSpan="2">Dias para terminar</th>
                </tr>
                    <tr>
                        <th>Zona</th>
                        <th>Total Mes</th>
                        {/* Realizadas */}
                        <th>Optimo</th>
                        <th>Real</th>
                        {/* Pendientes */}
                        <th>Optimo</th>
                        <th>Real</th>
                        {/* Auditorias por día */}
                        <th>Optimo</th>
                        <th>Real</th>
                        {/* Porcentaje */}
                        <th>Optimo</th>
                        <th>Real</th>
                        {/* Porcentaje */}
                        <th>Optimo</th>
                        <th>Real</th>
                    </tr>
                </thead>
                <tbody>
                    {this.props.datos.length===0?
                        <tr><td>Sin datos en este periodo</td></tr>
                        :
                        (this.props.datos.map((datos, index)=>{
                            let difRealizadas = datos.informado.realizadas - datos.informado.realizadasALaFecha_esperado
                            let difDias = Math.round(datos.informado.diasParaTerminar_real - datos.informado.diasParaTerminar_esperado)
                            let difPorcentaje = datos.informado.porcentajeCumplimiento_real - datos.informado.porcentajeCumplimiento_esperado
                            return <tr key={index}>
                                <td><b>{datos.zona.nombre}</b></td>
                                <td>{datos.informado.totalMes}</td>
                                {/* Realizadas */}
                                <td>{datos.informado.realizadasALaFecha_esperado}</td>
                                <td>
                                    {datos.informado.realizadas}
                                    {difPorcentaje<-25 ?
                                        <span className="label label-danger pull-right">{difRealizadas}</span> : null}
                                    {(difPorcentaje<0 && difPorcentaje>=-25)?
                                        <span className="label label-warning pull-right">{difRealizadas}</span> : null}
                                    {(difPorcentaje>1)?
                                        <span className="label label-success pull-right">+{difRealizadas}</span> : null}
                                </td>
                                {/* Pendientes */}
                                <td>{datos.informado.pendientesALaFecha_esperado}</td>
                                <td>{datos.informado.pendientes}</td>
                                {/* Auditorias por día */}
                                <td>{datos.informado.auditoriasPorDia_esperado}</td>
                                <td>{datos.informado.auditoriasPorDia_real}</td>
                                {/* Porcentaje */}
                                <td>{datos.informado.porcentajeCumplimiento_esperado}%</td>
                                <td>
                                    {datos.informado.porcentajeCumplimiento_real}%
                                    {difPorcentaje<-25 ?
                                        <span className="label label-danger pull-right">{difPorcentaje}%</span> : null}
                                    {(difPorcentaje<0 && difPorcentaje>=-25)?
                                        <span className="label label-warning pull-right">{difPorcentaje}%</span> : null}
                                    {(difPorcentaje>=0)?
                                        <span className="label label-success pull-right">+{difPorcentaje}%</span> : null}
                                </td>
                                {/* Estimación de dias */}
                                <td>{datos.informado.diasParaTerminar_esperado}</td>
                                <td /*className={ this.getColorDiasParaTerminar(datos) }*/>
                                    {datos.informado.diasParaTerminar_real+'  '}
                                    {difDias>=5 ?
                                        <span className="label label-danger pull-right">{difDias} días después</span> : null}
                                    {(difDias>=1 && difDias<5)?
                                        <span className="label label-warning pull-right">{difDias} días después</span> : null}
                                    {(difDias<1)?
                                        <span className="label label-success pull-right">{-difDias} días antes</span> : null}
                                </td>
                            </tr>
                        })
                        )
                    }
                </tbody>
            </table>
        )
    }
}

EstadoZonas.propTypes = {
    datos: React.PropTypes.array.isRequired
}