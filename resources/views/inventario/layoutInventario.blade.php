@extends('layouts.twocols')

@section('title', 'Inventario')

@section('sidebar')
    <ul class="nav nav-sidebar">
        <li class="{{ Request::is('inventario/programa')? 'active': '' }}">
            <a href="{{ url('inventario/programa') }}">Programa <span class="sr-only">(current)</span></a>
        </li>
        <li class="{{ Request::is('inventario/inventario')? 'active': '' }}">
            <a href="{{ url('inventario/inventario') }}">Inventarios </a>
        </li>
        <li class="{{ Request::is('inventario/nominas')? 'active': '' }}">
            <a href="{{ url('inventario/nominas') }}">Nominas </a>
        </li>
        <li class="{{ Request::is('inventario/nominasFinales')? 'active': '' }}">
            <a href="{{ url('inventario/nominasFinales') }}">Nominas Finales</a>
        </li>
    </ul>
@stop