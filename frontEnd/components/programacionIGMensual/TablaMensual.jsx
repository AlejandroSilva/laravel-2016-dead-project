import React from 'react'

// Componentes
import Sticky from '../shared/react-sticky/sticky.js'
import StickyContainer from '../shared/react-sticky/container.js'
import HeaderConFiltro from '../shared/HeaderConFiltro.jsx'

// Styles
import * as css from './TablaMensual.css'

class TablaMensual extends React.Component{
    render(){
        return (
            <StickyContainer type={React.DOM.table}  className={"table table-bordered table-condensed "+css.tableFixed}>
                <colgroup>
                    <col className={css.thCorrelativo}/>
                    <col className={css.thFecha}/>
                    <col className={css.thCliente}/>
                    <col className={css.thCeco}/>
                    <col className={css.thLocal}/>
                    <col className={css.thRegion}/>
                    <col className={css.thComuna}/>
                    <col className={css.thStock}/>
                    {/*<col className={css.thDotacion}/>*/}
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
                                ordenarLista={this.props.ordenarInventarios}
                            />
                        </th>
                        <th className={css.thCliente}>Cliente</th>
                        <th className={css.thCeco}>CE</th>
                        <th className={css.thLocal}>Local</th>
                        <th className={css.thRegion}>
                            <HeaderConFiltro
                                nombre="Región"
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
                        <th className={css.thStock}>Stock</th>
                        {/*<th className={css.thDotacion}>Dot.Total</th>*/}
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
TablaMensual.propTypes = {
    // Objetos
    filtros: React.PropTypes.objectOf(React.PropTypes.array).isRequired,
    // Metodos
    actualizarFiltro: React.PropTypes.func.isRequired,
    ordenarInventarios: React.PropTypes.func.isRequired
}
export default TablaMensual