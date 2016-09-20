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
            @include('home.dashboard-tablaNominas', $nominas)
        </div>
    </div>

    <script>
        window.laravelUsuario = {!! json_encode($user) !!};
        window.laravelPermisos = {!! json_encode($perms) !!};
        window.laravelFechaHoy = {!! json_encode($fechaHoy) !!};
    </script>
@endsection
