// Librerias
import React from 'react'
import api from '../../apiClient/v1'
import moment from 'moment'

// Componentes
import { TablaAuditoriasPendientes }from './TablaAuditoriasPendientes.jsx'
import { EstadoZonas } from './EstadoZonas.jsx'

export class ProgramacionAIPendientes extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            datos: []
        }
    }
    componentWillMount(){
        api.auditoria.estadoGeneral(2, moment().format('YYYY-MM-DD'))
            .then(datos=>{
                this.setState({
                    datos
                })
            })
    }
    render(){
        let datos = this.state.datos[0]? this.state.datos[0].informado : {}
        let mensaje = datos.diasHabilesMes? `(${datos.diasHabilesMes} días hábiles, ${datos.diasHabilesRestantes} restantes)` : ''
        return <div>
            <h1>
                Estado general de auditorías para el mes de <b>{moment().format('MMMM')}</b>
                <small>  {mensaje}</small>
            </h1>
            {/*<TablaAuditoriasPendientes />*/}
            <EstadoZonas
                datos={this.state.datos}
            />
        </div>
    }
}