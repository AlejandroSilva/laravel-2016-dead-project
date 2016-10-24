@extends('layouts.root')
@section('title', 'Próximas nóminas')

@section('body')
    {{-- aca se montara el component 'ProgramacionMensual' de React --}}
    <div id="react-nominas-captador">
        <p>Cargando, espere unos segundos</p>
    </div>

    <script>
        window.laravelNominas = {!! json_encode($nominas) !!};
    </script>
@stop