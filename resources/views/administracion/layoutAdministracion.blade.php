@extends('layouts.twocols')

@section('title', 'Administración')

@section('sidebar')
    <ul class="nav nav-sidebar">
        <li class="{{ Request::is('admin/clientes') ? 'active' : '' }}">
            <a href="{{ route('admin.clientes.lista') }}">Clientes <span class="sr-only">(current)</span></a>
        </li>
        <li class="{{ Request::is('admin/locales') ? 'active' : '' }}">
            <a href="{{ url('admin/locales') }}">Locales <span class="sr-only">(current)</span></a>
        </li>
    </ul>
@stop