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
            <div id="react-activo-fijo-index">
                <p>cargando...</p>
            </div>
        </div>
    </div>

    <script>
        window.laravelAlmacenes = {!! json_encode($almacenes) !!};
        window.laravelPermisos = {!! json_encode(array_values($permisos)) !!};
    </script>
@stop