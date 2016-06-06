// Librerias
import React from 'react'
// Componentes
import * as css from './TablaLocales.css'

class RowLocales extends React.Component{
    render(){
        return <tr>
            <td className={css.id}>
                {this.props.index}</td>
            <td className={css.cliente}>
                {this.props.local.idCliente}</td>
            <td className={css.formatoLocal}>
                {this.props.local.idFormatoLocal}</td>
            <td className={css.jornada}>
                {this.props.local.idJornadaSugerida}</td>
            <td className={css.numero}>
                {this.props.local.numero}</td>
            <td className={css.nombre}>
                {this.props.local.nombre}</td>
            <td className={css.horaApertura}>
                {this.props.local.horaApertura}</td>
            <td className={css.horaCierre}>
                {this.props.local.horaCierre}</td>
            <td className={css.emailContacto}>
                {this.props.local.emailContacto}</td>
            <td className={css.telefono1}>
                {this.props.local.telefono1}</td>
            <td className={css.telefono2}>
                {this.props.local.telefono2}</td>
            <td className={css.stock}>
                {this.props.local.stock}</td>
            <td className={css.fechaStock}>
                {this.props.local.fechaStock}</td>
            <td className={css.comuna}>
                {this.props.local.direccion.cutComuna}</td>
            <td className={css.direccion}>
                {this.props.local.direccion.direccion}</td>
        </tr>
    }
}

RowLocales.propTypes = {
    index: React.PropTypes.number.isRequired,
    local: React.PropTypes.object.isRequired
}
export default RowLocales