{{-- locales.listado --}}
@extends('administracion.layoutAdministracion')

@section('title', 'Locales')

@section('content')
    <h1 class="page-header">Lista de Locales</h1>

    <ul>
        <li>ver lista de locales</li>
        <li>ver historial de inventarios por locales</li>
        <li>crear local</li>
        <li>editar local</li>
        <li>ver local</li>
        <ul>
            <li>inventarios pasados</li>
            <li>actualizar stock</li>
        </ul>
    </ul>
@stop