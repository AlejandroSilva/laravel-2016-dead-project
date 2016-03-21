@extends('operacional.layoutOperacional')
@section('title', 'Programación Mensual')

@section('content')
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
    </script>
@stop