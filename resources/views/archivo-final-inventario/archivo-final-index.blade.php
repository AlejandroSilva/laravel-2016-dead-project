@extends('layouts.unacolumna')

<div class="container-fluid">
    <div class="row">
        {{-- Datos del acta --}}
        @include('archivo-final-inventario.comp-datos-acta')
    </div>

    <div class="row">
        {{-- La tabla que incluye el listado de los archivos que han sido subidos a plataforma --}}
        @include('archivo-final-inventario.comp-tabla-archivos')
    </div>

<div class="container">
    <div class="row">
        <div class="col-md-offset-1 col-md-4">
            {{-- Formulario de envio de Zips --}}
            @include('archivo-final-inventario.comp-formulario-envio-zip')
        </div>
        <div class="col-md-offset-1 col-md-4">
            {{-- Formulario de envio de TXT --}}
            @include('archivo-final-inventario.comp-formulario-envio-txt')
        </div>
    </div>
</div>