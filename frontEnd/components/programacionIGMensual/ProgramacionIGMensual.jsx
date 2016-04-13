// Libs
import React from 'react'
import moment from 'moment'
moment.locale('es')
import api from '../../apiClient/v1'
import BlackBoxMensual from './BlackBoxMensual.js'

// Component
//import Multiselect from 'react-widgets/lib/Multiselect'
import { Tab, Tabs, TabList, TabPanel } from 'react-tabs'
import TablaMensual from './TablaMensual.jsx'
import AgregarPrograma from './AgregarPrograma.jsx'

class ProgramacionIGMensual extends React.Component{
    constructor(props) {
        super(props)
        // mostrar en el selector, los proximos 12 meses
        let meses = []
        for (let desface = 0; desface < 12; desface++) {
            let mes = moment().add(desface, 'month')
            meses.push({
                valor: mes.format('YYYY-MM-00'),
                texto: mes.format('MMMM  YYYY')
            })
        }
        this.state = {
            mesSeleccionado: '2016-01-00',
            inventariosFiltrados: [],
            filtroClientes: [],
            filtroRegiones: [],
            meses
        }
        // MAGIA NEGRA!!
        this.blackbox = new BlackBoxMensual(this.props.clientes)
    }

    onSeleccionarMes(annoMesDia){
        console.log('mes seleccionado ', annoMesDia)
        this.setState({
            mesSeleccionado: annoMesDia
        })
        // obtener todos los inventarios realizados en el mes seleccionado

        api.inventario.getPorMes(annoMesDia)
            .then(inventarios=>{
                this.blackbox.reset()
                inventarios.forEach(inventario=>{
                    // crear un dummy
                    let nuevoInventario = this.blackbox.__crearDummy(annoMesDia, inventario.idLocal )
                    this.blackbox.addFinal(nuevoInventario)

                    // actualizar los datos del inventario
                    this.blackbox.actualizarDatosInventario(nuevoInventario, inventario)
                })
                // actualizar los filtros, y la lista ordenada de locales
                this.blackbox.ordenarLista()
                this.setState(this.blackbox.getListaFiltrada())
            })
            //.catch(err=>console.error('error: ', err))
    }

