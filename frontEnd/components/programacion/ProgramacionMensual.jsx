// Libs
import React from 'react'
import moment from 'moment'
moment.locale('es')
import api from '../../apiClient/v1'
import BlackBox from './BlackBox.js'

// Component
//import Multiselect from 'react-widgets/lib/Multiselect'
import { Tab, Tabs, TabList, TabPanel } from 'react-tabs'
import TablaProgramas from './TablaProgramas.jsx'
import AgregarPrograma from './AgregarPrograma.jsx'

class ProgramacionMensual extends React.Component{
    constructor(props) {
        super(props)
        let meses = []
        // mostrar en el selector, los proximos 12 meses
        for (let desface = 0; desface < 12; desface++) {
            let mes = moment().add(desface, 'month')
            meses.push({
                valor: mes.format('YYYY-MM-00'),
                texto: mes.format('MMMM  YYYY')
            })
        }
        this.state = {
            inventariosFiltrados: [],
            filtroClientes: [],
            filtroRegiones: [],
            meses
        }
        // MAGIA NEGRA!!
        this.blackbox = new BlackBox(this.props.clientes)
    }

    onSeleccionarMes(annoMesDia){
        console.log('mes seleccionado ', annoMesDia)
        // obtener todos los inventarios realizados en el mes seleccionado

        api.inventario.getPorMes(annoMesDia)
            .then(inventarios=>{
                this.blackbox.reset()
                let idLocalesToFetch = []
                inventarios.forEach(inventario=>{
                    // crear un dummy
                    let nuevoInventario = this.blackbox.__crearDummy(annoMesDia, inventario.idLocal )
                    this.blackbox.add(nuevoInventario)

                    // actualizar los datos del inventario
                    this.blackbox.actualizarDatosInventario(nuevoInventario, inventario)

                    // los locales a los que se hara un fetch de los datos
                    idLocalesToFetch.push(inventario.idLocal)
                })
                // actualizar los filtros, y la lista ordenada de locales
                this.setState(this.blackbox.getListaFiltrada())

                // pedir los datos del los locales de forma asincrona
                this.fetchLocales(idLocalesToFetch)
            })
            //.catch(err=>console.error('error: ', err))

    }
    fetchLocales(idLocales){
        let promesasFetch = []
        idLocales.forEach(idLocal=> {
            // pedir los datos de los locales
            promesasFetch.push(
                api.locales.getVerbose(idLocal)
                    .then(local=>this.blackbox.actualizarDatosLocal(local))
                    .catch(error=>console.error('error con :', error))
            )
        })
        // en algun momento las promesas se van a cumplior, entonces actualizar el estado
        Promise.all(promesasFetch)
            .then(locales=>{
                console.log('fetch de todos los locales correcto')
                // actualizar los filtros, y la lista ordenada de locales
                this.setState(this.blackbox.getListaFiltrada())
            })
            .catch(datos=> {
                // Todo: agregar bluebird para que esto no ocurra nunca
                // todo, al fallar UNA promesa, no se cumple el resto
                alert('error al buscar la información de los locales, (AgregarGrupoInventarios desde Excel: fetch de todos los locales correcto)')
                // actualizar los filtros, y la lista ordenada de locales
                this.setState(this.blackbox.getListaFiltrada())
            })
    }
    agregarInventario(idCliente, numeroLocal, annoMesDia){
        let [errores, nuevoInventario] = this.blackbox.crearDummy(idCliente, numeroLocal, annoMesDia)
        if(errores)
            return [errores, {}]

        // agregar al listado
        this.blackbox.add(nuevoInventario)

        // actualizar la vista de la lista
        // actualizar los filtros, y la lista ordenada de locales
        this.setState(this.blackbox.getListaFiltrada())

        // fetch de todos los datos, y actualizacion de la lista
        api.locales.getVerbose(nuevoInventario.local.idLocal)
            .then(local=>{
                this.blackbox.actualizarDatosLocal(local)

                // actualizar los filtros, y la lista ordenada de locales
                this.setState(this.blackbox.getListaFiltrada())
            })
            .catch(error=>{
                console.error(`error al obtener los datos de ${nuevoInventario.local.idLocal}`, error)
                alert(`error al obtener los datos de ${nuevoInventario.local.idLocal}`)
            })

        return [null, {}]
    }

