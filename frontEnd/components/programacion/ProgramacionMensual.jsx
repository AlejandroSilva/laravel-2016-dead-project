// Libs
import React from 'react'
import moment from 'moment'
moment.locale('es')
import api from '../../apiClient/v1'
import Inventarios from './Inventarios.js'

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
                valor: mes.format('YYYY-MM'),
                texto: mes.format('MMMM  YYYY')
            })
        }
        this.state = {
            inventariosFiltrados: [],
            meses
        }
        // MAGIA NEGRA!!
        this.blackbox = new Inventarios(this.props.clientes)
    }
    componentDidMount(){
        this.agregarInventario(2, this.props.clientes[1].locales[1].numero, '2016-07')
        //this.agregarInventario(this.props.clientes[1].locales[2].numero, '2016-04')
        this.agregarInventario(2, this.props.clientes[1].locales[4].numero, '2016-07')
    //    this.agregarInventario(this.props.clientes[0].locales[5].numero, '2016-05')
    //    this.agregarInventario(this.props.clientes[0].locales[6].numero, '2016-05')
        this.agregarInventario(2, this.props.clientes[1].locales[8].numero, '2016-09')
        this.agregarInventario(1, this.props.clientes[0].locales[12].numero, '2016-08')
    }

    agregarInventario(idCliente, numeroLocal, annoMes){
        let [errores, nuevoInventario] = this.blackbox.crearDummy(idCliente, numeroLocal, annoMes)
        if(errores)
            return [errores, {}]

        // agregar al listado
        this.blackbox.add(nuevoInventario)

        // actualizar la vista de la lista
        this.setState({
            inventariosFiltrados: this.blackbox.getListaFiltrada()
        })

        // fetch de todos los datos, y actualizacion de la lista
        api.locales.getVerbose(nuevoInventario.local.idLocal)
            .then(local=>{
                this.blackbox.actualizarDatosLocal(local)

                this.setState({
                    inventariosFiltrados: this.blackbox.getListaFiltrada()
                })
            })
            .catch(error=>{
                console.error(`error al obtener los datos de ${nuevoInventario.local.idLocal}`, error)
                alert(`error al obtener los datos de ${nuevoInventario.local.idLocal}`)
            })

        return [null, {}]
    }

    agregarGrupoInventarios(idCliente, idLocales, annoMes){
        let promesasFetch = []
        let pegadoConProblemas = []
        // se evalua y agrega cada uno de los elementos
        idLocales.forEach(idLocal=> {
            let [errores, nuevoInventario] = this.blackbox.crearDummy(idCliente, idLocal, annoMes)
            if (errores){
                pegadoConProblemas.push(errores)
            }else{
                // pedir los datos de los locales
                promesasFetch.push(
                    api.locales.getVerbose(nuevoInventario.local.idLocal)
                        .then(local=>this.blackbox.actualizarDatosLocal(local))
                        .catch(error=>console.error('error con :', error))
                )
                this.blackbox.add(nuevoInventario)
            }
        })

        // cuando terminen todos, se actualiza el state de la aplicacion
        this.setState({
            inventariosFiltrados: this.blackbox.getListaFiltrada()
        })

        // en algun momento las promesas se van a cumplior, entonces actualizar el estado
        Promise.all(promesasFetch)
            .then(locales=>{
                console.log('AgregarGrupoInventarios desde Excel: fetch de todos los locales correcto')
                this.setState({inventariosFiltrados: this.blackbox.getListaFiltrada()})
            })
            .catch(datos=> {
                // Todo: agregar bluebird para que esto no ocurra nunca
                // todo, al fallar UNA promesa, no se cumple el resto
                alert('error al buscar la información de los locales, (AgregarGrupoInventarios desde Excel: fetch de todos los locales correcto)')
                this.setState({inventariosFiltrados: this.blackbox.getListaFiltrada()})
            })
        return {
            pegadoConProblemas: pegadoConProblemas,
            conteoTotal: idLocales.length,
            conteoCorrectos: idLocales.length - pegadoConProblemas.length,
            conteoProblemas: pegadoConProblemas.length
        }
    }

    guardarOCrearInventario(){
        //if(evt) evt.preventDefault()
        //
        //let jornada  = this.inputJornada.value
        //console.log("guardar o crear: ", jornada)
        //
        //// Todo: validar esto
        //const fechaEsValida = this.state.inputDia>=1 && this.state.inputDia<= 31//this.props.ultimoDiaMes
        //if(fechaEsValida){
        //    // ToDo: llamar al API
        //    this.props.guardarOCrear({
        //        idLocal: this.props.local.idLocal,
        //        idJornada: jornada,
        //        fechaProgramada: `${this.props.annoProgramado}-${this.props.mesProgramado}-${this.state.inputDia}`,
        //        horaLlegada: '00:00',
        //        stockTeorico: this.props.local.stock,
        //        dotacionAsignada: 66,
        //    }).then(res=>{
        //        this.setState({
        //            //guardado: true,
        //            fechaValida: true,
        //            estado: ESTADO.GUARDADO
        //        })
        //    }).catch(err=>{
        //        console.error(err)
        //    })
        //}else {
        //    this.setState({
        //        //guardado: true,
        //        fechaValida: false,
        //        estado: ESTADO.FECHA_INVALIDA
        //    })
        //    console.log(`dia ${this.state.inputDia} incorrecto`)
        //}
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
                />

                <div className="row">
                    <h4 className="page-header" style={{marginTop: '1em'}}>Locales a programar:</h4>
                    <TablaProgramas
                        inventariosFiltrados={this.state.inventariosFiltrados}
                        guardarOCrearInventario={this.guardarOCrearInventario.bind(this)}
                        ref={ref=>this.TablaInventarios=ref}
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