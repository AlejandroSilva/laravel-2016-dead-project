import React from 'react'
let PropTypes = React.PropTypes
import api from '../../apiClient/v1'
import moment from 'moment'

// Componentes
import Sticky from '../shared/react-sticky/sticky.js'
import StickyContainer from '../shared/react-sticky/container.js'
import Cabecera from './TableHeader.jsx'
import RowLocales from './RowInventario.jsx'

// Styles
import sharedStyles from '../shared/shared.css'
import styles from './TablaProgramas.css'

class TablaInventarios extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            programas: [],
            programasFiltrados: [],
            filtro: {
                clientes: [],
                regiones: []
            }
        }
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

    guardarOCrear(request){
        let programa = this.state.programas.find(programa=>programa.idLocal===request.idLocal)
        if(!programa){
            return console.error(`inventario ${request.idLocal} no encontrado`)
        }
        // actualizar
        if(programa.idInventario){
            // actualizar el programa
            return new Promise((res, rej)=>{
                api.inventario.actualizar(programa.idInventario, request)
                    .then(programaActualizado=>{
                        console.log('el servidor retorna ', programaActualizado.idJornada)
                        //todo Esto en la practica no va a hacer mucho, porque los cambios ya estan visialente escritos en los formularios
                        this.actualizarInventario(programa)
                        console.log(`programa ${programa.idInventario} actualizado`)
                        res(programa)
                    })
                    .catch(rej)
            })
        }else{
            // Crear el inventario
            return new Promise((res, rej)=>{
                api.inventario.nuevo(request)
                    .then(inventarioCreado=>{
                        console.log(inventarioCreado.idJornada)
                        // buscar el inventario, por el id de local (en teorica nunca deberia estar repetido en esta instancia)

                        inventario.idInventario = inventarioCreado.idInventario
                        this.actualizarInventario(inventario)

                        console.log(`inventario ${inventarioCreado.idInventario} creado correctamente`)
                        res(inventarioCreado)
                    })
                    .catch(rej)
            })
        }
    }

    agregarInventario(nuevoInventario, mesAnno){
        let [mes, anno] = mesAnno.split('-')
        let inventariosActualizados = this.state.programas

        let programaNoAgregado = this.state.programas.find(inventario=>inventario.idLocal===nuevoInventario.idLocal)===undefined
        if( programaNoAgregado ){
            // buscar asincronicamente la informacion completa al servidor

            api.locales.getVerbose(nuevoInventario.idLocal)
                .then(local=>{
                    // modificar la estructura el objeto para que sea mas manejable
                    local.comuna = local.direccion.comuna || {}
                    local.provincia = local.comuna.provincia || {}
                    local.region = local.provincia.region || {}

                    // actualizar los datos del inventario
                    nuevoInventario.local = local
                    nuevoInventario.nombreCliente = local.cliente.nombreCorto
                    nuevoInventario.nombreComuna = local.comuna.nombre
                    nuevoInventario.nombreProvincia = local.provincia.nombre
                    nuevoInventario.nombreRegion = local.region.nombreCorto

                    this.actualizarInventario(nuevoInventario)
                })
                .catch(error=>console.error(`error al obtener los datos de ${nuevoInventario.idLocal}`, error))

            nuevoInventario.mesProgramado = mes
            nuevoInventario.annoProgramado = anno

            // modificar la estructura el objeto para que sea mas manejable
            nuevoInventario.local = {}
            nuevoInventario.nombreCliente = '-'
            nuevoInventario.nombreComuna = '-'
            nuevoInventario.nombreProvincia = '-'
            nuevoInventario.nombreRegion = '-'

            // actualizar la lista de locales
            inventariosActualizados.push(nuevoInventario)

            // actualizar la lista de locales, y el filtro
            let filtroActualizado = this.generarFiltro(inventariosActualizados)
            let programasFiltrados = this.filtrarInventarios(inventariosActualizados, filtroActualizado)

            this.setState({
                inventarios: inventariosActualizados,
                filtro: filtroActualizado,
                programasFiltrados: programasFiltrados
            })
        }
        return programaNoAgregado
    }
    actualizarInventario(inventarioModificado){
        let inventariosActualizados = this.state.programas.map(inventario=>{
            if(inventario.idLocal===inventarioModificado.idLocal) { // Todo idLocal no existe
                return inventarioModificado
            }
            return inventario
        })
        let filtroActualizado = this.generarFiltro(inventariosActualizados)
        let programasFiltrados = this.filtrarInventarios(inventariosActualizados, filtroActualizado)

        this.setState({
            // actualizar los datos de la lista con la informacion obtenida por el api
            programas: inventariosActualizados,
            filtro: filtroActualizado,
            programasFiltrados: programasFiltrados
        })
    }

    generarFiltro(inventarios){
        // TODO: simplificar este metodo, hay mucho codigo repetido
        const filtrarSoloUnicos = (valor, index, self)=>self.indexOf(valor)===index

        // FILTRO CLIENTES
        // obtener una lista de clientes sin repetir
        const seleccionarNombreCliente = inventario=>inventario.nombreCliente || ''
        let clientesUnicos = inventarios.map(seleccionarNombreCliente).filter(filtrarSoloUnicos)

        // crear el filtro con los datos del filtro anterior
        let filtroClientes = clientesUnicos.map(textoUnico=>{
            let opcion = this.state.filtro.clientes.find(opc=> opc.texto===textoUnico)

            // si no existe la opcion, se crea y se selecciona por defecto
            return opcion || { texto: textoUnico, seleccionado: true}
        })

        // FILTRO REGIONES
        const seleccionarNombreRegion = inventario=>inventario.nombreRegion || ''
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
        //console.log('inventarios: ', inventarios.map(local=>local.nombreCliente))

        // por cliente: cumple el criterio si la opcion con su nombre esta seleccionada
        let programasFiltrados = inventarios.filter(inventario=>{
            let textoBuscado = inventario.nombreCliente || ''  // si es undefined, es tratado como ''
            return filtros.clientes.find( opcion=>(opcion.texto===textoBuscado && opcion.seleccionado===true) )
        })
        // por regiones
        programasFiltrados = programasFiltrados.filter(inventario=>{
            let textoBuscado = inventario.nombreRegion || ''  // si es undefined, es tratado como ''
            return filtros.regiones.find( opcion=>(opcion.texto===textoBuscado && opcion.seleccionado===true) )
        })
        return programasFiltrados
    }

    // Reemplazar el filtro que es actualizado por la Cabecera
    reemplazarFiltro(nombreFiltro, filtroActualizado) {
        // se reemplaza el filtro indicado en 'nombreFiltro'
        let nuevoFiltro = this.state.filtro
        nuevoFiltro[nombreFiltro] = filtroActualizado

        this.setState({
            filtro: nuevoFiltro,
            programasFiltrados: this.filtrarInventarios(this.state.programas, nuevoFiltro)
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
                    {this.state.programasFiltrados.map((inventario, index)=>{
                        return <RowLocales
                            key={index}
                            index={index}
                            // a quitar
                            nombreCliente={inventario.nombreCliente}
                            region={inventario.nombreRegion}
                            comuna={inventario.nombreComuna}

                            idInventario={inventario.idInventario}

                            // desde el formulario
                            mesProgramado={inventario.mesProgramado}
                            ultimoDiaMes={moment(`${inventario.annoProgramado}${inventario.mesProgramado}`, 'YYYYMM').daysInMonth()}
                            annoProgramado={inventario.annoProgramado}

                            // Importante
                            local={inventario.local}
                            // Metodos
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

TablaInventarios.protoTypes = {
    //inventarioesAgregados: PropTypes.array.required
}
export default TablaInventarios