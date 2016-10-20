@extends('layouts.unacolumna')
@section('main-content')
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            {{-- Mis Inventarios --}}
            @if($mostrar_misProximosInventarios)
                @include('home.dashboard-misProximosInventarios')
            @endif

            {{-- Indicadores de gestión de inventarios --}}
            @if($mostrar_indicadoresDeInventarios)
                @include('home.dashboard-indicadoresGestionInventarios')
            @endif

            {{-- Dashboard "Estado general de auditorias FCV --}}
            @if($mostrar_indicadoresDeInventarios)
                @include('auditorias.estado-general-fcv.panel-estado-general')
            @endif
        </div>
    </div>
@endsection
