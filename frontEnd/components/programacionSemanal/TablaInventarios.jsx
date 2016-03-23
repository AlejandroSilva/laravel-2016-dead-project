import React from 'react'
//import moment from 'moment'

// Componentes
//import Sticky from '../shared/react-sticky/sticky.js'
//import StickyContainer from '../shared/react-sticky/container.js'
//import TableHeader from './TableHeader.jsx'
import RowInventario from './RowInventario.jsx'

// Styles
//import sharedStyles from '../shared/shared.css'
import css from '../programacionMensual/RowInventario.css'

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
            <table className="table table-bordered table-condensed">
                <thead>
                    <tr>
                        <th className={css.thFecha}>FECHA</th>
                        <th>CLIENTE</th>
                        <th>CEC</th>
                        <th>RG</th>
                        <th>COMUNA</th>
                        <th>TURNO</th>
                        <th>TIENDA</th>
                        <th>STOCK</th>
                        <th>Pro</th>
                        <th>DOTACION</th>
                        <th>LIDER</th>
                        <th>CAP1</th>
                        <th>Dot1</th>
                        <th>CAP2</th>
                        <th>Dot2</th>
                        <th>Horario Pres.</th>
                        <th>DIRECCION</th>
                        <th>Opciones</th>
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
                                captadores={this.props.captadores}
                                // Metodos
                                guardarInventario={this.props.guardarInventario}
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
    //inventariosFiltrados: React.PropTypes.array.isRequired,
    //filtroClientes: React.PropTypes.array.isRequired,
    //filtroRegiones: React.PropTypes.array.isRequired,
    // Metodos
    //actualizarFiltro: React.PropTypes.func.isRequired,
    guardarInventario: React.PropTypes.func.isRequired
}
export default TablaInventarios