@extends('layouts.root')
@section('title', 'Mantenedor Correos')

@section('body')
    {{-- aca se montara el component 'ProgramacionMensual' de React --}}
    <div class="container-fluid">

        <div id="react-mantenedor-clientes">
            <p>Cargando, espere unos segundos</p>
        </div>
    </div>

    <script>
        window.laravelClientes = {!! $clientes !!};
    </script>
@stop