@extends('operacional.layoutOperacional')
@section('title', 'Nuevo inventario')

@section('content')

    {{-- aca se montara el component 'NuevoInventario' de React --}}
    <div id="nuevo-inventario"></div>
    {{ csrf_field() }}


    <script>
        {{-- Se entrega la lista de clientes para ser utilizados por el cliente --}}
        @if(isset($clientes))
            var laravelClientes = {!! $clientes !!};
        @else
            console.error('no se recibieron datos de clientes desde la vista');
            var laravelClientes = [];
        @endif
    </script>

@stop