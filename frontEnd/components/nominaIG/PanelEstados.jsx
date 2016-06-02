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
                activo={idEstado==2}
                acciones={[
                    {
                        texto: 'Enviar nómina',onclick: this.props.enviarNomina,
                        habilitado: idEstado==2 && this.props.permisos.enviar
                    }
                ]}
            />
            {/* Recibida */}
            <Estado
                titulo="Recibida"
                descripcion="Dotación recibida por SEI"
                activo={idEstado==3}
                acciones={[
                    {
                        texto: 'Aprobar nómina', onclick: this.props.aprobarNomina,
                        habilitado: idEstado==3 && this.props.permisos.aprobar
                    },{
                        texto: 'Rechazar nómina', onclick: this.props.rechazarNomina,
                        habilitado: idEstado==3 && this.props.permisos.aprobar
                    }
                ]}
            />
            {/* Aprobada */}
            <Estado
                titulo="Aprobada"
                descripcion="Dotación aprobada por SEI"
                activo={idEstado==4}
                acciones={[
                    {
                        texto: 'Informar nómina', onclick: this.props.informarNomina,
                        habilitado: idEstado==4 && this.props.permisos.informar
                    }
                ]}
            />
            {/* Informada */}
            <Estado
                titulo="Informada"
                descripcion="El cliente ha sido informado por correo"
                activo={idEstado==5}
                acciones={[
                    {
                        texto: 'Rectificar Nómina', onclick: this.props.rectificarNomina,
                        habilitado: idEstado==5 && this.props.permisos.rectificar
                    }
                ]}
            />
        </div>
    }
}

PanelEstados.propTypes = {
    idEstado: PropTypes.number.isRequired,
    // Se espera que estos metodos retornen una promesa:
    enviarNomina: PropTypes.func.isRequired,
    aprobarNomina: PropTypes.func.isRequired,
    rechazarNomina: PropTypes.func.isRequired,
    informarNomina: PropTypes.func.isRequired,
    rectificarNomina: PropTypes.func.isRequired,
    // Permisos
    permisos: PropTypes.object.isRequired
}
// PanelEstados.defaultProps = {
//     usuario: {}
// }