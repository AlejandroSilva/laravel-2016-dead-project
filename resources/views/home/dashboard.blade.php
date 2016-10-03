@extends('layouts.unacolumna')
@section('main-content')
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Bienvenido/a {{ Auth::user()->nombre1 }}</div>

                <div class="panel-body">
                    Seleccione una opción del menu superior
                </div>
            </div>
            <div id="react-main-dashboard"></div>

            {{-- Mis Inventarios --}}
            @if($mostrar_misProximosInventarios)
                @include('home.dashboard-misProximosInventarios')
            @endif

            {{-- Indicadores de gestión de inventarios --}}
            @if($mostrar_indicadoresDeInventarios)
                @include('home.dashboard-indicadoresGestionInventarios')
            @endif
        </div>
    </div>

    <script>
        window.laravelUsuario = {!! json_encode($usuario) !!};
    </script>
@endsection
