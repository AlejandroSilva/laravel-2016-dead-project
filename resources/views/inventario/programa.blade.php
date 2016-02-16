@extends('inventario.layoutInventario')

@section('title', 'Programa')

@section('content')
    <h1 class="page-header">Programa</h1>

    <ul>
        <li>vista reducida diaria de los inventarios</li>
        <li>vista reducida semanal de los inventarios</li>
        <li>vista reducida mensual de los inventarios</li>
        <li>la vista reducida contempla:</li>
        <ul>
            <li>nombre, fecha y hora de inventario</li>
            <li>lider y supervisor asignado</li>
            <li>estado de la nomina (incompleta, completa, confirmada)</li>
        </ul>
    </ul>
@stop