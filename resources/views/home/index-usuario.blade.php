@extends('layouts.root')

@section('body')
    <div class="container">
        <div class="row">
            {{-- Agregar archivos WOM --}}
            @if($puedeSubirArchivosWOM)
                @include('home.dashboard-agregarArchivosWOM')
            @endif
            {{-- Lider/Supervisor/Operador: Mis Inventarios --}}
            @if($mostrar_misProximosInventarios)
                @include('home.dashboard-misProximosInventarios')
            @endif

            {{-- Captador: Nominas asignadas para completarlas --}}
            @if($mostrar_misNominasAsignadasCaptador)
                @include('home.dashboard-misNominasAsignadasCaptador')
            @endif

            {{-- Indicadores de gestión de inventarios --}}
            @if($mostrar_indicadoresDeInventarios)
                @include('home.dashboard-indicadoresGestionInventarios')
            @endif

            {{-- Dashboard "Estado general de auditorias FCV --}}
            @if($mostrar_estadoGeneralAuditorias_fcv)
                @include('auditorias.estado-general-fcv.panel-estado-general')
            @endif
        </div>
    </div>
@endsection
