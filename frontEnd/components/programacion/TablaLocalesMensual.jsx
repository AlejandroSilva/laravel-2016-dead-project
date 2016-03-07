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
            locales: [],
            localesFiltrados: [],
            filtro: {
                clientes: [],
                regiones: []
            }
        }
        // referencia a todos las entradas de fecha de los locales a inventariar
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

    obtenerDatosLocal(idLocal){
        api.locales.getVerbose(idLocal)
            .then(this.actualizarLocal.bind(this))
            .catch(error=>console.error(`error al obtener los datos de ${idLocal}`, error))
    }

    agregarLocal(nuevoLocal, mesAnno){
        let [mes, anno] = mesAnno.split('-')
        let localesActualizados = this.state.locales

        let localNoExiste = this.state.locales.find(local=>local.idLocal===nuevoLocal.idLocal)===undefined
        if( localNoExiste ){
            // buscar asincronicamente la informacion completa al servidor
            this.obtenerDatosLocal(nuevoLocal.idLocal)
            nuevoLocal.mesProgramado = mes
            nuevoLocal.annoProgramado = anno

            // modificar la estructura el objeto para que sea mas manejable
            nuevoLocal.cliente = {}
            nuevoLocal.comuna = {}
            nuevoLocal.provincia = {}
            nuevoLocal.region = {}
            nuevoLocal.zona = {}

            // actualizar la lista de locales
            localesActualizados.push(nuevoLocal)

            // actualizar la lista de locales, y el filtro
            let filtroActualizado = this.generarFiltro(localesActualizados)
            let localesFiltrados = this.filtrarLocales(localesActualizados, filtroActualizado)

            this.setState({
                locales: localesActualizados,
                filtro: filtroActualizado,
                localesFiltrados: localesFiltrados
            })
        }
        return localNoExiste
    }
    actualizarLocal(localActualizado){
        let localesActualizados = this.state.locales.map(local=>{
            if(local.idLocal===localActualizado.idLocal) {
                // modificar la estructura el objeto para que sea mas manejable
                localActualizado.comuna = localActualizado.direccion.comuna || {}
                localActualizado.provincia = localActualizado.comuna.provincia || {}
                localActualizado.region = localActualizado.provincia.region || {}
                localActualizado.zona = localActualizado.region.zona || {}

                // mezclar los objetos
                return Object.assign(local, localActualizado)
            }else
                return local
        })
        let filtroActualizado = this.generarFiltro(localesActualizados)
        let localesFiltrados = this.filtrarLocales(localesActualizados, filtroActualizado)

        this.setState({
            // actualizar los datos de la lista con la informacion obtenida por el api
            locales: localesActualizados,
            filtro: filtroActualizado,
            localesFiltrados: localesFiltrados
        })
    }

    generarFiltro(locales){
        // TODO: simplificar este metodo, hay mucho codigo repetido
        const filtrarSoloUnicos = (valor, index, self)=>self.indexOf(valor)===index

        // FILTRO CLIENTES
        // obtener una lista de clientes sin repetir
        const seleccionarNombreCliente = local=>local.cliente.nombreCorto || ''
        let clientesUnicos = locales.map(seleccionarNombreCliente).filter(filtrarSoloUnicos)

        // crear el filtro con los datos del filtro anterior
        let filtroClientes = clientesUnicos.map(textoUnico=>{
            let opcion = this.state.filtro.clientes.find(opc=> opc.texto===textoUnico)

            // si no existe la opcion, se crea y se selecciona por defecto
            return opcion || { texto: textoUnico, seleccionado: true}
        })

        // FILTRO REGIONES
        const seleccionarNombreRegion = local=>local.region.nombreCorto || ''
        let regionesUnicas = locales.map(seleccionarNombreRegion).filter(filtrarSoloUnicos)

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

    filtrarLocales(locales, filtros){
        //console.log('filtros actualizado: ', filtros.clientes.map(op=>op.seleccionado))
        //console.log('locales: ', locales.map(local=>local.cliente.nombreCorto))

        // por cliente: cumple el criterio si la opcion con su nombre esta seleccionada
        let localesFiltrados = locales.filter(local=>{
            let textoBuscado = local.cliente.nombreCorto || ''  // si es undefined, es tratado como ''
            return filtros.clientes.find( opcion=>(opcion.texto===textoBuscado && opcion.seleccionado===true) )
        })
        // por regiones
        localesFiltrados = localesFiltrados.filter(local=>{
            let textoBuscado = local.region.nombreCorto || ''  // si es undefined, es tratado como ''
            return filtros.regiones.find( opcion=>(opcion.texto===textoBuscado && opcion.seleccionado===true) )
        })
        return localesFiltrados
    }

    // Reemplazar el filtro que es actualizado por la Cabecera
    reemplazarFiltro(nombreFiltro, filtroActualizado) {
        // se reemplaza el filtro indicado en 'nombreFiltro'
        let nuevoFiltro = this.state.filtro
        nuevoFiltro[nombreFiltro] = filtroActualizado

        this.setState({
            filtro: nuevoFiltro,
            localesFiltrados: this.filtrarLocales(this.state.locales, nuevoFiltro)
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
                            <th className={styles.thEstado}>Estado</th>
                            <th className={styles.thOpciones}>Opciones</th>
                        </Sticky>
                    </thead>
                    <tbody>
                    {this.state.localesFiltrados.map((local, index)=>{
                        return <RowLocales
                            key={index}
                            index={index}
                            mesProgramado={local.mesProgramado}
                            ultimoDiaMes={moment(`${local.annoProgramado}${local.mesProgramado}`, 'YYYYMM').daysInMonth()}
                            annoProgramado={local.annoProgramado}
                            nombreCliente={local.cliente? local.cliente.nombreCorto : '...'}
                            ceco={local.numero? local.numero : '...'}
                            nombreLocal={local.nombre? local.nombre : '...'}
                            zona={local.zona.nombre? local.zona.nombre : '...'}
                            region={local.region.nombreCorto? local.region.nombreCorto : '...'}
                            comuna={local.comuna.nombre? local.comuna.nombre : '...'}
                            stock={local.stock? local.stock : '...'}
                            dotacionSugerida={98}
                            jornada={local.jornada? local.jornada.nombre : '(...jornada)'}
                            focusFilaSiguiente={this.focusFilaSiguiente.bind(this)}
                            focusFilaAnterior={this.focusFilaAnterior.bind(this)}
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
    //localesAgregados: PropTypes.array.required
}
export default TablaLocalesMensual