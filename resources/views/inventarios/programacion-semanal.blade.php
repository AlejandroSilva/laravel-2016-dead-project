@extends('layouts.root')
@section('title', 'Programación Semanal IG')

@section('body')
    {{-- Styles --}}
    <link rel='stylesheet' href='/vendor/daterangepicker/daterangepicker.css'>

    {{-- aca se montara el component 'ProgramacionSemanal' de React --}}
    <div class="container-fluid">
        <div class="row">
            <div id="react-programacionIG-semanal">
                <p>Cargando, espere unos segundos</p>
            </div>
        </div>
    </div>

    <script>
        window.laravelPuedeModificarInventarios = {!! $puedeModificarInventarios !!};
        window.laravelClientes = {!! $clientes !!};
        window.laravelCaptadores = {!! $captadores!!};
        window.laravelSupervisores = {!! $supervisores !!};
        window.laravelLideres = {!! $lideres !!};
    </script>
@stop