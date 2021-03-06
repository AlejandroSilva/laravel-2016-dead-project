import React from 'react'

// Componentes
import Sticky from '../../components/shared/react-sticky/sticky.js'
import StickyContainer from '../../components/shared/react-sticky/container.js'
import HeaderConFiltro from '../../components/shared/HeaderConFiltro.jsx'

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
                    <col className={css.thUsuario}/>
                    <col className={css.thHora}/>
                    <col className={css.thHora}/>
                    <col className={css.thUsuario}/>
                    <col className={css.thUsuario}/>
                    <col className={css.thDireccion}/>
                    <col className={css.thNomina}/>
                    <col className={css.thPatentes}/>
                    <col className={css.thUnidadesReales}/>
                    <col className={css.thUnidadesTeoricas}/>
                    <col className={css.thArchivoFinal}/>
                    {/*<col className={css.thNomimaPago}/>*/}
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
                                // se quita el boton de ordenar por dos motivos:
                                // 1) espacio
                                // 2) todas las listas por defecto se ordenan en la vista semanal, solo en la mensual se desordenan
                                //ordenarLista={this.props.ordenarInventarios}
                            />
                        </th>
                        <th className={css.thCliente}>CL</th>
                        <th className={css.thCeco}>
                            <HeaderConFiltro
                                nombre='CE'
                                filtro={this.props.filtros.filtroCeco || []}
                                actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroCeco')}
                                busquedaExacta={true}
                            />
                        </th>
                        <th className={css.thRegion}>
                            <HeaderConFiltro
                                nombre='RG'
                                filtro={this.props.filtros.filtroRegiones || []}
                                actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroRegiones')}
                                busquedaExacta={true}
                            />
                        </th>
                        <th className={css.thComuna}>
                            <HeaderConFiltro
                                nombre='Comuna'
                                filtro={this.props.filtros.filtroComunas || []}
                                actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroComunas')}
                            />
                        </th>
                        <th className={css.thTurno}>
                            <HeaderConFiltro
                                nombre='Turno'
                                filtro={this.props.filtros.filtroTurnos || []}
                                actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroTurnos')}
                            />
                        </th>
                        <th className={css.thTienda}>Tienda</th>
                        <th className={css.thStock}>Stock</th>
                        <th className={css.thDotacionTotal}>Dot.Total</th>
                        <th className={css.thUsuario}>
                            <HeaderConFiltro
                                nombre='Lider'
                                filtro={this.props.filtros.filtroLideres || []}
                                actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroLideres')}
                            />
                        </th>
                        <th className={css.thHora}>Hr.Lider</th>
                        <th className={css.thHora}>Hr.Equipo</th>
                        <th className={css.thUsuario}>
                            Supervisor
                        </th>
                        <th className={css.thUsuario}>
                            <HeaderConFiltro
                                nombre='Captador'
                                filtro={this.props.filtros.filtroCaptadores || []}
                                actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroCaptadores')}
                            />
                        </th>
                        <th className={css.thDireccion}>Dirección</th>
                        <th className={css.thNomina}>
                            Nómina
                            {/*<HeaderConFiltro
                                nombre='Nómina'
                                filtro={this.props.filtros.filtroFechaSubidaNomina || []}
                                actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroFechaSubidaNomina')}
                            />
                            */}
                        </th>
                        <th className={css.thPatentes}>PTT</th>
                        <th className={css.thUnidadesReales}>U.Cont</th>
                        <th className={css.thUnidadesTeoricas}>U.Teo</th>
                        <th className={css.thArchivoFinal}>Acta</th>
                        {/*<th className={css.thNomimaPago}>Nom.Pago</th>*/}
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