    crearGrupoInventarios(nuevosInventarios){
        let promesasFetch = []
        nuevosInventarios.forEach(nuevoInventario=> {
            // crear todos los locales (y sus nominas)
            promesasFetch.push(
                api.inventario.nuevo({
                        idLocal: nuevoInventario.idLocal,
                        fechaProgramada: this.state.mesSeleccionado
                    })
                    .then(inventarioCreado=>{
                        this.blackbox.actualizarDatosInventario({
                            idDummy: nuevoInventario.idDummy
                        }, inventarioCreado)
                        // actualizar los datos y el state de la app
                        // actualizar los filtros, y la lista ordenada de locales
                        // this.setState(this.blackbox.getListaFiltradaSinOrdenar())
                        this.setState(this.blackbox.getListaFiltrada())
                    })
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
        this.blackbox.addNuevo(nuevoInventario)

        // actualizar la vista de la lista
        // actualizar los filtros, y la lista ordenada de locales
        this.setState(this.blackbox.getListaFiltrada())

        // cuando se agregar un inventario, se crea automaticamente (junto a su nomina)
        api.inventario.nuevo({
            idLocal: nuevoInventario.local.idLocal,
            fechaProgramada: this.state.mesSeleccionado
        })
            .then(inventarioCreado=>{
                this.blackbox.actualizarDatosInventario({
                    idDummy: nuevoInventario.idDummy
                }, inventarioCreado)
                // actualizar los datos y el state de la app
                // actualizar los filtros, y la lista ordenada de locales
                // this.setState(this.blackbox.getListaFiltradaSinOrdenar())
                this.setState(this.blackbox.getListaFiltrada())
            })
        return [null, {}]
    }

    // Lista de inventarios que son "pegados desde excel"
    agregarGrupoInventarios(idCliente, numerosLocales, annoMesDia){
        console.log(numerosLocales)
        let pegadoConProblemas = []
        // se evalua y agrega cada uno de los elementos
        let nuevosInventarios = []
        numerosLocales.forEach(numero=> {
            let [errores, nuevoInventario] = this.blackbox.crearDummy(idCliente, numero, annoMesDia)
            if (errores){
                pegadoConProblemas.push(errores)
            }else{
                // this.blackbox.addNuevo(nuevoInventario)
                nuevosInventarios.push(nuevoInventario)
            }
        })
        // agregar el grupo completo de una vez (para hacer un poco mas rapido el proceso)
        this.blackbox.addNuevos(nuevosInventarios)

        // cuando terminen todos, se actualiza el state de la aplicacion
        // actualizar los filtros, y la lista ordenada de locales
        this.setState(this.blackbox.getListaFiltrada())
        this.crearGrupoInventarios(nuevosInventarios)

        return {
            pegadoConProblemas: pegadoConProblemas,
            conteoTotal: numerosLocales.length,
            conteoCorrectos: numerosLocales.length - pegadoConProblemas.length,
            conteoProblemas: pegadoConProblemas.length
        }
    }

    guardarOCrearInventario(formInventario){
        // Nota: agregarInventario() y fetchLocales() siempre crean el inventario y sus nominas, por lo que el metodo
        // api.inventario.nuevo() de abajo nunca deberia ser llamado.

        if(formInventario.idInventario){
            // Actualizar los datos del inventario
            api.inventario.actualizar(formInventario.idInventario, {
                    idInventario: formInventario.idInventario,
                    fechaProgramada: formInventario.fechaProgramada,
                    dotacionAsignadaTotal: formInventario.dotacionAsignadaTotal,
                    idJornada: formInventario.idJornada,
                })
                .then(inventarioActualizado=>{
                    console.log('inventario actualizado correctamente')
                    // actualizar los datos y el state de la app
                    this.blackbox.actualizarDatosInventario(formInventario, inventarioActualizado)
                    // actualizar los filtros, y la lista ordenada de locales
                    this.setState(this.blackbox.getListaFiltrada())
                })
        }else{
            // Crear los datos del inventario en el servidor (quitar todos los campos que no interesan)
            api.inventario.nuevo({
                idLocal: formInventario.idLocal,
                //idJornada: formInventario.idJornada,      // deja que tome la jornada por defecto
                fechaProgramada: formInventario.fechaProgramada
            })
                .then(inventarioCreado=>{
                    this.blackbox.actualizarDatosInventario(formInventario, inventarioCreado)
                    // actualizar los datos y el state de la app
                    // actualizar los filtros, y la lista ordenada de locales
                    // this.setState(this.blackbox.getListaFiltradaSinOrdenar())
                    this.setState(this.blackbox.getListaFiltrada())
                })
        }
    }

    quitarInventario(idDummy){
        this.blackbox.remove(idDummy)
        // actualizar los filtros, y la lista ordenada de locales
        this.setState(this.blackbox.getListaFiltrada())
    }

    eliminarInventario(inventario){
        api.inventario.eliminar(inventario.idInventario)
            .then(resp=>{
                console.log(resp)
                this.blackbox.remove(inventario.idDummy)
                // actualizar los filtros, y la lista ordenada de locales
                this.setState(this.blackbox.getListaFiltrada())
            })
            .error(resp=>console.error)
    }

    ordenarInventarios(){
        this.blackbox.ordenarLista()
        this.setState( this.blackbox.getListaFiltrada() )
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
                <h1>Programación mensual IG</h1>

                <AgregarPrograma
                    puedeAgregar={this.props.puedeAgregar}
                    clientes={this.props.clientes}
                    meses={this.state.meses}
                    agregarInventario={this.agregarInventario.bind(this)}
                    agregarGrupoInventarios={this.agregarGrupoInventarios.bind(this)}
                    onSeleccionarMes={this.onSeleccionarMes.bind(this)}
                />

                <div className="row">
                    <h4 className="page-header" style={{marginTop: '1em'}}>
                        Locales a programar:
                        <a className="btn btn-success btn-xs pull-right"
                            href={`/programacionIG/mensual/pdf/${this.state.mesSeleccionado}`}
                        >Exportar</a>
                    </h4>
                    <TablaMensual
                        puedeModificar={this.props.puedeModificar}
                        inventariosFiltrados={this.state.inventariosFiltrados}
                        filtroClientes={this.state.filtroClientes}
                        filtroRegiones={this.state.filtroRegiones}
                        actualizarFiltro={this.actualizarFiltro.bind(this)}
                        guardarOCrearInventario={this.guardarOCrearInventario.bind(this)}
                        quitarInventario={this.quitarInventario.bind(this)}
                        eliminarInventario={this.eliminarInventario.bind(this)}
                        ordenarInventarios={this.ordenarInventarios.bind(this)}
                        //ref={ref=>this.TablaInventarios=ref}
                    />
                </div>
            </div>
        )
    }
}

ProgramacionIGMensual.propTypes = {
    puedeModificar: React.PropTypes.bool.isRequired,
    puedeAgregar: React.PropTypes.bool.isRequired,
    clientes: React.PropTypes.array.isRequired
}

export default ProgramacionIGMensual