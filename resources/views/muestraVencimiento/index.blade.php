@extends('layouts.unacolumna')
<style  type="text/css">
    .tabla-documentos {
        font-size: 12px;
    }
    .tabla-documentos {
        display: block;
        height: 400px;
        overflow-y: scroll;
    }
    .td-no-wrap{
        white-space: nowrap;
    }
</style>

<div class="container">

    <div class="row">
        {{-- ARHIVOS SUBIDOS A PLATAFORMA --}}
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading" align="center">
                    <span class="glyphicon glyphicon-folder-close"></span> Muestras de Vencimiento
                </div>
                <div class="panel-body">
                    <table class="table table-responsive table-hover table-bordered tabla-documentos">
                        <thead>
                            <th>Nombre archivo</th>
                            <th>Subido por</th>
                            <th>Fecha subida</th>
                            <th>Estado</th>
                            <th>Valida</th>
                            <th>Resultado</th>
                        </thead>
                        <tbody>
                            @foreach($archivos as $archivo)
                                <tr class="{{ $archivo->muestraValida? 'success' : 'warning' }}">
                                    <td>{{ $archivo->nombre_original}}</td>
                                    <td class="td-no-wrap">{{ $archivo->subidoPor? $archivo->subidoPor->nombreCorto() : '-'}}</td>
                                    <td class="td-no-wrap">{{ $archivo->created_at }}</td>
                                    <td class="td-no-wrap">{{ $archivo->muestraValida? 'valida' : 'con errores' }}</td>
                                    <td>{{ $archivo->resultado }}</td>
                                    <td>
                                        <a href='/muestra-vencimiento-fcv/{{$archivo->idArchivoMuestraVencimientoFCV}}/descargar' class="btn btn-primary btn-xs">Descargar</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- ENVIAR NUEVA MUESTRA --}}
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading" align="center">
                    <span class="glyphicon glyphicon-upload"></span> Subir muestra<muestra></muestra>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="/muestra-vencimiento-fcv/subir-muestra-fcv" method="post" enctype="multipart/form-data">
                        <input type="hidden" value="{{ csrf_token() }}" name="_token">

                        <label class="col-md-3">Muestra de vencimiento</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="file" name="muestraVencimiento">
                        </div>

                        <div class="col-sm-offset-3 col-sm-9">
                            <input type="submit" class="btn btn-primary btn-sm btn-block" name="submit"></br>
                        </div>

                        {{-- mensaje de error o de exito luego de subir un archivo --}}
                        <div class="col-sm-12">
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
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- DESCARGAR DATOS EXISTENTE --}}
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading" align="center"><span class="glyphicon glyphicon-download-alt"></span> Descargar Datos</div>
                <div class="panel-body">
                    <div class="panel-body">
                        <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                            <label class="col-md-3">Descargar datos</label>
                            <div class="col-sm-9">
                                <a class="btn btn-primary btn-sm btn-block" disabled>Descargar datos</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>