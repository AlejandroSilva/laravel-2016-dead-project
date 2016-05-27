
{{-- locales.listado --}}
@extends('operacional.layoutOperacional')

@section('title', 'Geo')

@section('content')
    <div class="container">
        {{-- aca se montara el component 'ProgramacionMensual' de React --}}
        <h1>Captadores</h1>

        <ul class="list-unstyled">
        @foreach($captadores as $captador)
            <li>
                <a href="/nominas/captador/{{ $captador->id }}">{{ $captador->nombreCompleto() }}</a>
            </li>
        @endforeach
        </ul>
    </div>
@stop
