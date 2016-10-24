@extends('layouts.root')

@section('body')
    <div class="container">
        <div class="row">
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
