// Librerias
import React from 'react'
import ReactDOM from 'react-dom'

// Componentes
import InventarioForm from './components/inventario/InventarioForm.jsx'
import ProgramacionMensual from './components/programacionMensual/ProgramacionMensual.jsx'
import ProgramacionSemanal from './components/programacionSemanal/ProgramacionSemanal.jsx'

// Carga el componente dependiendo del elemento DOM en la pagina
let nuevoInventarioDOM = document.getElementById('nuevo-inventario')
let programacionMensualDOM = document.getElementById('react-programacion-mensual')
let programacionSemanalDOM = document.getElementById('react-programacion-semanal')

if (nuevoInventarioDOM){
    ReactDOM.render(
        <InventarioForm
            clientes={window.laravelClientes}
        />, nuevoInventarioDOM)
}

if( programacionMensualDOM ){
    ReactDOM.render(
        <ProgramacionMensual
            clientes={window.laravelClientes}
        />, programacionMensualDOM)
}

if( programacionSemanalDOM ){
    ReactDOM.render(
        <ProgramacionSemanal
            primerInventario={window.laravelPrimerInventario}
            ultimoInventario={window.laravelUltimoInventario}
        />, programacionSemanalDOM)
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