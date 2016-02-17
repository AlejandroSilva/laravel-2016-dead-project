@extends('usuarios.layoutUsuarios')

@section('title', 'Operadores')

@section('content')
    <h1 class="page-header">Operadores</h1>

    <ul>
        <li>lista de operadores</li>
        <li>ver inventarios realizados</li>
        <li>agregar operador</li>
        <ul>
            <li>nombre</li>
            <li>rut</li>
            <li>medio pago, numero de cuenta</li>
            <li>telefon, email</li>
        </ul>
        <li>editar operador</li>
        <ul>
            <li>evaluar</li>
            <li>comentar</li>
        </ul>
    </ul>
@stop