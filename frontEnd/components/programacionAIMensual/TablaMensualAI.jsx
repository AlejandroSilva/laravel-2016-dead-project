import React from 'react'
import moment from 'moment'

// Componentes
import Sticky from '../shared/react-sticky/sticky.js'
import StickyContainer from '../shared/react-sticky/container.js'
import HeaderConFiltro from '../shared/HeaderConFiltro.jsx'

// Styles
import * as css from './TablaMensualAI.css'

class TablaMensualAI extends React.Component{
    render(){
        return (
            <div>
                {/* Table */}
                <StickyContainer type={React.DOM.table}  className={"table table-bordered table-condensed "+css.tableFixed}>
                    <colgroup>
                        <col className={css.thCorrelativo}/>
                        <col className={css.thFecha}/>
                        <col className={css.thCliente}/>
                        <col className={css.thCeco}/>
                        <col className={css.thRegion}/>
                        <col className={css.thComuna}/>
                        <col className={css.thLocal}/>
                        <col className={css.thStock}/>
                        <col className={css.thAuditor}/>
                        <col className={css.thAperturaCierre}/>
                        <col className={css.thAperturaCierre}/>
                        <col className={css.thDireccion}/>
                        <col className={css.thOpciones}/>
                    </colgroup>
                    <thead>
                        {/* TR que se pega al top de la pagina, es una TR, con instancia de 'Sticky' */}
                        <Sticky
                            topOffset={-50}
                            type={React.DOM.tr}
                            stickyStyle={{top: '50px'}}>

                            <th className={css.thCorrelativo}>#</th>
                            <th className={css.thFecha}>
                                <HeaderConFiltro
                                    nombre='Fecha'
                                    filtro={this.props.filtros.filtroFechas || []}
                                    actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroFechas')}
                                    ordenarLista={this.props.ordenarAuditorias}
                                />
                            </th>
                            <th className={css.thCliente}>
                                <HeaderConFiltro nombre="Cliente"
                                     filtro={this.props.filtros.filtroClientes || []}
                                     actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroClientes')}
                                />
                            </th>
                            <th className={css.thCeco}>Ceco</th>
                            <th className={css.thRegion}>
                                <HeaderConFiltro nombre="Región"
                                     filtro={this.props.filtros.filtroRegiones || [] }
                                     actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroRegiones')}
                                />
                            </th>
                            <th className={css.thComuna}>
                                <HeaderConFiltro nombre="Comuna"
                                     filtro={this.props.filtros.filtroComunas || [] }
                                     actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroComunas')}
                                />
                            </th>
                            <th className={css.thLocal}>Local</th>
                            <th className={css.thStock}>Stock</th>
                            <th className={css.thAuditor}>
                                <HeaderConFiltro nombre="Auditor"
                                     filtro={this.props.filtros.filtroAuditores || [] }
                                     actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroAuditores')}
                                />
                            </th>
                            <th className={css.thAperturaCierre}>Hr.Apertura</th>
                            <th className={css.thAperturaCierre}>Hr.Cierre</th>
                            <th className={css.thDireccion}>Dirección</th>
                            <th className={css.thOpciones}>Opciones</th>
                        </Sticky>
                    </thead>
                    <tbody>
                        {this.props.children}
                    </tbody>
                </StickyContainer>
            </div>
        )
    }
}
TablaMensualAI.propTypes = {
    // Objetos
    filtros: React.PropTypes.objectOf(React.PropTypes.array).isRequired,
    // Metodos
    ordenarAuditorias: React.PropTypes.func.isRequired,
    actualizarFiltro: React.PropTypes.func.isRequired
}
export default TablaMensualAI