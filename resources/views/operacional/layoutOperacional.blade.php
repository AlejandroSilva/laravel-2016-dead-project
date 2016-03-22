@extends('layouts.doscolumnas')

@section('title', 'Inventario')

@section('main-sidebarXX')

    <ul class="nav nav-sidebar">
        {{-- Gestión de Inventarios --}}
        <li><h4>Inventarios</h4></li>
        <li class="{{ Request::is('programacion/*')? 'active': '' }}">
            <a href="{{ url('programacion') }}">Programación</a>
        </li>
        <li class="{{ Request::is('inventario/*')? 'active': '' }}">
            <a href="{{ url('inventario') }}">Inventarios </a>
        </li>
        <li class="{{ Request::is('nominas*')? 'active': '' }}">
            <a href="{{ url('nominas') }}">Nominas </a>
        </li>
        <li class="{{ Request::is('nomFinales*')? 'active': '' }}">
            <a href="{{ url('nomFinales') }}">Nominas Finales</a>
        </li>

        {{-- Gestión de Personal --}}
        <li><h4>Personal</h4></li>
        <li class="{{ Request::is('personal/personal') ? 'active' : '' }}">
        <a href="{{ url('personal/personal') }}">Usuarios</a>
        </li>
        <li class="{{ Request::is('personal/operadores') ? 'active' : '' }}">
        <a href="{{ url('personal/operadores') }}">Operadores</a>
        </li>

        {{-- Gestión de Clientes --}}
        <li><h4>Clientes</h4></li>
        <li class="{{ Request::is('admin/clientes') ? 'active' : '' }}">
            <a href="{{ route('admin.clientes.lista') }}">Clientes</a>
        </li>
        <li class="{{ Request::is('admin/locales') ? 'active' : '' }}">
            <a href="{{ url('admin/locales') }}">Locales</a>
        </li>
    </ul>
@stop

@section('main-content')
    @yield('content')
@stop