    agregarGrupoInventarios(idCliente, numerosLocales, annoMesDia){
        console.log(numerosLocales)
        let idLocalesExistentes = []
        let pegadoConProblemas = []
        // se evalua y agrega cada uno de los elementos
        numerosLocales.forEach(numero=> {
            let [errores, nuevoInventario] = this.blackbox.crearDummy(idCliente, numero, annoMesDia)
            if (errores){
                pegadoConProblemas.push(errores)
            }else{
                idLocalesExistentes.push(nuevoInventario.idLocal)
                this.blackbox.add(nuevoInventario)
            }
        })

        // cuando terminen todos, se actualiza el state de la aplicacion
        // actualizar los filtros, y la lista ordenada de locales
        this.setState(this.blackbox.getListaFiltrada())
        this.fetchLocales(idLocalesExistentes)

        return {
            pegadoConProblemas: pegadoConProblemas,
            conteoTotal: numerosLocales.length,
            conteoCorrectos: numerosLocales.length - pegadoConProblemas.length,
            conteoProblemas: pegadoConProblemas.length
        }
    }

    guardarOCrearInventario(formInventario){
        if(formInventario.idInventario){
            // Actualizar los datos del inventario
            api.inventario.actualizar(formInventario.idInventario, formInventario)
                .then(inventarioActualizado=>{
                    console.log('inventario actualizado correctamente')
                    // actualizar los datos y el state de la app
                    this.blackbox.actualizarDatosInventario(formInventario, inventarioActualizado)
                    // actualizar los filtros, y la lista ordenada de locales
                    this.setState(this.blackbox.getListaFiltrada())
                })
        }else{
            // Crear los datos del inventario en el servidor
            api.inventario.nuevo(formInventario)
                .then(inventarioCreado=>{
                    this.blackbox.actualizarDatosInventario(formInventario, inventarioCreado)
                    // actualizar los datos y el state de la app
                    // actualizar los filtros, y la lista ordenada de locales
                    this.setState(this.blackbox.getListaFiltrada())
                })
        }
    }

    quitarInventario(idDummy){
        this.blackbox.remove(idDummy)
        // actualizar los filtros, y la lista ordenada de locales
        this.setState(this.blackbox.getListaFiltrada())
    }

    actualizarFiltro(nombreFiltro, filtro){
        if(nombreFiltro==='cliente'){
            this.blackbox.reemplazarFiltroClientes(filtro)

            // actualizar los filtros, y la lista ordenada de locales
            this.setState(this.blackbox.getListaFiltrada())
        }
        if(nombreFiltro==='region'){
            this.blackbox.reemplazarFiltroRegiones(filtro)

            // actualizar los filtros, y la lista ordenada de locales
            this.setState(this.blackbox.getListaFiltrada())
        }

    }

    render(){
        return (
            <div>
                <h1>Programación mensual</h1>

                <AgregarPrograma
                    clientes={this.props.clientes}
                    meses={this.state.meses}
                    agregarInventario={this.agregarInventario.bind(this)}
                    agregarGrupoInventarios={this.agregarGrupoInventarios.bind(this)}
                    onSeleccionarMes={this.onSeleccionarMes.bind(this)}
                />

                <div className="row">
                    <h4 className="page-header" style={{marginTop: '1em'}}>Locales a programar:</h4>
                    <TablaProgramas
                        inventariosFiltrados={this.state.inventariosFiltrados}
                        filtroClientes={this.state.filtroClientes}
                        filtroRegiones={this.state.filtroRegiones}
                        actualizarFiltro={this.actualizarFiltro.bind(this)}
                        guardarOCrearInventario={this.guardarOCrearInventario.bind(this)}
                        quitarInventario={this.quitarInventario.bind(this)}
                        //ref={ref=>this.TablaInventarios=ref}
                    />
                </div>
            </div>
        )
    }
}

ProgramacionMensual.protoTypes = {
    clientes: React.PropTypes.array.isRequired
}

export default ProgramacionMensual