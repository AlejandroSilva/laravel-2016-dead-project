@extends('layouts.unacolumna')

@section('title', 'Activo Fijo')

@section('main-content')
    <div class="container">
        <div class="row">
            {{-- aca se montara el component 'NuevoInventario' de React --}}
            {{--
            <h3 class="page-header">Cliente</h3>
            <a href="/maestra-productos/cliente/{{ $cliente->idCliente }}">Administrar Maestra</a>

            <br>
            --}}
            <h3 class="page-header">Local: {{$local->cliente->nombreCorto}}, {{$local->numero}}, {{$local->nombre}}</h3>


            <div id="react-activo-fijo-local">
                <p>cargando...</p>
            </div>
        </div>
    </div>

    <script>
        window.laravelLocal = {!! json_encode($local) !!};
        window.laravelAlmacenes = {!! json_encode($almacenes) !!};
    </script>
@stop