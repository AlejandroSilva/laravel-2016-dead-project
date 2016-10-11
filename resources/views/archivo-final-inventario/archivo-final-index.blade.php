@extends('layouts.unacolumna')

<script>
    window.laravelIdInventario = {!! $inventario->idInventario !!};
</script>

<div class="container-fluid">
    <h3>
        {{$inventario->local->cliente->nombreCorto }}
        <b>{{$inventario->local->numero}}</b>:
        {{$inventario->local->nombre}},
        {{$inventario->fechaProgramadaF()}}
    </h3>
    <div class="row">
        <div id="react-datos-acta-inventario-fcv">
            cargando datos del acta...
        </div>
    </div>

    <div class="row">
        {{-- La tabla que incluye el listado de los archivos que han sido subidos a plataforma --}}
        @include('archivo-final-inventario.comp-tabla-archivos')
    </div>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            {{-- Formulario de envio de Zips --}}
            @include('archivo-final-inventario.comp-formulario-envio-zip')
        </div>
        <div class="col-md-4">
            {{-- Formulario de envio de TXT --}}
            @include('archivo-final-inventario.comp-formulario-envio-txt')
        </div>
        <div class="col-md-4">
            {{-- Descargar de consolidado --}}
            @include('archivo-final-inventario.comp-descargar')
        </div>
    </div>
</div>