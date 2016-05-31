// Libs
import React from 'react'
import moment from 'moment'
moment.locale('es')
import api from '../../apiClient/v1'
import BlackBoxMensual from './BlackBoxMensual.js'

// Component
import TablaMensual from './TablaMensual.jsx'
import AgregarInventario from './AgregarInventario.jsx'
import RowInventarioMensual from './RowInventarioMensual.jsx'

class ProgramacionIGMensual extends React.Component{
    constructor(props) {
        super(props)
        this.blackbox = new BlackBoxMensual(this.props.clientes)

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
            meses,
            inventariosFiltrados: [],
            filtros: {}
        }

        // referencia a todos las entradas de fecha de los inventarios
        this.rows = []
    }

    componentWillReceiveProps(){
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

    // #### Agregar Inventarios 
    crearGrupoInventarios(nuevosInventarios){
        let promesasFetch = []
        nuevosInventarios.forEach(nuevoInventario=> {
            // crear todos los locales (y sus nominas)

            promesasFetch.push(
                api.inventario.nuevo({
                        idLocal: nuevoInventario.idLocal,
                        fechaProgramada: this.refFormularioAgregar.getOpcionesSeleccionadas().mes
                    })
                    .then(inventarioCreado=>{
                        this.blackbox.actualizarDatosInventario(nuevoInventario.idDummy, inventarioCreado)
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
            fechaProgramada: this.refFormularioAgregar.getOpcionesSeleccionadas().mes
        })
            .then(inventarioCreado=>{
                this.blackbox.actualizarDatosInventario(nuevoInventario.idDummy, inventarioCreado)
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
    
    // #### Filtros
    ordenarInventarios(evt){
        evt.preventDefault()
        this.blackbox.ordenarLista()
        this.setState( this.blackbox.getListaFiltrada() )
    }
    actualizarFiltro(nombreFiltro, filtro){
        this.blackbox.reemplazarFiltro(nombreFiltro, filtro)
        // actualizar los filtros, y la lista ordenada de locales
        this.setState(this.blackbox.getListaFiltrada())
    }
    
    // #### Llamadas al API
    buscarInventarios(annoMesDia, idCliente){
        api.inventario.getPorMesYCliente(annoMesDia, idCliente)
            .then(inventarios=>{
                this.blackbox.reset()
                inventarios.forEach(inventario=>{
                    // crear un dummy
                    let nuevoInventario = this.blackbox.__crearDummy(annoMesDia, inventario.idLocal )
                    this.blackbox.addFinal(nuevoInventario)

                    // actualizar los datos del inventario
                    this.blackbox.actualizarDatosInventario(nuevoInventario.idDummy, inventario)
                })
                // actualizar los filtros, y la lista ordenada de locales
                this.blackbox.ordenarLista()
                this.setState(this.blackbox.getListaFiltrada())
            })
        //.catch(err=>console.error('error: ', err))
    }
    actualizarInventario(idInventario, formInventario, idDummy){
        // Actualizar los datos del inventario
        api.inventario.actualizar(idInventario, formInventario)
            .then(inventarioActualizado=>{
                console.log('inventario actualizado correctamente')
                // actualizar los datos y el state de la app
                this.blackbox.actualizarDatosInventario(idDummy, inventarioActualizado)
                // actualizar los filtros, y la lista ordenada de locales
                this.setState(this.blackbox.getListaFiltrada())
            })
    }
    quitarInventario(idDummy){
        this.blackbox.remove(idDummy)
        // actualizar los filtros, y la lista ordenada de locales
        this.setState(this.blackbox.getListaFiltrada())
    }
    eliminarInventario(inventario){
        api.inventario.eliminar(inventario.idInventario)
            .then(resp=>{
                this.blackbox.remove(inventario.idDummy)
                // actualizar los filtros, y la lista ordenada de locales
                this.setState(this.blackbox.getListaFiltrada())
            })
            .catch(resp=>console.error)
    }

    render(){
        // refFormularioAgregar no esta definido antes del render
        let opcionesSeleccionadas = this.refFormularioAgregar? this.refFormularioAgregar.getOpcionesSeleccionadas() : {}

        return (
            <div>
                <h1>Programación mensual IG</h1>

                <AgregarInventario
                    ref={ref=>this.refFormularioAgregar=ref}
                    puedeAgregar={this.props.puedeAgregar}
                    clientes={this.props.clientes}
                    meses={this.state.meses}
                    agregarInventario={this.agregarInventario.bind(this)}
                    agregarGrupoInventarios={this.agregarGrupoInventarios.bind(this)}
                    buscarAuditorias={this.buscarInventarios.bind(this)}
                />

                <div className="row">
                    <a className="btn btn-success btn-xs pull-right"
                       href={`/pdf/inventarios/${opcionesSeleccionadas.mes}/cliente/${opcionesSeleccionadas.idCliente}`}
                    >Exportar</a>

                    <TablaMensual
                        filtros={this.state.filtros}
                        actualizarFiltro={this.actualizarFiltro.bind(this)}
                        ordenarInventarios={this.ordenarInventarios.bind(this)}>
                        {this.state.inventariosFiltrados.length===0
                            ? <tr><td colSpan="9" style={{textAlign: 'center'}}><b>No hay inventarios para mostrar en este periodo.</b></td></tr>
                            : this.state.inventariosFiltrados.map((inventario, index)=>{
                            let mostrarSeparador = false
                            let sgteInventario = this.state.inventariosFiltrados[index+1]
                            if(sgteInventario)
                                mostrarSeparador = inventario.fechaProgramada!==sgteInventario.fechaProgramada
                            return <RowInventarioMensual
                                    // Propiedades
                                    puedeModificar={this.props.puedeModificar}
                                    key={index}
                                    index={index}
                                    ref={ref=>this.rows[index]=ref}
                                    inventario={inventario}
                                    mostrarSeparador={mostrarSeparador}
                                    // Metodos
                                    focusRow={this.focusRow.bind(this)}
                                    actualizarInventario={this.actualizarInventario.bind(this)}
                                    quitarInventario={this.quitarInventario.bind(this)}
                                    eliminarInventario={this.eliminarInventario.bind(this)}
                                    ref={ref=>this.rows[index]=ref}
                                />
                            })
                        }
                    </TablaMensual>
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