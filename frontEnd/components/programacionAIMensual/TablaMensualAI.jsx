import React from 'react'
import moment from 'moment'

// Componentes
import Sticky from '../shared/react-sticky/sticky.js'
import StickyContainer from '../shared/react-sticky/container.js'
import TableHeader from './TableHeader.jsx'
import RowAuditoriaMensual from './RowAuditoriaMensual.jsx'

// Styles
//import sharedStyles from '../shared/shared.css'
import * as css from './TablaMensualAI.css'

class TablaMensualAI extends React.Component{
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
                    <col className={css.thCorrelativo}/>
                    <col className={css.thFecha}/>
                    <col className={css.thCliente}/>
                    <col className={css.thCeco}/>
                    <col className={css.thRegion}/>
                    <col className={css.thComuna}/>
                    <col className={css.thLocal}/>
                    <col className={css.thStock}/>
                    <col className={css.thLider}/>
                    <col className={css.thAperturaCierre}/>
                    <col className={css.thAperturaCierre}/>
                    <col className={css.thDireccion}/>
                    <col className={css.thOpciones}/>
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
                                {/*<span className={'glyphicon glyphicon-sort-by-attributes-alt pull-right'}></span>*/}
                            </th>
                            <th className={css.thCliente}>
                                <TableHeader nombre="Cliente"
                                             filtro={this.props.filtroClientes}
                                             actualizarFiltro={this.props.actualizarFiltro.bind(this, 'cliente')}
                                />
                            </th>
                            <th className={css.thCeco}>Ceco</th>
                            <th className={css.thRegion}>
                                <TableHeader nombre="Región"
                                             filtro={this.props.filtroRegiones}
                                             actualizarFiltro={this.props.actualizarFiltro.bind(this, 'region')}
                                />
                            </th>
                            <th className={css.thComuna}>Comuna</th>
                            <th className={css.thLocal}>Local</th>
                            <th className={css.thStock}>Stock</th>
                            <th className={css.thLider}>Auditor</th>
                            <th className={css.thAperturaCierre}>Hr.Apertura</th>
                            <th className={css.thAperturaCierre}>Hr.Cierre</th>
                            <th className={css.thDireccion}>Dirección</th>
                            <th className={css.thOpciones}>Opciones</th>
                        </Sticky>
                    </thead>
                    <tbody>
                    {}
                    {this.props.auditoriasFiltradas.length===0
                        ? <tr><td colSpan="13" style={{textAlign: 'center'}}><b>No hay auditorias para mostrar en este periodo.</b></td></tr>
                        : this.props.auditoriasFiltradas.map((auditoria, index)=>{
                            let mostrarSeparador = false
                            let sgteAuditoria = this.props.auditoriasFiltradas[index+1]
                            if(sgteAuditoria)
                                mostrarSeparador = auditoria.fechaProgramada!==sgteAuditoria.fechaProgramada
                            return <RowAuditoriaMensual
                                // Propiedades
                                puedeModificar={this.props.puedeModificar}
                                key={index}
                                index={index}
                                auditoria={auditoria}
                                mostrarSeparador={mostrarSeparador}
                                auditores={this.props.auditores}
                                // Metodos
                                focusFilaSiguiente={this.focusFilaSiguiente.bind(this)}
                                focusFilaAnterior={this.focusFilaAnterior.bind(this)}
                                actualizarAuditoria={this.props.actualizarAuditoria}
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
TablaMensualAI.propTypes = {
    // Objetos
    puedeModificar: React.PropTypes.bool.isRequired,
    auditoriasFiltradas: React.PropTypes.array.isRequired,
    auditores: React.PropTypes.array.isRequired,
    filtroClientes: React.PropTypes.array.isRequired,
    filtroRegiones: React.PropTypes.array.isRequired,
    // Metodos
    actualizarFiltro: React.PropTypes.func.isRequired,
    actualizarAuditoria: React.PropTypes.func.isRequired,
    quitarInventario: React.PropTypes.func.isRequired,
    ordenarAuditorias: React.PropTypes.func.isRequired
}
export default TablaMensualAI