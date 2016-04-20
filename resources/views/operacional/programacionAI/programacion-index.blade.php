@extends('operacional.layoutOperacional')
@section('title', 'Programa')

@section('content')
    {{-- aca se montara el component 'TablaAuditoriasPendientes' de React --}}
    <div id="react-programacionAI-pendientes">
        <p>Cargando, espere unos segundos</p>
    </div>

    <script>
        {{--window.laravelPuedeModificarInventarios = {!! $puedeModificarInventarios !!};--}}
    </script>
@stop