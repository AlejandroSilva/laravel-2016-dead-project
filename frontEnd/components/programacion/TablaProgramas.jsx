import React from 'react'
import moment from 'moment'

// Componentes
import Sticky from '../shared/react-sticky/sticky.js'
import StickyContainer from '../shared/react-sticky/container.js'
import TableHeader from './TableHeader.jsx'
import RowInventario from './RowInventario.jsx'

// Styles
import sharedStyles from '../shared/shared.css'
import styles from './TablaProgramas.css'

class TablaInventarios extends React.Component{
    constructor(props){
        super(props)
        // referencia a todos las entradas de fecha de los inventarios
        this.inputFecha = []
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

    // Reemplazar el filtro que es actualizado por la TableHeader
    //reemplazarFiltro(nombreFiltro, filtroActualizado) {
    //    // se reemplaza el filtro indicado en 'nombreFiltro'
    //    let nuevoFiltro = this.state.filtro
    //    nuevoFiltro[nombreFiltro] = filtroActualizado
    //
    //    this.setState({
    //        filtro: nuevoFiltro,
    //        programasFiltrados: this.filtrarInventarios(this.state.programas, nuevoFiltro)
    //    })
    //}

    render(){
        return (
            <div>
                {/* Table */}
                <StickyContainer type={React.DOM.table}  className="table table-bordered table-condensed">
                    <thead>
                        {/* TR que se pega al top de la pagina, es una TR, con instancia de 'Sticky' */}
                        <Sticky
                            topOffset={-50}
                            type={React.DOM.tr}
                            stickyStyle={{top: '50px'}}>

                            <th className={styles.thCorrelativo}>#</th>
                            <th className={styles.thFecha}>Fecha</th>
                            <th className={styles.thCliente}>
                                <TableHeader nombre="Cliente"
                                          //filtro={this.state.filtro.clientes}
                                          //onFiltroChanged={this.reemplazarFiltro.bind(this, 'clientes')}
                                             filtro={[]}
                                             onFiltroChanged={()=>{}}
                                />
                            </th>
                            <th className={styles.thCeco}>Ceco</th>
                            <th className={styles.thLocal}>Local</th>
                            <th className={styles.thRegion}>
                                <TableHeader nombre="Región"
                                          //filtro={this.state.filtro.regiones}
                                          //onFiltroChanged={this.reemplazarFiltro.bind(this, 'regiones')}
                                             filtro={[]}
                                             onFiltroChanged={()=>{}}
                                />
                            </th>
                            <th className={styles.thComuna}>Comuna</th>
                            <th className={styles.thStock}>Stock</th>
                            <th className={styles.thDotacion}>Dotación</th>
                            <th className={styles.thJornada}>Jornada</th>
                            {/*<th className={styles.thEstado}>Estado</th>*/}
                            <th className={styles.thOpciones}>Opciones</th>
                        </Sticky>
                    </thead>
                    <tbody>
                    {this.props.inventariosFiltrados.map((inventario, index)=>{
                        return <RowInventario
                            key={index}
                            index={index}
                            inventario={inventario}

                            // Metodos
                            focusFilaSiguiente={this.focusFilaSiguiente.bind(this)}
                            focusFilaAnterior={this.focusFilaAnterior.bind(this)}
                            guardarOCrearInventario={this.props.guardarOCrearInventario}
                            //guardarOCrear={this.guardarOCrear.bind(this)}
                            ref={ref=>this.inputFecha[index]=ref}
                        />
                    })}
                    </tbody>
                </StickyContainer>
            </div>
        )
    }
}
TablaInventarios.propTypes = {
    inventariosFiltrados: React.PropTypes.array.isRequired,
    guardarOCrearInventario: React.PropTypes.func.isRequired
}
export default TablaInventarios