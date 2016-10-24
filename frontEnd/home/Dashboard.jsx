// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// API
import api from '../apiClient/v1'
// Componentes
import { NominasCaptador } from './nominasCaptador/NominasCaptador.jsx'

export class Dashboard extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            nominasAsignadas: []
        }
        this.fetchNominasAsignadas = ()=>{
            // buscar las nominas asignadas al captador, solo cuando se monta el componente
            return api.usuario(this.props.usuario.id).nominasAsignadas(this.props.fechaHoy)
                .then(nominas=>{
                    this.setState({nominasAsignadas: nominas})
                })
        }
    }
    render(){
        return (
            <div>
                {/* Los captadores (role=3), tendran un listado de todos los inventarios que tienen asignados */}
                {this.props.usuario.roles.indexOf(3)>=0?
                    <NominasCaptador
                       fetchNominasAsignadas={this.fetchNominasAsignadas}
                       nominas={this.state.nominasAsignadas}
                    />
                    :
                    null
                }
            </div>
        )
    }
}

Dashboard.propTypes = {
    usuario: PropTypes.object.isRequired,
}