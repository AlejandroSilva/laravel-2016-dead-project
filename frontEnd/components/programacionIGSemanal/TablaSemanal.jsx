import React from 'react'
//import moment from 'moment'

// Componentes
import Sticky from '../shared/react-sticky/sticky.js'
import StickyContainer from '../shared/react-sticky/container.js'
import HeaderConFiltro from '../shared/HeaderConFiltro.jsx'


// Styles
import * as css from './TablaSemanal.css'

class TablaInventarios extends React.Component{
    render(){
        return (
            <StickyContainer type={React.DOM.table} className={"table table-bordered table-condensed "+css.tableFixed}>
                <colgroup>
                    <col className={css.thCorrelativo}/>
                    <col className={css.thFecha}/>
                    <col className={css.thCliente}/>
                    <col className={css.thCeco}/>
                    <col className={css.thRegion}/>
                    <col className={css.thComuna}/>
                    <col className={css.thTurno}/>
                    <col className={css.thTienda}/>
                    <col className={css.thStock}/>
                    <col className={css.thDotacionTotal}/>
                    <col className={css.thLider}/>
                    <col className={css.thLider}/>
                    <col className={css.thHora}/>
                    <col className={css.thHora}/>
                    <col className={css.thDireccion}/>
                    <col className={css.thNomina}/>
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
                                  onClick={ this.props.ordenarInventarios }
                            />
                        </th>
                        <th className={css.thCliente}>CL</th>
                        <th className={css.thCeco}>CE</th>
                        <th className={css.thRegion}>
                            <HeaderConFiltro
                                nombre='RG'
                                filtro={this.props.filtros.filtroRegiones || []}
                                actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroRegiones')}
                            />
                        </th>
                        <th className={css.thComuna}>
                            <HeaderConFiltro
                                nombre='Comuna'
                                filtro={this.props.filtros.filtroComunas || []}
                                actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroComunas')}
                            />
                        </th>
                        <th className={css.thTurno}>Turno</th>
                        <th className={css.thTienda}>Tienda</th>
                        <th className={css.thStock}>Stock</th>
                        <th className={css.thDotacionTotal}>Dot.Total</th>
                        <th className={css.thLider}>
                            <HeaderConFiltro
                                nombre='Lider'
                                filtro={this.props.filtros.filtroLideres || []}
                                actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroLideres')}
                            />
                        </th>
                        {/* <th className={css.thLider}>Supervisor</th> */}
                        <th className={css.thLider}>
                            <HeaderConFiltro
                                nombre='Captador'
                                filtro={this.props.filtros.filtroCaptadores || []}
                                actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroCaptadores')}
                            />
                        </th>
                        <th className={css.thHora}>Hr.Lider</th>
                        <th className={css.thHora}>Hr.Equipo</th>
                        <th className={css.thDireccion}>Dirección</th>
                        <th className={css.thNomina}>Nómina</th>
                    </Sticky>
                </thead>
                <tbody>
                    {this.props.children}
                </tbody>
            </StickyContainer>
        )
    }
}
TablaInventarios.propTypes = {
    // Objetos
    filtros: React.PropTypes.objectOf(React.PropTypes.array).isRequired,
    // Metodos
    ordenarInventarios: React.PropTypes.func.isRequired,
    actualizarFiltro: React.PropTypes.func.isRequired
}
export default TablaInventarios