import React from 'react'
//import moment from 'moment'

// Componentes
//import Sticky from '../shared/react-sticky/sticky.js'
//import StickyContainer from '../shared/react-sticky/container.js'
import RowInventarioSemanal from './RowInventarioSemanal.jsx'

// Styles
import * as css from './TablaSemanal.css'

class TablaInventarios extends React.Component{
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
            <table className={"table table-bordered table-condensed "+css.tableFixed}
                style={{overfow: 'overlay'}}>
                <thead>
                    <tr>
                        <th className={css.thCorrelativo}>#</th>
                        <th className={css.thFecha}>
                            Fecha
                            <span className={'glyphicon glyphicon-sort-by-attributes pull-right'}
                                  onClick={ this.props.ordenarInventarios }
                            />
                        </th>
                        <th className={css.thCliente}>CL</th>
                        <th className={css.thCeco}>CE</th>
                        <th className={css.thRegion}>RG</th>
                        <th className={css.thComuna}>Comuna</th>
                        <th className={css.thTurno}>Turno</th>
                        <th className={css.thTienda}>Tienda</th>
                        <th className={css.thStock}>Stock</th>
                        <th className={css.thDotacionTotal}>Dot.Total</th>
                        <th className={css.thLider}>Lider</th>
                        {/* <th className={css.thLider}>Supervisor</th> */}
                        <th className={css.thLider}>Captador 1</th>
                        <th className={css.thHora}>Hr.Lider</th>
                        <th className={css.thHora}>Hr.Equipo</th>
                        <th className={css.thDireccion}>Dirección</th>
                        <th className={css.thNomina}>Nómina</th>
                    </tr>
                </thead>
                <tbody>
                    {this.props.inventarios.length===0
                        ? <tr><td colSpan="14" style={{textAlign: 'center'}}><b>No hay inventarios para mostrar en este periodo.</b></td></tr>
                        : this.props.inventarios.map((inventario, index)=>{
                            let mostrarSeparador = false
                            let sgteInventario = this.props.inventarios[index+1]
                            if(sgteInventario)
                                mostrarSeparador = inventario.fechaProgramada!==sgteInventario.fechaProgramada
                            return <RowInventarioSemanal
                                // Propiedades
                                puedeModificar={this.props.puedeModificar}
                                key={index}
                                index={index}
                                ref={ref=>this.rows[index]=ref}
                                inventario={inventario}
                                lideres={this.props.lideres}
                                supervisores={this.props.supervisores}
                                captadores={this.props.captadores}
                                mostrarSeparador={mostrarSeparador}
                                // Metodos
                                guardarInventario={this.props.guardarInventario}
                                guardarNomina={this.props.guardarNomina}
                                focusRow={this.focusRow.bind(this)}
                            />
                        })}
                </tbody>
            </table>
        )
    }
}
TablaInventarios.propTypes = {
    // Objetos
    puedeModificar: React.PropTypes.bool.isRequired,
    lideres: React.PropTypes.array.isRequired,
    supervisores: React.PropTypes.array.isRequired,
    captadores: React.PropTypes.array.isRequired,
    // Metodos
    //actualizarFiltro: React.PropTypes.func.isRequired,
    guardarInventario: React.PropTypes.func.isRequired,
    guardarNomina: React.PropTypes.func.isRequired,
    ordenarInventarios: React.PropTypes.func.isRequired
}
export default TablaInventarios