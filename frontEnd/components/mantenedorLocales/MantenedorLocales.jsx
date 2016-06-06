// Libs
import React from 'react'
import api from '../../apiClient/v1'
import BlackBoxLocales from './BlackBoxLocales'

// Component
import TablaLocales from './TablaLocales.jsx'
import AgregarLocal from './AgregarLocal.jsx'
import { RowNuevoLocal } from './RowNuevoLocal.jsx'
// ReactNotify
import ReactNotify from 'react-notify'
import * as ReactNotifyCSS from '../shared/ReactNotify.css'
// Estilos
import * as cssTooltip from './Tooltip.css' // css global


class MantenedorLocales extends React.Component{
    constructor(props) {
        super(props)
        this.blackbox = new BlackBoxLocales()
        this.state = {
            idCliente: 0,
            localesFiltrados: [],
            filtros: {}
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
                this.blackbox.actualizarFiltros()
                this.setState(this.blackbox.getListaFiltrada())
            })
            //.catch(console.error)
    }

    actualizarLocal(){
        console.error('pendiente')
    }
    agregarLocal(nuevoLocal){
        // todo: agregar el blackbox
        console.log('nuevo local agregado ', nuevoLocal)
    }
    eliminarLocal(){
        console.error('pendiente')
    }

    mostrarError(titulo, cuerpo){
        this.refs.notificator.error(titulo, cuerpo, 4 * 1000);
    }

    // Filtros
    actualizarFiltro(nombreFiltro, filtro){
        this.blackbox.reemplazarFiltro(nombreFiltro, filtro)
        // actualizar los filtros, y la lista ordenada de locales
        this.setState(this.blackbox.getListaFiltrada())
    }

    render(){
        return (
            <div>
                <ReactNotify ref='notificator' className={ReactNotifyCSS}/>
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
                        // Objetos
                        localesFiltrados={this.state.localesFiltrados}
                        filtros={this.state.filtros}
                        // Metodos
                        actualizarFiltro={this.actualizarFiltro.bind(this)}
                        actualizarLocal={this.actualizarLocal.bind(this)}
                        eliminarLocal={this.eliminarLocal.bind(this)}
                    >
                        {/* Formulario para agregar un nuevo local */}
                        <RowNuevoLocal
                            // Objetos
                            clientes={this.props.clientes}
                            jornadas={this.props.jornadas}
                            formatoLocales={this.props.formatoLocales}
                            comunas={this.props.comunas}
                            // Metodos
                            agregarLocal={this.agregarLocal.bind(this)}
                            mostrarError={this.mostrarError.bind(this)}
                        />
                    </TablaLocales>
                </div>
            </div>
        )
    }
}

MantenedorLocales.propTypes = {
    clientes: React.PropTypes.array.isRequired,
    jornadas: React.PropTypes.array.isRequired,
    formatoLocales: React.PropTypes.array.isRequired,
    comunas: React.PropTypes.array.isRequired
}

export default MantenedorLocales