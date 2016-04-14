{{-- locales.listado --}}
@extends('operacional.layoutOperacional')

@section('title', 'Listado de Locales')

@section('content')
    {{-- aca se montara el component 'ProgramacionMensual' de React --}}
    <div id="react-mantenedor-locales">
        <p>Cargando, espere unos segundos</p>
    </div>

    <script>
        {{-- Se entrega la lista de clientes para ser utilizados por el cliente --}}
        window.laravelClientes = {!! $clientes !!};
        window.laravelJornadas = {!! $jornadas !!};
        window.laravelFormatoLocales = {!! $formatoLocales !!};
    </script>
@stop