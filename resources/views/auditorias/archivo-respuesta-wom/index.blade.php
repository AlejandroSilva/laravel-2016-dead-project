@extends('layouts.root')
@section('title', 'Archivos Respuesta')

@section('body')
    <style>
        {{-- header --}}
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


        .btn-100 {
            /*width: 80px;*/
            text-align: left !important;
        }
        .texto-centrado{
            text-align: center;
        }
        .descargar-consolidado-link{
            width: 100%;
            margin: 5px 0;
            text-align: left !important;
        }
    </style>

    <div class="container">

        {{-- HEADER --}}
        <div class="container fluid">
            <div class="page-header row">
                <div class="col-sm-10">
                    <h2>Archivos de respuesta de WOM</h2>
                </div>
                <div class="col-sm-2">
                    <ul class="nav nav-pills navbar-right nav-header">
                        <li class="success" >
                            <a class="btn btn-xs btn-success pull"
                               href={{ $puedeSubirArchivo? 'agregar-archivos-respuesta-wom' : '#' }}
                               {{ $puedeSubirArchivo? '' : 'disabled' }}
                            >
                                <span class="glyphicon glyphicon-plus-sign"></span>&nbsp;Agregar archivo</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-3">

                {{-- PANEL BUSQUEDA --}}
                <div class="panel panel-default hidden-xs">
                    <div class="panel-heading">
                        Buscar Inventarios
                    </div>
                    <div class="panel-body">
                        <form action="/inventarios/informes-finales-fcv" method="GET">
                            {{-- Numero de local --}}
                            <div class="form-group">
                                <label>Numero de Local</label>
                                <input class="form-control" name="ceco" placeholder="Numero de local"
                                       disabled
                                       {{--value={{$cecoBuscado}}--}}
                                >
                            </div>
                            {{-- Fecha programada --}}
                            <div class="form-group">
                                <label>Mes</label>
                                <select name="mes" class="form-control" disabled>
                                    <option value="">Todos</option>
                                    {{--@foreach($mesesFechaProgramada as $mes)--}}
                                        {{--<option value="{{$mes['value']}}" {{$mesBuscado==$mes['value']? 'selected' : ''}}>--}}
                                            {{--{{$mes['text']}}--}}
                                        {{--</option>--}}
                                    {{--@endforeach--}}
                                </select>
                            </div>
                            {{-- Incluir inventarios sin archivo final--}}
                            {{--<div class="checkbox">--}}
                                {{--<label>--}}
                                    {{--<input type="checkbox" name="incluirPendientes" {{$incluirPendientes? 'checked="selected"' : ''}}/>--}}
                                    {{--incluir inventarios pendientes--}}
                                {{--</label>--}}
                            {{--</div>--}}
                            {{-- Buscar --}}
                            <span class="input-group-btn">
                                <input type="submit" class="btn btn-primary btn-sm btn-block" value="Buscar" disabled/>
                            </span>
                        </form>
                    </div>
                </div>

                {{-- PANEL CONSOLIDADOS --}}
                {{--<div class="panel panel-default">--}}
                    {{--<div class="panel-heading">--}}
                        {{--Consolidado de Inventarios--}}
                    {{--</div>--}}
                    {{--<div class="panel-body">--}}
                        {{--@foreach($mesesConsolidados as $mes)--}}
                            {{--<a class="btn btn-primary descargar-consolidado-link"--}}
                               {{--href="/inventarios/descargar-consolidado-fcv?mes={{$mes['value']}}&orden=desc">--}}
                                {{--<span class="glyphicon glyphicon-download-alt"></span>--}}
                                {{--Descargar {{$mes['text']}}--}}
                            {{--</a>--}}
                        {{--@endforeach--}}
                    {{--</div>--}}
                {{--</div>--}}

            </div>
            {{-- TABLA DE INVENTARIOS --}}
            <div class="col-sm-9">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Inventarios
                    </div>
                    <table class="table table-bordered table-hover table-condensed tabla-nominas">
                        <thead>
                        <tr>
                            <th class="th">#</th>
                            <th class="th">Nombre archivo</th>
                            <th class="th">Fecha/hora subida</th>
                            <th class="th">Opciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if( count($archivosRespuesta)>0 )
                            @foreach($archivosRespuesta as $archivo)
                                <tr>
                                    <td>{{ $archivo->idArchivoRespuestaWOM }}</td>
                                    <td>{{ $archivo->nombreOriginal }}</td>
                                    <td>{{ $archivo->created_at }}</td>
                                    <td class="td-opciones">
                                        <a class="btn btn-default btn-xs btn-100" href='archivo-respuesta-wom/{{ $archivo->idArchivoRespuestaWOM }}/descargar-original'>
                                            <span class="glyphicon glyphicon-download-alt"></span>
                                            Original
                                        </a>
                                        <a class="btn btn-primary btn-xs btn-100" href='archivo-respuesta-wom/{{ $archivo->idArchivoRespuestaWOM }}/descargar-excel'>
                                            <span class="glyphicon glyphicon-download-alt"></span>
                                            Excel
                                        </a>
                                        {{--<a class="btn btn-primary btn-xs btn-100" href='archivo-respuesta-wom/{{ $archivo->idArchivoRespuestaWOM }}/descargar-pdf'>--}}
                                            {{--<span class="glyphicon glyphicon-download-alt"></span>--}}
                                            {{--PDF--}}
                                        {{--</a>--}}
                                        {{--<a class="btn btn-primary btn-xs btn-100" href='archivo-respuesta-wom/{{ $archivo->idArchivoRespuestaWOM }}/preview-pdf'>--}}
                                            {{--<span class="glyphicon glyphicon-download-alt"></span>--}}
                                            {{--PDF-preview--}}
                                        {{--</a>--}}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="texto-centrado">
                                    Sin archivos en este periodo
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection