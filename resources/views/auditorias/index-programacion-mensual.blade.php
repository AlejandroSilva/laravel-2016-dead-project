@extends('layouts.root')
@section('title', 'Programación Mensual AU')

@section('body')
    {{-- aca se montara el component 'ProgramacionMensual' de React --}}
    <div class="container-fluid">
        <div id="react-programacionAI-mensual">
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
        window.laravelPuedeModificarAuditorias = {!! $puedeModificarAuditorias !!};
        window.laravelPuedeAgregarAuditorias   = {!! $puedeAgregarAuditorias !!};
        window.laravelAuditores = {!! $auditores !!};
    </script>
@stop