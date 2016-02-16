@extends('layouts.twocols')

@section('title', 'Usuarios')

@section('sidebar')
    <ul class="nav nav-sidebar">
        <li class="{{ Request::is('usuarios/usuarios') ? 'active' : '' }}">
            <a href="{{ url('usuarios/usuarios') }}">Usuarios <span class="sr-only">(current)</span></a>
        </li>
        <li class="{{ Request::is('usuarios/operadores') ? 'active' : '' }}">
            <a href="{{ url('usuarios/operadores') }}">Operadores <span class="sr-only">(current)</span></a>
        </li>
    </ul>
@stop