{{-- locales.listado --}}
@extends('operacional.layoutOperacional')

@section('title', 'Clientes')

@section('content')
    <h1 class="page-header">Lista de Clientes</h1>

    <pre>
        @foreach($clientes as $cliente)
            <a href="cliente/{{ $cliente->idCliente }}">{{ $cliente->nombre }}</a>
            {{ $cliente }}
        @endforeach
    </pre>

    <ul>
        <li>ver clientes</li>
        <li>crear cliente</li>
        <li>editar cliente</li>
    </ul>
@stop