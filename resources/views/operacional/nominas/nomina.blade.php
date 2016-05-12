@extends('operacional.layoutOperacional')
@section('title', 'Programa')

@section('content')
    {{-- aca se montara el component 'TablaAuditoriasPendientes' de React --}}
    <div id="react-nominaIG-nominaIG">
        <p>Cargando, espere unos segundos</p>
    </div>

    <script>
        window.laravelInventario = {!! json_encode($inventario) !!};
        window.laravelNomina = {!! json_encode($nomina) !!};
        window.laravelComunas = {!! json_encode($comunas) !!};
    </script>
@stop