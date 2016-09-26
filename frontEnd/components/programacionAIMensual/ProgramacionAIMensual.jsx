// Libs
import React from 'react'
import moment from 'moment'
moment.locale('es')
import api from '../../apiClient/v1'
import BlackBoxMensualAI from './BlackBoxMensualAI.js'

// Component
import { FormularioAgregarAuditoria } from './FormularioAgregarAuditoria.jsx'
import RowAuditoriaMensual from './RowAuditoriaMensual.jsx'
import TablaMensualAI from './TablaMensualAI.jsx'

class ProgramacionAIMensual extends React.Component{
    constructor(props) {
        super(props)
        this.blackbox = new BlackBoxMensualAI(this.props.clientes)

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
            auditoriasFiltradas: [],
            filtros: {},
            meses
        }

        // referencia a todos las entradas de fecha de los inventarios
        this.rows = []
    }

    componentWillReceiveProps(nextProps){
        // cuando se reciben nuevos elementos, se generand posiciones "vacias" en el arreglo de rows
        this.rows = this.rows.filter(input=>input!==null)
    }

    focusRow(index, nombreElemento){
        let ultimoIndex = this.rows.length-1
        if(index<0){
            // al seleccionar "antes de la primera", se seleciona el ultimo
            this.rows[ultimoIndex].focusElemento(nombreElemento)
        }else if(index>ultimoIndex){
            // al seleccionar "despues de la ultima", se selecciona el primero
            this.rows[ index%this.rows.length ].focusElemento(nombreElemento)
        }else{
            // no es ni el ultimo, ni el primero
            this.rows[index].focusElemento(nombreElemento)
        }
    }

    crearGrupoInventarios(nuevasAuditorias){
        let promesasFetch = []
        nuevasAuditorias.forEach(nuevaAuditoria=> {
            // crear todos los locales (y sus nominas)
            promesasFetch.push(
                api.auditoria.nuevo({
                        idLocal: nuevaAuditoria.local_idLocal,
                        fechaProgramada: nuevaAuditoria.aud_fechaProgramada || this.refFormularioAgregar.getOpcionesSeleccionadas().mes,
                        idAuditor: nuevaAuditoria.aud_idAuditor
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
            idLocal: auditoriaDummy.local_idLocal,
            fechaProgramada: this.refFormularioAgregar.getOpcionesSeleccionadas().mes
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
    agregarGrupoInventarios(datosAuditorias){
        let pegadoConProblemas = []
        // se evalua y agrega cada uno de los elementos
        let nuevosInventarios = []
        datosAuditorias.forEach(datos=> {
            let [errores, nuevoInventario] = this.blackbox.crearDummy(datos.idCliente, datos.ceco, datos.fecha, datos.idAuditor)
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
            conteoTotal: datosAuditorias.length,
            conteoCorrectos: datosAuditorias.length - pegadoConProblemas.length,
            conteoProblemas: pegadoConProblemas.length
        }
    }

    // Llamadas al API
    buscarAuditorias(annoMesDia, idCliente){
        // obtener todos los inventarios realizados en el mes seleccionado
        api.auditoria.getPorMesYCliente(annoMesDia, idCliente)
            .then(auditorias=>{
                this.blackbox.reset()
                auditorias.forEach(auditoria=>{
                    // crear un dummy
                    let auditoriaDummy = this.blackbox.__crearDummy(annoMesDia, auditoria.local_idLocal)
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
    quitarAuditoria(idDummy){
        this.blackbox.remove(idDummy)
        // actualizar los filtros, y la lista ordenada de locales
        this.setState(this.blackbox.getListaFiltrada())
    }
    eliminarAuditoria(auditoria){
        console.log('eliminando ', auditoria)
        api.auditoria.eliminar(auditoria.aud_idAuditoria)
            .then(resp=>{
                this.blackbox.remove(auditoria.idDummy)
                // actualizar los filtros, y la lista ordenada de locales
                this.setState(this.blackbox.getListaFiltrada())
            })
            .catch(resp=>console.error)
    }

    // Filtros
    actualizarFiltro(nombreFiltro, filtro){
        this.blackbox.reemplazarFiltro(nombreFiltro, filtro)
        // actualizar los filtros, y la lista ordenada de locales
        this.setState(this.blackbox.getListaFiltrada())
    }
    ordenarAuditorias(evt){
        evt.preventDefault()
        this.blackbox.ordenarLista()
        this.setState( this.blackbox.getListaFiltrada() )
    }

    render(){
        // refFormularioAgregar no esta definido antes del render
        let opcionesSeleccionadas = this.refFormularioAgregar? this.refFormularioAgregar.getOpcionesSeleccionadas() : {}
        return (
            <div>
                <h1>Programación mensual AI</h1>

                <FormularioAgregarAuditoria
                    ref={ref=>this.refFormularioAgregar=ref}
                    clientes={this.props.clientes}
                    meses={this.state.meses}
                    agregarAuditoria={this.agregarAuditoria.bind(this)}
                    agregarGrupoInventarios={this.agregarGrupoInventarios.bind(this)}
                    buscarAuditorias={this.buscarAuditorias.bind(this)}
                    puedeAgregar={this.props.puedeAgregar}
                />

                <div className="row">
                    <a className="btn btn-success btn-xs pull-right"
                        href={`/pdf/auditorias/${opcionesSeleccionadas.mes}/cliente/${opcionesSeleccionadas.idCliente}`}
                    >Exportar a Excel</a>
                    <TablaMensualAI
                        filtros={this.state.filtros}
                        actualizarFiltro={this.actualizarFiltro.bind(this)}
                        ordenarAuditorias={this.ordenarAuditorias.bind(this)}>

                        {this.state.auditoriasFiltradas.length===0
                            ? <tr><td colSpan="13" style={{textAlign: 'center'}}><b>No hay auditorias para mostrar en este periodo.</b></td></tr>
                            : this.state.auditoriasFiltradas.map((auditoria, index)=>{
                                let mostrarSeparador = false
                                let sgteAuditoria = this.state.auditoriasFiltradas[index+1]
                                if(sgteAuditoria)
                                    mostrarSeparador = auditoria.aud_fechaProgramada!==sgteAuditoria.aud_fechaProgramada
                                return <RowAuditoriaMensual
                                    // Propiedades
                                    puedeModificar={this.props.puedeModificar}
                                    key={auditoria.aud_idAuditoria}
                                    index={index}
                                    ref={ref=>this.rows[index]=ref}
                                    mostrarSeparador={mostrarSeparador}
                                    auditoria={auditoria}
                                    auditores={this.props.auditores}
                                    // Metodos
                                    actualizarAuditoria={this.actualizarAuditoria.bind(this)}
                                    quitarAuditoria={this.quitarAuditoria.bind(this)}
                                    eliminarAuditoria={this.eliminarAuditoria.bind(this)}
                                    focusRow={this.focusRow.bind(this)}
                                    //guardarOCrear={this.guardarOCrear.bind(this)}
                                />
                            })
                        }
                    </TablaMensualAI>
                </div>
            </div>
        )
    }
}

ProgramacionAIMensual.propTypes = {
    puedeModificar: React.PropTypes.bool.isRequired,
    puedeAgregar: React.PropTypes.bool.isRequired,
    clientes: React.PropTypes.array.isRequired,
    auditores: React.PropTypes.array.isRequired
}

export default ProgramacionAIMensual