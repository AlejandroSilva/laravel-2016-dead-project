@extends('layouts.root')
{{--@section('title', '')--}}

@section('body')
    {{-- aca se montara el component 'TablaAuditoriasPendientes' de React --}}
    <div id="react-nominaIG-nominaIG">
        <p>Cargando, espere unos segundos</p>
    </div>

    <script>
        window.laravelNomina = {!! json_encode($nomina) !!};
        window.laravelComunas = {!! json_encode($comunas) !!};
        window.laravelPermisos = {!! json_encode($permisos) !!};
    </script>
@stop