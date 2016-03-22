@extends('operacional.layoutOperacional')
@section('title', 'Programación Mensual')

@section('content')

    {{-- aca se montara el component 'ProgramacionMensual' de React --}}
    <div id="react-programacion-mensual">
        <p>Cargando, espere unos segundos</p>
    </div>

    <script>
        {{-- Se entrega la lista de clientes para ser utilizados por el cliente --}}
        @if(isset($clientes))
            window.laravelClientes = {!! $clientes !!};
        @else|
            console.error('no se recibieron datos de clientes desde la vista');
            window.laravelClientes = [];
        @endif
    </script>
@stop