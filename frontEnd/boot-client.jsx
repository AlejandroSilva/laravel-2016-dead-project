// Librerias
import React from 'react'
import ReactDOM from 'react-dom'

// Componentes
import InventarioForm from './components/inventario/InventarioForm.jsx'
import ProgramacionMensual from './components/programacion/ProgramacionMensual.jsx'

// Carga el componente dependiendo del elemento DOM en la pagina
let nuevoInventarioDOM = document.getElementById('nuevo-inventario')
let programacionMensualDOM = document.getElementById('sei-programacion-mensual')


if (nuevoInventarioDOM){
    console.log("es la pagina de inventario")
    ReactDOM.render(
        <InventarioForm
            clientes={window.laravelClientes}
        />, nuevoInventarioDOM)
}

if( programacionMensualDOM ){
    console.log("es la pagina de programacion mensual")
    ReactDOM.render(
        <ProgramacionMensual
            clientes={window.laravelClientes}
        />, programacionMensualDOM)
}
