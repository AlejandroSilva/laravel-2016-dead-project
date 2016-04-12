// Librerias
import React from 'react'
import ReactDOM from 'react-dom'

// Componentes
import InventarioForm from './components/inventario/InventarioForm.jsx'
import ProgramacionIGMensual from './components/programacionIGMensual/ProgramacionIGMensual.jsx'
import ProgramacionIGSemanal from './components/programacionIGSemanal/ProgramacionIGSemanal.jsx'
import ProgramacionAIMensual from './components/programacionAIMensual/ProgramacionAIMensual.jsx'
import ProgramacionAISemanal from './components/programacionAISemanal/ProgramacionAISemanal.jsx'

/* FORMULARIO PARA LA CREACIÓN DE UN NUEVO FORMULARIO */
let nuevoInventarioDOM = document.getElementById('nuevo-inventario')
if (nuevoInventarioDOM){
    ReactDOM.render(
        <InventarioForm
            clientes={window.laravelClientes}
        />, nuevoInventarioDOM)
}

/* PROGRAMACIÓN INVENTARIO GENERAL */
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

/* PROGRAMACIÓN AUDITORIA INVENTARIO */
let programacionAIMensualDOM = document.getElementById('react-programacionAI-mensual')
if( programacionAIMensualDOM ){
    ReactDOM.render(
        <ProgramacionAIMensual
            puedeModificar={ window.laravelPuedeModificarAuditorias }
            puedeAgregar={ window.laravelPuedeAgregarAuditorias }
            clientes={window.laravelClientes}
            auditores={window.laravelAuditores}
        />, programacionAIMensualDOM)
}
let programacionAISemanalDOM = document.getElementById('react-programacionAI-semanal')
if( programacionAISemanalDOM ){
    ReactDOM.render(
        <ProgramacionAISemanal
            puedeModificar={ window.laravelPuedeModificarAuditorias }
            clientes={window.laravelClientes}
            auditores={window.laravelAuditores}
        />, programacionAISemanalDOM)
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