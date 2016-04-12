// Libs
import React from 'react'
import moment from 'moment'
moment.locale('es')
import api from '../../apiClient/v1'
import BlackBoxMensualAI from './BlackBoxMensualAI.js'

// Component
//import Multiselect from 'react-widgets/lib/Multiselect'
import { Tab, Tabs, TabList, TabPanel } from 'react-tabs'
import TablaMensualAI from './TablaMensualAI.jsx'
import AgregarAuditoria from './AgregarAuditoria.jsx'

class ProgramacionMensualAI extends React.Component{
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
            auditoriasFiltradas: [],
            filtroClientes: [],
            filtroRegiones: [],
            meses
        }
        // MAGIA NEGRA!!
        this.blackbox = new BlackBoxMensualAI(this.props.clientes)
    }

    onSeleccionarMes(annoMesDia){
        console.log('mes seleccionado ', annoMesDia)
        this.setState({
            mesSeleccionado: annoMesDia
        })
        // obtener todos los inventarios realizados en el mes seleccionado

        api.auditoria.getPorMes(annoMesDia)
            .then(auditorias=>{
                this.blackbox.reset()
                auditorias.forEach(auditoria=>{
                    // crear un dummy
                    let auditoriaDummy = this.blackbox.__crearDummy(annoMesDia, auditoria.idLocal )
                    this.blackbox.addFinal(auditoriaDummy)

                    // actualizar los datos del auditoria
                    this.blackbox.actualizarDatosAuditoria(auditoriaDummy.idDummy, auditoria)
                })
                // actualizar los filtros, y la lista ordenada de locales
                this.blackbox.ordenarLista()
                this.setState(this.blackbox.getListaFiltrada())
            })
            //.catch(err=>console.error('error: ', err))
    }

    crearGrupoInventarios(nuevasAuditorias){
        let promesasFetch = []
        nuevasAuditorias.forEach(nuevaAuditoria=> {
            // crear todos los locales (y sus nominas)
            promesasFetch.push(
                api.auditoria.nuevo({
                        idLocal: nuevaAuditoria.idLocal,
                        fechaProgramada: this.state.mesSeleccionado
                    })
                    .then(auditoriaCreada=>{
                        this.blackbox.actualizarDatosAuditoria(nuevaAuditoria.idDummy, auditoriaCreada)
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

    agregarAuditoria(idCliente, numeroLocal, annoMesDia){
        let [errores, auditoriaDummy] = this.blackbox.crearDummy(idCliente, numeroLocal, annoMesDia)
        if(errores)
            return [errores, {}]

        // agregar al listado
        this.blackbox.addNuevo(auditoriaDummy)

        // actualizar la vista de la lista
        // actualizar los filtros, y la lista ordenada de locales
        this.setState(this.blackbox.getListaFiltrada())

        // cuando se agregar un inventario, se crea automaticamente (junto a su nomina)
        api.auditoria.nuevo({
            idLocal: auditoriaDummy.local.idLocal,
            fechaProgramada: this.state.mesSeleccionado
        })
            .then(auditoriaCreada=>{
                this.blackbox.actualizarDatosAuditoria(auditoriaDummy.idDummy, auditoriaCreada)
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

    actualizarAuditoria(idAuditoria, formAuditoria, idDummy){
        // Actualizar los datos del inventario
        api.auditoria.actualizar(idAuditoria, formAuditoria)
            .then(auditoriaActualizada=>{
                console.log('inventario actualizado correctamente')
                // actualizar los datos y el state de la app
                this.blackbox.actualizarDatosAuditoria(idDummy, auditoriaActualizada)
                // actualizar los filtros, y la lista ordenada de locales
                this.setState(this.blackbox.getListaFiltrada())
            })
    }

    quitarInventario(idDummy){
        this.blackbox.remove(idDummy)
        // actualizar los filtros, y la lista ordenada de locales
        this.setState(this.blackbox.getListaFiltrada())
    }

    ordenarAuditorias(){
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
                <h1>Programación mensual AI</h1>

                <AgregarAuditoria
                    clientes={this.props.clientes}
                    meses={this.state.meses}
                    agregarAuditoria={this.agregarAuditoria.bind(this)}
                    agregarGrupoInventarios={this.agregarGrupoInventarios.bind(this)}
                    onSeleccionarMes={this.onSeleccionarMes.bind(this)}
                    puedeAgregar={this.props.puedeAgregar}
                />

                <div className="row">
                    <h4 className="page-header" style={{marginTop: '1em'}}>
                        {/*<a className="btn btn-success btn-xs pull-right"
                            href={`/programacionAI/mensual/pdf/${this.state.mesSeleccionado}`}
                        >Exportar</a>*/}
                    </h4>
                    <TablaMensualAI
                        puedeModificar={this.props.puedeModificar}
                        auditoriasFiltradas={this.state.auditoriasFiltradas}
                        auditores={this.props.auditores}
                        filtroClientes={this.state.filtroClientes}
                        filtroRegiones={this.state.filtroRegiones}
                        actualizarFiltro={this.actualizarFiltro.bind(this)}
                        actualizarAuditoria={this.actualizarAuditoria.bind(this)}
                        quitarInventario={this.quitarInventario.bind(this)}
                        ordenarAuditorias={this.ordenarAuditorias.bind(this)}
                        //ref={ref=>this.TablaInventarios=ref}
                    />
                </div>
            </div>
        )
    }
}

ProgramacionMensualAI.propTypes = {
    puedeModificar: React.PropTypes.bool.isRequired,
    puedeAgregar: React.PropTypes.bool.isRequired,
    clientes: React.PropTypes.array.isRequired,
    auditores: React.PropTypes.array.isRequired
}

export default ProgramacionMensualAI