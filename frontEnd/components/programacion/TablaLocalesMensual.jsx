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
                clientes: []
            }
        }
        // referencia a todos las entradas de fecha de los locales a inventariar
        this.inputFecha = []
    }
    obtenerDatosLocal(idLocal){
        api.locales.getVerbose(idLocal)
            .then(this.actualizarLocal.bind(this))
            .catch(error=>console.error(`error al obtener los datos de ${idLocal}`, error))
    }

    generarFiltro(locales){
        //console.log('--- generar filtro: ')
        const seleccionarNombreCliente = local=>local.cliente.nombreCorto || ''
        const filtrarSoloUnicos = (valor, index, self)=>self.indexOf(valor)===index

        // obtener una lista de clientes sin repetir
        let clientesUnicos = locales.map(seleccionarNombreCliente).filter(filtrarSoloUnicos)

        // crear el filtro con los datos del filtro anterior, y si no existe el la opcion sea y selecciona por defecto
        let filtroClientes = clientesUnicos.map(clienteUnico=>{
            let opcion = this.state.filtro.clientes.find(cliente=> cliente.texto===clienteUnico)
            if(opcion){
                return opcion
            }else{
                return { texto: clienteUnico, seleccionado: true}
            }
        })

        //console.log('clientes unicos ', clientesUnicos )
        //console.log('filtro.cliente viejo: ', this.state.filtro.clientes.map(fi=>fi.seleccionado))
        //console.log('filtro.cliente nuevo: ', filtroClientes.map(fi=>fi.seleccionado))
        //console.log('--------------------')
        return {
            clientes: filtroClientes
        }
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
            // Todo: actualizar filtro con el nuevo elemento

            this.setState({
                locales: localesActualizados,
                filtro: filtroActualizado,
                localesFiltrados: this.filtrarLocales(localesActualizados, filtroActualizado)
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

        this.setState({
            // actualizar los datos de la lista con la informacion obtenida por el api
            locales: localesActualizados,
            filtro: filtroActualizado,
            localesFiltrados: this.filtrarLocales(localesActualizados, filtroActualizado)
        })
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

    // Aplicar filtros a los elementos
    actualizarFiltro(filtroClientes) {
        let filtroActualizado = {
            clientes: filtroClientes
        }

        this.setState({
            filtro: filtroActualizado,
            localesFiltrados: this.filtrarLocales(this.state.locales, filtroActualizado)
        })
    }
    filtrarLocales(locales, filtros){
        //console.log('filtros actualizado: ', filtros.clientes.map(op=>op.seleccionado))
        //console.log('locales: ', locales.map(local=>local.cliente.nombreCorto))

        // por cliente: cumple el criterio si la opcion con su nombre esta seleccionada
        let filtro = filtros.clientes

        let localesFiltrados = locales.filter(local=>{
            let textoBuscado = local.cliente.nombreCorto || ''  // si es undefined, es tratado como ''
            return filtro.find( opcion=>(opcion.texto===textoBuscado && opcion.seleccionado===true) )
        })
        return localesFiltrados
    }

    render(){
        // agregar los unicos a state (cuando se crean y actualizan), o va a perjudicar el performance
        //let fechasUnicas = ['01-03-2016', '03-03-2016', '08-03-2016', '12-03-2016', '18-03-2016', '23-03-2016', '28-03-2016']
        let filtroClientes = [{texto: 'FCV', seleccionado: true}, {texto: 'PUC', seleccionado: true}]

        // todo: malo , se debe arreglar
        let filtroRegiones = this.state.locales
            .map(local=>({texto: local.region.nombreCorto, seleccionado: true}))
            .filter((opcion, index, self)=>self.indexOf(opcion.texto)===index)

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
                                          onFiltroChanged={this.actualizarFiltro.bind(this)}/>
                            </th>
                            <th className={styles.thCeco}>Ceco</th>
                            <th className={styles.thLocal}>Local</th>
                            <th className={styles.thZonaSei}>Zona SEI</th>
                            <th className={styles.thRegion}>
                                <Cabecera nombre="Región"
                                          filtro={filtroRegiones}
                                          onFiltroChanged={this.actualizarFiltro.bind(this)}/>
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