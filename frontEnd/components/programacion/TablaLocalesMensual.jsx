import React from 'react'
let PropTypes = React.PropTypes
import api from '../../apiClient/v1'
import moment from 'moment'

// Componentes
import Sticky from '../shared/react-sticky/sticky.js'
import StickyContainer from '../shared/react-sticky/container.js'
import Cabecera from './Cabecera.jsx'
import RowLocales from './RowLocales.jsx'

// Styles
import sharedStyles from '../shared/shared.css'
import styles from './TablaLocalesMensual.css'

class TablaLocalesMensual extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            inventarios: [],
            inventariosFiltrados: [],
            filtro: {
                clientes: [],
                regiones: []
            }
        }
        // referencia a todos las entradas de fecha de los inventarios
        this.inputFecha = []
    }
    focusFilaSiguiente(indexActual){
        let nextIndex = (indexActual+1)%this.inputFecha.length
        let nextRow = this.inputFecha[nextIndex]
        nextRow.focusFecha()
    }
    focusFilaAnterior(indexActual){
        let prevIndex = indexActual===0? this.inputFecha.length-1 : indexActual-1
        let prevRow = this.inputFecha[prevIndex]
        prevRow.focusFecha()
    }

    guardarOCrear(datos){
        return api.inventario.nuevo(datos)
    }

    agregarInventario(nuevoInventario, mesAnno){
        let [mes, anno] = mesAnno.split('-')
        let inventariosActualizados = this.state.inventarios

        let inventarioNoAgregado = this.state.inventarios.find(inventario=>inventario.idLocal===nuevoInventario.idLocal)===undefined
        if( inventarioNoAgregado ){
            // buscar asincronicamente la informacion completa al servidor
            api.locales.getVerbose(nuevoInventario.idLocal)
                .then(this.actualizarInventario.bind(this))
                .catch(error=>console.error(`error al obtener los datos de ${idLocal}`, error))

            nuevoInventario.mesProgramado = mes
            nuevoInventario.annoProgramado = anno

            // modificar la estructura el objeto para que sea mas manejable
            nuevoInventario.cliente = {}
            nuevoInventario.comuna = {}
            nuevoInventario.provincia = {}
            nuevoInventario.region = {}
            nuevoInventario.zona = {}

            // actualizar la lista de locales
            inventariosActualizados.push(nuevoInventario)

            // actualizar la lista de locales, y el filtro
            let filtroActualizado = this.generarFiltro(inventariosActualizados)
            let inventariosFiltrados = this.filtrarInventarios(inventariosActualizados, filtroActualizado)

            this.setState({
                inventarios: inventariosActualizados,
                filtro: filtroActualizado,
                inventariosFiltrados: inventariosFiltrados
            })
        }
    }
    actualizarInventario(local){
        let localesActualizados = this.state.inventarios.map(inventario=>{
            if(inventario.idLocal===local.idLocal) {
                // modificar la estructura el objeto para que sea mas manejable
                local.comuna = local.direccion.comuna || {}
                local.provincia = local.comuna.provincia || {}
                local.region = local.provincia.region || {}
                local.zona = local.region.zona || {}

                // mezclar los objetos
                return Object.assign(inventario, local)
            }else
                return inventario
        })
        let filtroActualizado = this.generarFiltro(localesActualizados)
        let inventariosFiltrados = this.filtrarInventarios(localesActualizados, filtroActualizado)

        this.setState({
            // actualizar los datos de la lista con la informacion obtenida por el api
            inventarios: localesActualizados,
            filtro: filtroActualizado,
            inventariosFiltrados: inventariosFiltrados
        })
    }

    generarFiltro(inventarios){
        // TODO: simplificar este metodo, hay mucho codigo repetido
        const filtrarSoloUnicos = (valor, index, self)=>self.indexOf(valor)===index

        // FILTRO CLIENTES
        // obtener una lista de clientes sin repetir
        const seleccionarNombreCliente = inventario=>inventario.cliente.nombreCorto || ''
        let clientesUnicos = inventarios.map(seleccionarNombreCliente).filter(filtrarSoloUnicos)

        // crear el filtro con los datos del filtro anterior
        let filtroClientes = clientesUnicos.map(textoUnico=>{
            let opcion = this.state.filtro.clientes.find(opc=> opc.texto===textoUnico)

            // si no existe la opcion, se crea y se selecciona por defecto
            return opcion || { texto: textoUnico, seleccionado: true}
        })

        // FILTRO REGIONES
        const seleccionarNombreRegion = inventario=>inventario.region.nombreCorto || ''
        let regionesUnicas = inventarios.map(seleccionarNombreRegion).filter(filtrarSoloUnicos)

        // crear el filtro con los datos del filtro anterior
        let filtroRegiones = regionesUnicas.map(textoUnico=>{
            let opcion = this.state.filtro.regiones.find(opc=> opc.texto===textoUnico)

            // si no existe la opcion, se crea y se selecciona por defecto
            return opcion || { texto: textoUnico, seleccionado: true}
        })

        return {
            clientes: filtroClientes,
            regiones: filtroRegiones
        }
    }

    filtrarInventarios(inventarios, filtros){
        //console.log('filtros actualizado: ', filtros.clientes.map(op=>op.seleccionado))
        //console.log('inventarios: ', inventarios.map(local=>local.cliente.nombreCorto))

        // por cliente: cumple el criterio si la opcion con su nombre esta seleccionada
        let inventariosFiltrados = inventarios.filter(inventario=>{
            let textoBuscado = inventario.cliente.nombreCorto || ''  // si es undefined, es tratado como ''
            return filtros.clientes.find( opcion=>(opcion.texto===textoBuscado && opcion.seleccionado===true) )
        })
        // por regiones
        inventariosFiltrados = inventariosFiltrados.filter(inventario=>{
            let textoBuscado = inventario.region.nombreCorto || ''  // si es undefined, es tratado como ''
            return filtros.regiones.find( opcion=>(opcion.texto===textoBuscado && opcion.seleccionado===true) )
        })
        return inventariosFiltrados
    }

    // Reemplazar el filtro que es actualizado por la Cabecera
    reemplazarFiltro(nombreFiltro, filtroActualizado) {
        // se reemplaza el filtro indicado en 'nombreFiltro'
        let nuevoFiltro = this.state.filtro
        nuevoFiltro[nombreFiltro] = filtroActualizado

        this.setState({
            filtro: nuevoFiltro,
            inventariosFiltrados: this.filtrarInventarios(this.state.inventarios, nuevoFiltro)
        })
    }

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
                                <Cabecera nombre="Cliente"
                                          filtro={this.state.filtro.clientes}
                                          onFiltroChanged={this.reemplazarFiltro.bind(this, 'clientes')}/>
                            </th>
                            <th className={styles.thCeco}>Ceco</th>
                            <th className={styles.thLocal}>Local</th>
                            <th className={styles.thZonaSei}>Zona SEI</th>
                            <th className={styles.thRegion}>
                                <Cabecera nombre="Región"
                                          filtro={this.state.filtro.regiones}
                                          onFiltroChanged={this.reemplazarFiltro.bind(this, 'regiones')}/>
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
                    {this.state.inventariosFiltrados.map((inventario, index)=>{
                        return <RowLocales
                            key={index}
                            index={index}
                            mesProgramado={inventario.mesProgramado}
                            ultimoDiaMes={moment(`${inventario.annoProgramado}${inventario.mesProgramado}`, 'YYYYMM').daysInMonth()}
                            annoProgramado={inventario.annoProgramado}
                            nombreCliente={inventario.cliente? inventario.cliente.nombreCorto : '...'}
                            ceco={inventario.numero? inventario.numero : '...'}
                            nombreLocal={inventario.nombre? inventario.nombre : '...'}
                            zona={inventario.zona.nombre? inventario.zona.nombre : '...'}
                            region={inventario.region.nombreCorto? inventario.region.nombreCorto : '...'}
                            comuna={inventario.comuna.nombre? inventario.comuna.nombre : '...'}
                            stock={inventario.stock? inventario.stock : '...'}
                            dotacionSugerida={98}
                            jornada={inventario.jornada? inventario.jornada.nombre : '(...jornada)'}
                            focusFilaSiguiente={this.focusFilaSiguiente.bind(this)}
                            focusFilaAnterior={this.focusFilaAnterior.bind(this)}
                            guardarOCrear={this.guardarOCrear.bind(this)}
                            ref={ref=>this.inputFecha[index]=ref}
                        />
                    })}
                    </tbody>
                </StickyContainer>
            </div>
        )
    }
}

TablaLocalesMensual.protoTypes = {
    //inventarioesAgregados: PropTypes.array.required
}
export default TablaLocalesMensual