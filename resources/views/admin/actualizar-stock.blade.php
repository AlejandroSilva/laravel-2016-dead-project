@extends('operacional.layoutOperacional')

@section('title', 'Mantenedor Stock')

@section('content')
    {{-- aca se montara el component 'ProgramacionMensual' de React --}}
    <div id="react-mantenedor-stock">
        <p>Cargando, espere unos segundos</p>
    </div>

    <script>
        window.laravelClientes = {!! json_encode($clientes) !!};
    </script>
@stop