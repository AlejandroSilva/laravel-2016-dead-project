@extends('layouts.root')
@section('title', 'Mantenedor Locales')

@section('body')
    {{-- aca se montara el component 'ProgramacionMensual' de React --}}
    <div class="container-fluid">

        <div id="react-mantenedor-locales">
            <p>Cargando, espere unos segundos</p>
        </div>
    </div>

    <script>
        {{-- Se entrega la lista de clientes para ser utilizados por el cliente --}}
        window.laravelClientes = {!! $clientes !!};
        window.laravelJornadas = {!! $jornadas !!};
        window.laravelFormatoLocales = {!! $formatoLocales !!};
        window.laravelComunas = {!! $comunas !!};
    </script>
@stop