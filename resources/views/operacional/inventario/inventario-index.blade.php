@extends('operacional.layoutOperacional')
@section('title', 'Inventario')

@section('content')
    <div class="row">
        <h1 class="page-header">Inventario Index</h1>
        <ul>
            <li>ver lista de inventarios pasados (vista historica)</li>
            <li>ver inventarios por lideres</li>
            <li>ver inventarios por supervisores</li>
            <li>editar un inventario</li>
            <li>crear nuevo inventario</li>

            <li> * paso 1, datos del inventario:</li>
            <ul>
                <li>asignar fecha</li>
                <li>definir hora de llegada</li>
                <li>definir stock a inventariar</li>
                <li>definir un lider / los lideres</li>
                <li>definir un supervisor / los supervisores</li>
                <li>definir la cantidad de operadores a enviar</li>
            </ul>

            <li> * paso 2, completar nomina:</li>
            <ul>
                <li>agregar y quitar operadores</li>
                <li>confirmar nomina</li>
            </ul>

            <li> * paso 3, confirmacion con el cliente:</li>
            <ul>
                <li>enviar correo</li>
            </ul>
        </ul>
    </div>
@stop