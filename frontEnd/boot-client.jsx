window.$ = window.jQuery = require('../public/vendor/jquery/jquery-2.1.1.min-xs.js')
require('../public/vendor/bootstrap/bootstrap.min-xs.js')
// require('../public/vendor/zinga-scroller/ZingaAnimateScroller.min.js')

// Librerias
import React from 'react'
import ReactDOM from 'react-dom'

// Componentes
// DASHBOARD
import { Dashboard } from './home/Dashboard.jsx'
// LOGISTICA
import ProgramacionIGMensual from './inventarios/programacion-mensual/ProgramacionIGMensual.jsx'
import ProgramacionIGSemanal from './inventarios/programacion-semanal/ProgramacionIGSemanal.jsx'
import ProgramacionAIMensual from './auditorias/programacion-mensual/ProgramacionAIMensual.jsx'
import ProgramacionAISemanal from './auditorias/programacion-semanal/ProgramacionAISemanal.jsx'
import MantenedorLocales2 from './admin/mantenedorLocales2/MantenedorLocales2.jsx'
import { NominaIG } from './nominas/nominaIG/nominaIG.jsx'
// OTROS
import { MantenedorStock } from './admin/mantenedorStock/MantenedorStock.jsx'
import { ActivoFijo } from './activo-fijo/ActivoFijo.jsx'
import { MantenedorPersonal } from './admin/MantenedorPersonal/MantenedorPersonal.jsx'
import { PanelDatosActa } from './inventarios/archivo-final-fcv/PanelDatosActa.jsx'

/** ************************************************************* **/
/** ************************ DASHBOARD ************************** **/
/** ************************************************************* **/
let mainDashboardDOM = document.getElementById('react-main-dashboard')
if( mainDashboardDOM ){
    ReactDOM.render(
        <Dashboard
            usuario={window.laravelUsuario}
        />, mainDashboardDOM)
}

let datosActaInventarioDOM = document.getElementById('react-datos-acta-inventario-fcv')
if( datosActaInventarioDOM ){
    ReactDOM.render(
        <PanelDatosActa
            idInventario={window.laravelIdInventario}
            puedeEditar={window.laravelpuedeEditar}
        />, datosActaInventarioDOM)
}
/** ************************************************************* **/
/** ************************ LOGISTICA ************************** **/
/** ************************************************************* **/

/** PROGRAMACIÓN INVENTARIO GENERAL */
let programacionIGMensualDOM = document.getElementById('react-programacionIG-mensual')
if( programacionIGMensualDOM ){
    ReactDOM.render(
        <ProgramacionIGMensual
            puedeModificar={ window.laravelPuedeModificarInventarios }
            puedeAgregar={ window.laravelPuedeAgregarInventarios }
            clientes={window.laravelClientes}
        />, programacionIGMensualDOM)
}
let programacionIGSemanalDOM = document.getElementById('react-programacionIG-semanal')
if( programacionIGSemanalDOM ){
    ReactDOM.render(
        <ProgramacionIGSemanal
            puedeModificar={ window.laravelPuedeModificarInventarios }
            clientes={window.laravelClientes}
            lideres={window.laravelLideres}
            supervisores={window.laravelSupervisores}
            captadores={window.laravelCaptadores}
        />, programacionIGSemanalDOM)
}

/** PROGRAMACIÓN AUDITORIA INVENTARIO */
let programacionAIMensualDOM = document.getElementById('react-programacionAI-mensual')
if( programacionAIMensualDOM ){
    /** IMPORANTE: POR EL MOMENTO SOLO SE MUESTRAN AUDITORIAS DE FCV **/
    let clienteFCV = window.laravelClientes.filter(cliente=>cliente.nombreCorto==='FCV' || cliente.nombreCorto==='WOM')
    ReactDOM.render(
        <ProgramacionAIMensual
            puedeModificar={ window.laravelPuedeModificarAuditorias }
            puedeAgregar={ window.laravelPuedeAgregarAuditorias }
            clientes={clienteFCV}
            // clientes={window.laravelClientes}
            auditores={window.laravelAuditores}
        />, programacionAIMensualDOM)
}
let programacionAISemanalDOM = document.getElementById('react-programacionAI-semanal')
if( programacionAISemanalDOM ){
    /** IMPORANTE: POR EL MOMENTO SOLO SE MUESTRAN AUDITORIAS DE FCV **/
    let clienteFCV_ = window.laravelClientes.filter(cliente=>cliente.nombreCorto==='FCV')
    ReactDOM.render(
        <ProgramacionAISemanal
            puedeModificar={ window.laravelPuedeModificarAuditorias }
            puedeRevisar={laravelPuedeRevisarAuditorias}
            clientes={clienteFCV_}
            auditores={window.laravelAuditores}
        />, programacionAISemanalDOM)
}

/** MANTENEDOR LOCALES **/
let mantenedorLocalesDOM = document.getElementById('react-mantenedor-locales')
if( mantenedorLocalesDOM ){
    ReactDOM.render(
        <MantenedorLocales2
            clientes={window.laravelClientes}
            jornadas={window.laravelJornadas}
            formatoLocales={window.laravelFormatoLocales}
            comunas={window.laravelComunas}
        />, mantenedorLocalesDOM)
}
/** MANTENEDOR STOCK **/
let mantenedorStockDOM = document.getElementById('react-mantenedor-stock')
if( mantenedorStockDOM ) {
    ReactDOM.render(
        <MantenedorStock
            clientes={window.laravelClientes}
        />, mantenedorStockDOM
    )
}

/** NOMINAS **/
let mantenedorNominaIGDOM = document.getElementById('react-nominaIG-nominaIG')
if( mantenedorNominaIGDOM ){
    ReactDOM.render(
        <NominaIG
            nomina={window.laravelNomina}
            comunas={window.laravelComunas}
            permisos={window.laravelPermisos}
        />, mantenedorNominaIGDOM)
}

/** ************************************************************* **/
/** ************************** OTROS **************************** **/
/** ************************************************************* **/

/** ACTIVOS FIJOS **/
let activoFijoDOM = document.getElementById('react-activo-fijo-index')
if( activoFijoDOM ) {
    ReactDOM.render(
        <ActivoFijo
            almacenes={window.laravelAlmacenes}
            permisos={window.laravelPermisos}
        />, activoFijoDOM
    )
}

/** MANTENEDOR USUARIOS **/
let personalIndexDOM = document.getElementById('react-personal-index')
if( personalIndexDOM ) {
    ReactDOM.render(
        <MantenedorPersonal/>,
        personalIndexDOM
    )
}


// http://jamesknelson.com/push-state-vs-hash-based-routing-with-react-js/
//class App extends React.Component{
//    render(){
//        console.log('render componente')
//        return <div>
//            <h1>golas {this.props.route}</h1>
//        </div>
//    }
//}
//
//let hashChangeHandler = ()=>{
//    let hashes = window.location.hash.replace(/^#\/?|\/$/g, '').split('/')
//
//    ReactDOM.render( <App route={hashes[0]}/>, document.getElementById('rc-top-menu'))
//}
//hashChangeHandler()
//window.addEventListener('hashchange', hashChangeHandler, false)