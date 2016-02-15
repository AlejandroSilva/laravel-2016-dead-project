{{-- locales.listado --}}
@extends('administracion.layoutAdministracion')

@section('title', 'Clientes')

@section('content')
    <h1 class="page-header">Lista de Clientes</h1>

    <pre>
        @foreach($clientes as $cliente)
            <a href="cliente/{{ $cliente->idCliente }}">{{ $cliente->nombre }}</a>
            {{ $cliente }}
        @endforeach
    </pre>
@stop