// Libs
import React from 'react'
import moment from 'moment'
// moment.locale('es')
import api from '../../apiClient/v1'
import BlackBoxLocales from './BlackBoxLocales'

// Component
import TablaLocales from './TablaLocales.jsx'
import AgregarLocal from './AgregarLocal.jsx'

class MantenedorLocales extends React.Component{
    constructor(props) {
        super(props)
        this.blackbox = new BlackBoxLocales()
        this.state = {
            idCliente: 0,
            localesFiltrados: [] 
        }
    }
    
    seleccionarCliente(idCliente){
        // todo buscar locales del cliente
        this.blackbox.reset()
        this.setState({
            idCliente: idCliente
        })
        api.cliente.getLocales(idCliente)
            .then(locales=>{
                console.log(locales)
                locales.forEach(local=>{
                    this.blackbox.add(local)
                })
                this.setState({
                    localesFiltrados: this.blackbox.getListaFiltrada()
                })
            })
            //.catch(console.error)
    }

    actualizarLocal(){
        console.error('pendiente')
    }
    eliminarLocal(){
        console.error('pendiente')
    }

    render(){
        return (
            <div>
                <h1>Mantenedor de Locales</h1>

                <AgregarLocal
                    seleccionarCliente={this.seleccionarCliente.bind(this)}
                    clientes={this.props.clientes}
                    jornadas={this.props.jornadas}
                    formatoLocales={this.props.formatoLocales}
                />

                <div className="row">
                    <h4 className="page-header" style={{marginTop: '1em'}}>
                        {/*<a className="btn btn-success btn-xs pull-right"
                         href={`/locales/pdf/${this.state.idCliente}`}
                         >Exportar</a>*/}
                    </h4>
                    <TablaLocales
                        localesFiltrados={this.state.localesFiltrados}
                        actualizarLocal={this.actualizarLocal.bind(this)}
                        eliminarLocal={this.eliminarLocal.bind(this)}
                    />
                </div>
            </div>
        )
    }
}

MantenedorLocales.propTypes = {
    clientes: React.PropTypes.array.isRequired,
    jornadas: React.PropTypes.array.isRequired,
    formatoLocales: React.PropTypes.array.isRequired
}

export default MantenedorLocales