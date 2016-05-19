// Librearias
import React from 'react'
let PropTypes = React.PropTypes
// Modules
import { Estado } from './Estado.jsx'

export class PanelEstados extends React.Component {
    render(){
        let idEstado = this.props.idEstado

        return  <div className="row">
            {/* Pendiente */}
            <Estado
                titulo="Pendiente"
                descripcion="La nómina se encuentra pendiente"
                activo={idEstado==1}
                acciones={[
                    {texto: 'Enviar nómina', onclick: this.props.enviarNomina, habilitado: idEstado==1}
                ]}
            />
            {/* Recibida */}
            <Estado
                titulo="Recibida"
                descripcion="Dotación recibida por SEI"
                activo={idEstado==2}
                acciones={[
                    {texto: 'Aprobar nómina', onclick: this.props.aprobarNomina, habilitado: idEstado==2},
                    {texto: 'Rechazar nómina', onclick: this.props.rechazarNomina, habilitado: idEstado==2}
                ]}
            />
            {/* Aprobada */}
            <Estado
                titulo="Aprobada"
                descripcion="Dotación aprobada por SEI"
                activo={idEstado==3}
                acciones={[
                    {texto: 'Informar nómina', onclick: this.props.informarNomina, habilitado: idEstado==3}
                ]}
            />
            {/* Informada */}
            <Estado
                titulo="Informada"
                descripcion="El cliente ha sido informado por correo"
                activo={idEstado==4}
                acciones={[
                    {texto: 'Rectificar Nómina', onclick: this.props.rectificarNomina, habilitado: idEstado==4}
                ]}
            />
        </div>
    }
}

PanelEstados.propTypes = {
    idEstado: PropTypes.number.isRequired,
    enviarNomina: PropTypes.func.isRequired,
    aprobarNomina: PropTypes.func.isRequired,
    rechazarNomina: PropTypes.func.isRequired,
    informarNomina: PropTypes.func.isRequired,
    rectificarNomina: PropTypes.func.isRequired
}
// PanelEstados.defaultProps = {
//     usuario: {}
// }