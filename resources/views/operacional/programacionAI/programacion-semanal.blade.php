@extends('operacional.layoutOperacional')
@section('title', 'Programación Mensual')

@section('content')
    {{-- Styles --}}
    <link rel='stylesheet' href='/vendor/daterangepicker/daterangepicker.css'>

    <div class="row">
        {{-- aca se montara el component 'ProgramacionSemanal' de React --}}
        <div id="react-programacionAI-semanal">
            <p>Cargando, espere unos segundos</p>
        </div>
    </div>

    <script>
        window.laravelPuedeModificarAuditorias = {!! $puedeModificarAuditorias !!};
        window.laravelPuedeRevisarAuditorias = {!! $puedeRevisarAuditorias !!};
        window.laravelClientes = {!! $clientes !!};
        window.laravelAuditores = {!! $auditores !!};
    </script>
@stop