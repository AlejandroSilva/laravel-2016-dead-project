@extends('layouts.root')
@section('title', 'Programación Semanal AI')

@section('body')
    {{-- Styles --}}
    <link rel='stylesheet' href='/vendor/daterangepicker/daterangepicker.css'>

    <div class="container-fluid">
        <div class="row">
            {{-- aca se montara el component 'ProgramacionSemanal' de React --}}
            <div id="react-programacionAI-semanal">
                <p>Cargando, espere unos segundos</p>
            </div>
        </div>
    </div>

    <script>
        window.laravelPuedeModificarAuditorias = {!! $puedeModificarAuditorias !!};
        window.laravelPuedeRevisarAuditorias = {!! $puedeRevisarAuditorias !!};
        window.laravelClientes = {!! $clientes !!};
        window.laravelAuditores = {!! $auditores !!};
    </script>
@stop