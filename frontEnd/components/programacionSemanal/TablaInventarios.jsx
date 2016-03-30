import React from 'react'
//import moment from 'moment'

// Componentes
//import Sticky from '../shared/react-sticky/sticky.js'
//import StickyContainer from '../shared/react-sticky/container.js'
//import TableHeader from './TableHeader.jsx'
import RowInventario from './RowInventario.jsx'

// Styles
//import sharedStyles from '../shared/shared.css'
import css from '../programacionSemanal/TablaInventario.css'

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
                        <th className={css.thFecha}>Fecha</th>
                        <th className={css.thCliente}>CL</th>
                        <th className={css.thCeco}>CECO</th>
                        <th className={css.thRegion}>RG</th>
                        <th className={css.thComuna}>Comuna</th>
                        <th className={css.thTurno}>Turno</th>
                        <th className={css.thTienda}>Tienda</th>
                        <th className={css.thStock}>Stock</th>
                        <th className={css.thDotacionTotal}>Dot.Total</th>
                        <th className={css.thLider}>Lider</th>
                        <th className={css.thLider}>Supervisor</th>
                        <th className={css.thLider}>Captador 1</th>
                        {/* <th className={css.thDotacion}>Dot.Cap1</th> */}
                        <th className={css.thLider}>Captador 2</th>
                        {/* <th className={css.thDotacion}>Dot.Cap2</th> */}
                        <th className={css.thHora}>Hr.P.Lider</th>
                        <th className={css.thHora}>Hr.P.Equipo</th>
                        <th className={css.thDireccion}>Dirección</th>
                        <th className={css.thNomina}>Nómina</th>
                    </tr>
                </thead>
                <tbody>
                    {this.props.inventarios.length===0
                        ? <tr><td colSpan="13" style={{textAlign: 'center'}}><b>No hay inventarios para mostrar en este periodo.</b></td></tr>
                        : this.props.inventarios.map((inventario, index)=>{
                            return <RowInventario
                                // Propiedades
                                key={index}
                                index={index}
                                ref={ref=>this.rows[index]=ref}
                                inventario={inventario}
                                lideres={this.props.lideres}
                                supervisores={this.props.supervisores}
                                captadores={this.props.captadores}
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
    lideres: React.PropTypes.array.isRequired,
    supervisores: React.PropTypes.array.isRequired,
    captadores: React.PropTypes.array.isRequired,
    // Metodos
    //actualizarFiltro: React.PropTypes.func.isRequired,
    guardarInventario: React.PropTypes.func.isRequired,
    guardarNomina: React.PropTypes.func.isRequired
}
export default TablaInventarios