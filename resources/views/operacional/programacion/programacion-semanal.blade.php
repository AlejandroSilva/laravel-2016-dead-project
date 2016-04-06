@extends('operacional.layoutOperacional')
@section('title', 'Programación Mensual')

@section('content')
    {{-- Styles --}}
    <link rel='stylesheet' href='/vendor/daterangepicker/daterangepicker.css'>

    <div class="row">
        {{-- aca se montara el component 'ProgramacionSemanal' de React --}}
        <div id="react-programacion-semanal">
            <p>Cargando, espere unos segundos</p>
        </div>
    </div>

    <script>
        {{-- Se entregan las fechas minimas y maximas --}}
        window.laravelPrimerInventario = '{!! $primerInventario !!}';
        window.laravelUltimoInventario = '{!! $ultimoInventario !!}';
        window.laravelCaptadores = {!! $captadores!!};
        window.laravelSupervisores = {!! $supervisores !!};
        window.laravelLideres = {!! $lideres !!};
    </script>
@stop