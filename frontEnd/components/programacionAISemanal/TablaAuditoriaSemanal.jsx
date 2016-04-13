import React from 'react'
//import moment from 'moment'

// Componentes
import Sticky from '../shared/react-sticky/sticky.js'
import StickyContainer from '../shared/react-sticky/container.js'
import RowAuditoriaSemanal from './RowAuditoriaSemanal.jsx'

// Styles
import * as css from './TablaAuditoriaSemanal.css'

class TablaAuditoriaSemanal extends React.Component{
    constructor(props){
        super(props)
        // referencia a todos las entradas de fecha de los inventarios
        this.rows = []
    }
    
    componentWillReceiveProps(nextProps){
        // cuando se pasa de mes a mes, se generand posiciones "vacias" en el arreglo inputFecha, esto lo soluciona
        this.rows = this.rows.filter(input=>input!==null)
    }
    focusRow(index, nombreElemento){
        let ultimoIndex = this.rows.length-1
        // seleccionar "antes de la primera"
        if(index<0)
            index = ultimoIndex
        if(index>ultimoIndex)
            index = index%this.rows.length
        
        let nextRow = this.rows[index]
        nextRow.focusElemento(nombreElemento)
    }
    
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
                    <col className={css.thLider}/>
                    <col className={css.thAperturaCierre}/>
                    <col className={css.thAperturaCierre}/>
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
                                  onClick={ this.props.ordenarAuditorias }
                            />
                        </th>
                        <th className={css.thCliente}>CL</th>
                        <th className={css.thCeco}>CE</th>
                        <th className={css.thRegion}>RG</th>
                        <th className={css.thComuna}>Comuna</th>
                        <th className={css.thTienda}>Tienda</th>
                        <th className={css.thStock}>Stock</th>
                        <th className={css.thLider}>Auditor</th>
                        {/*<th className={css.thHora}>Hr.Lider</th>*/}
                        <th className={css.thAperturaCierre}>Hr.Apertura</th>
                        <th className={css.thAperturaCierre}>Hr.Cierre</th>
                        <th className={css.thDireccion}>Dirección</th>
                        <th className={css.thNomina}>Opciones</th>
                    </Sticky>
                </thead>
                <tbody>
                    {this.props.auditorias.length===0
                        ? <tr><td colSpan="13" style={{textAlign: 'center'}}><b>No hay inventarios para mostrar en este periodo.</b></td></tr>
                        : this.props.auditorias.map((auditoria, index)=>{
                            let mostrarSeparador = false
                            let sgteInventario = this.props.auditorias[index+1]
                            if(sgteInventario)
                                mostrarSeparador = auditoria.fechaProgramada!==sgteInventario.fechaProgramada
                            return <RowAuditoriaSemanal
                                // Propiedades
                                puedeModificar={this.props.puedeModificar}
                                key={index}
                                index={index}
                                ref={ref=>this.rows[index]=ref}
                                auditoria={auditoria}
                                // lideres={this.props.lideres}
                                // supervisores={this.props.supervisores}
                                // captadores={this.props.captadores}
                                mostrarSeparador={mostrarSeparador}
                                auditores={this.props.auditores}
                                // Metodos
                                guardarAuditoria={this.props.guardarAuditoria}
                                focusRow={this.focusRow.bind(this)}
                            />
                        })}
                </tbody>
            </StickyContainer>
        )
    }
}
TablaAuditoriaSemanal.propTypes = {
    // Objetos
    puedeModificar: React.PropTypes.bool.isRequired,
    auditorias: React.PropTypes.array.isRequired,
    auditores: React.PropTypes.array.isRequired,
    // supervisores: React.PropTypes.array.isRequired,
    // captadores: React.PropTypes.array.isRequired,
    // Metodos
    //actualizarFiltro: React.PropTypes.func.isRequired,
    guardarAuditoria: React.PropTypes.func.isRequired,
    ordenarAuditorias: React.PropTypes.func.isRequired
}
export default TablaAuditoriaSemanal