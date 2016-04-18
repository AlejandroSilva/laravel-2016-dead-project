import React from 'react'

// Componentes
import Sticky from '../shared/react-sticky/sticky.js'
import StickyContainer from '../shared/react-sticky/container.js'
import HeaderConFiltro from '../shared/HeaderConFiltro.jsx'

// Styles
import * as css from './TablaAuditoriaSemanal.css'

class TablaAuditoriaSemanal extends React.Component{
    render(){
        return (
            <StickyContainer type={React.DOM.table}  className={"table table-bordered table-condensed "+css.tableFixed}>
                <colgroup>
                    <col className={css.thCorrelativo}/>
                    <col className={css.thFecha}/>
                    <col className={css.thCliente}/>
                    <col className={css.thCeco}/>
                    <col className={css.thRegion}/>
                    <col className={css.thComuna}/>
                    <col className={css.thTienda}/>
                    <col className={css.thStock}/>
                    <col className={css.thAuditor}/>
                    <col className={css.thRealizadaAprobada}/>
                    <col className={css.thRealizadaAprobada}/>
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
                            Fecha
                            <span className={'glyphicon glyphicon-sort-by-attributes pull-right'}
                                  onClick={ this.props.ordenarAuditorias }
                            />
                        </th>
                        <th className={css.thCliente}>CL</th>
                        <th className={css.thCeco}>
                            CE
                            {/*<HeaderConFiltro
                                nombre="CE"
                                filtro={this.props.filtros.filtroLocales || []}
                                actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroLocales')}
                            />*/}
                        </th>
                        <th className={css.thRegion}>
                            <HeaderConFiltro
                                nombre="RG"
                                filtro={this.props.filtros.filtroRegiones || []}
                                actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroRegiones')}
                            />
                        </th>
                        <th className={css.thComuna}>
                            <HeaderConFiltro
                                nombre="Comuna"
                                filtro={this.props.filtros.filtroComunas || []}
                                actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroComunas')}
                            />
                        </th>
                        <th className={css.thTienda}>Tienda</th>
                        <th className={css.thStock}>Stock</th>
                        <th className={css.thAuditor}>
                            <HeaderConFiltro
                                nombre="Auditor"
                                filtro={this.props.filtros.filtroAuditores || []}
                                actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroAuditores')}
                            />
                        </th>
                        <th className={css.thRealizadaAprobada}>Realizada</th>
                        <th className={css.thRealizadaAprobada}>Aprobada</th>
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
        )
    }
}
TablaAuditoriaSemanal.propTypes = {
    // Objetos
    filtros: React.PropTypes.objectOf(React.PropTypes.array).isRequired,
    // Metodos
    ordenarAuditorias: React.PropTypes.func.isRequired,
    actualizarFiltro: React.PropTypes.func.isRequired
}
export default TablaAuditoriaSemanal