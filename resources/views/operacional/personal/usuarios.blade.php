@extends('operacional.layoutOperacional')

@section('title', 'Usuarios')

@section('content')
    <h1 class="page-header">Usuarios</h1>

    <p>los usuarios son personal de SEI, tienen acceso al sistema, entre ellos
        estan <b>operadores</b>, <b>lideres</b>, etc.</p>
    <ul>
        <li>ver lista usuarios SEI</li>
        <li>crear usuarios (dar de alta)</li>
        <ul>
            <li>nombre, rut</li>
            <li>rol/cargo</li>
            <li>zonas de accion</li>
        </ul>
        <li>editar usuarios</li>
        <li>dar de baja / dar de alta usuarios</li>
        <li>asignar o quitar permisos permisos</li>
    </ul>
@stop