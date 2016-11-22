@extends('layouts.root')
@section('title', 'Informes Finales')

@section('body')
    <style>
        {{-- header --}}
        .page-header{
            margin-top: 0 !important;
        }
        .page-header h2{
            margin-bottom: 0;
        }
        .page-header h3{
            margin: 0;
        }
        .nav-header {
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .nav-header > li > a {
            padding: 5px 15px !important;
        }
    </style>

    <div class="container">

        {{-- HEADER --}}
        <div class="container fluid">
            <div class="page-header row">
                <div class="col-sm-9">
                    <h2>Agregar archivos de respuesta de WOM</h2>
                </div>
                <div class="col-sm-3">
                    <ul class="nav nav-pills navbar-right nav-header">
                        <li class="success">
                            <a class="btn btn-xs btn-default pull" href="/archivos-respuesta-wom">
                                <span class="glyphicon glyphicon-arrow-left"></span>&nbsp;Volver</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 col-xs-12">

                {{-- FORMULARIO --}}
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form action="/agregar-archivos-respuesta-wom" method="POST" enctype="multipart/form-data">
                            <input type="hidden" value="{{ csrf_token() }}" name="_token">

                            {{-- Archivo --}}
                            <div class="form-group">
                                <label>Archivo de respuesta</label>
                                <input class="form-control" type="file" name="file" {{$puedeSubirArchivo? '' : 'disabled'}}>
                            </div>

                            <span class="input-group-btn">
                                <input type="submit" class="btn btn-primary btn-sm btn-block" value="Agregar" {{$puedeSubirArchivo? '' : 'disabled'}}/>
                            </span>

                            @if(session('mensaje-exito'))
                                <div class="alert alert-success" role="alert">
                                    {{session('mensaje-exito')}}
                                </div>
                            @endif
                            @if(session('mensaje-error'))
                                <div class="alert alert-danger" role="alert">
                                    {{session('mensaje-error')}}
                                </div>
                            @endif
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection