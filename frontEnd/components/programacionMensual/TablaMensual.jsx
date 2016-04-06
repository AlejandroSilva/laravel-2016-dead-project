import React from 'react'
import moment from 'moment'

// Componentes
import Sticky from '../shared/react-sticky/sticky.js'
import StickyContainer from '../shared/react-sticky/container.js'
import TableHeader from './TableHeader.jsx'
import RowInventario from './RowInventarioMensual.jsx'

// Styles
//import sharedStyles from '../shared/shared.css'
import * as css from './TablaMensual.css'

class TablaMensual extends React.Component{
    constructor(props){
        super(props)
        // referencia a todos las entradas de fecha de los inventarios
        this.inputFecha = []
    }
    componentWillReceiveProps(){
        // cuando se pasa de mes a mes, se generand posiciones "vacias" en el arreglo inputFecha, esto lo soluciona
        this.inputFecha = this.inputFecha.filter(input=>input!==null)
    }

    focusFilaSiguiente(indexActual, nombreElemento){
        let nextIndex = (indexActual+1)%this.inputFecha.length
        let nextRow = this.inputFecha[nextIndex]
        nextRow.focusElemento(nombreElemento)
    }
    focusFilaAnterior(indexActual, nombreElemento){
        let prevIndex = indexActual===0? this.inputFecha.length-1 : indexActual-1
        let prevRow = this.inputFecha[prevIndex]
        prevRow.focusElemento(nombreElemento)
    }

    render(){
        return (
            <div>
                {/* Table */}
                <StickyContainer type={React.DOM.table}  className={"table table-bordered table-condensed "+css.tableFixed}>
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
                                {/*<span className={'glyphicon glyphicon-sort-by-attributes-alt pull-right'}></span>*/}
                            </th>
                            <th className={css.thCliente}>
                                <TableHeader nombre="Cliente"
                                             filtro={this.props.filtroClientes}
                                             actualizarFiltro={this.props.actualizarFiltro.bind(this, 'cliente')}
                                />
                            </th>
                            <th className={css.thCeco}>Ceco</th>
                            <th className={css.thLocal}>Local</th>
                            <th className={css.thRegion}>
                                <TableHeader nombre="Región"
                                             filtro={this.props.filtroRegiones}
                                             actualizarFiltro={this.props.actualizarFiltro.bind(this, 'region')}
                                />
                            </th>
                            <th className={css.thComuna}>Comuna</th>
                            <th className={css.thStock}>Stock</th>
                            <th className={css.thDotacion}>Dot.Total</th>
                            {/*<th className={css.thJornada}>Jornada</th>*/}
                            {/*<th className={css.thEstado}>Estado</th>*/}
                            <th className={css.thOpciones}>Opciones</th>
                        </Sticky>
                    </thead>
                    <tbody>
                    {}
                    {this.props.inventariosFiltrados.length===0
                        ? <tr><td colSpan="10" style={{textAlign: 'center'}}><b>No hay inventarios para mostrar en este periodo.</b></td></tr>
                        : this.props.inventariosFiltrados.map((inventario, index)=>{
                            return <RowInventario
                                key={index}
                                index={index}
                                inventario={inventario}

                                // Metodos
                                focusFilaSiguiente={this.focusFilaSiguiente.bind(this)}
                                focusFilaAnterior={this.focusFilaAnterior.bind(this)}
                                guardarOCrearInventario={this.props.guardarOCrearInventario}
                                quitarInventario={this.props.quitarInventario}
                                //guardarOCrear={this.guardarOCrear.bind(this)}
                                ref={ref=>this.inputFecha[index]=ref}

                            />
                        })
                    }
                    </tbody>
                </StickyContainer>
            </div>
        )
    }
}
TablaMensual.propTypes = {
    // Objetos
    inventariosFiltrados: React.PropTypes.array.isRequired,
    filtroClientes: React.PropTypes.array.isRequired,
    filtroRegiones: React.PropTypes.array.isRequired,
    // Metodos
    actualizarFiltro: React.PropTypes.func.isRequired,
    guardarOCrearInventario: React.PropTypes.func.isRequired,
    quitarInventario: React.PropTypes.func.isRequired,
    ordenarInventarios: React.PropTypes.func.isRequired
}
export default TablaMensual