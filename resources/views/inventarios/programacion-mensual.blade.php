
@extends('layouts.root')
@section('title', 'Programación Mensual IG')

@section('body')
    {{-- aca se montara el component 'ProgramacionMensual' de React --}}
    <div class="container-fluid">
        <div id="react-programacionIG-mensual">
            <p>Cargando, espere unos segundos</p>
        </div>
    </div>

    <script>
        {{-- Se entrega la lista de clientes para ser utilizados por el cliente --}}
        @if(isset($clientes))
            window.laravelClientes = {!! $clientes !!};
        @else|
            console.error('no se recibieron datos de clientes desde la vista');
            window.laravelClientes = [];
        @endif
        window.laravelPuedeModificarInventarios = {!! $puedeModificarInventarios !!};
        window.laravelPuedeAgregarInventarios   = {!! $puedeAgregarInventarios !!};
    </script>
@stop