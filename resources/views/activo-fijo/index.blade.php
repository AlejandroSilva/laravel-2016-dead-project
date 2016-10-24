@extends('layouts.root')
@section('title', 'Contro de activo fijo')

@section('body')
    <div class="container">
        <div class="row">
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