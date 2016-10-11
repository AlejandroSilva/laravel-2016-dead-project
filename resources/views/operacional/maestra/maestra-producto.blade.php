@extends('layouts.unacolumna')
<style  type="text/css">

    .tablefiles {
        font-size: 12px;
        padding: 0px !important;
        text-align: center;
    }

    .tablefiles th {
        text-align: center;
        font-size: 12px;
        width: 10% !important;
        padding: 0px !important;

    }

    .tablefiles td {
        text-align: center;
        width: 10px;
        padding: 1px !important;

    }

    .panel-body {
        padding: 0px !important;
    }

</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading" align="center"><span class="glyphicon glyphicon-folder-close"></span> Maestra Productos</div>
                <div style="height:110px;overflow:auto;" class="panel-body">
                    <table class="table table-responsive table-hover tablefiles table-bordered">
                        <thead>
                        <th>Nombre archivo</th>
                        <th>Subido Por</th>
                        <th>Fecha Subida</th>
                        <th>Estado</th>
                        <th>Válida</th>
                        <th width="1px">Resultado</th>
                        </thead>
                        @foreach($archivosMaestraFCV as $archivoMaestraFCV)
                            <tr class="{{ $archivoMaestraFCV->maestraValida? 'success' : 'warning' }}">
                                <td>{{ $archivoMaestraFCV->nombreOriginal}}</td>
                                <td>{{ $archivoMaestraFCV->usuario->nombreCompleto() }}</td>
                                <td>{{ $archivoMaestraFCV->created_at }}</td>
                                <td>{{ $archivoMaestraFCV->resultado }}</td>
                                <td >{{ $archivoMaestraFCV->maestraValida? 'válida' : 'con errores' }}</td>
                                <td>
                                    <a href='maestra-productos-fcv/{{$archivoMaestraFCV->idArchivoMaestra}}/descargar'  class="btn btn-primary btn-xs">Descargar</a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class='container-fluid'>
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading" align="center"><span class="glyphicon glyphicon-upload"></span> Subir Nueva Maestra</div>
                <div align="center">
                    <form action="/maestra-productos-fcv/subir-maestra-fcv" method="post" enctype="multipart/form-data">
                        <label>Seleccione Archivo:</label>
                        <input type="file" name="file" id="file">
                        <br><input type="submit" class="btn btn-primary btn-xs" value="Subir Maestra" name="submit"></br>
                        <input type="hidden" value="{{ csrf_token() }}" name="_token">
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
        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading" align="center"><span class="glyphicon glyphicon-download-alt"></span> Descargar Datos</div>
                <div class="panel-body">
                    <div align="center">
                        <form action="" method="post" enctype="multipart/form-data">
                            <td>
                                <label>Descargar dump bd</label>
                                <br>
                                <a aria-haspopup="true" disabled aria-expanded="false" class="btn btn-primary btn-xs">Descargar</a>
                            </td>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            @if($duplicados->count()>0)
                <div class="panel-heading" align="center"><span class="glyphicon glyphicon-duplicate"></span> Su maestra contiene sku duplicados</div>
                <table class="table table-responsive table-hover tablefiles table-bordered">
                    <thead>
                    <th>barra</th>
                    <th>descriptor</th>
                    <th>sku</th>
                    <th>laboratorio</th>
                    <th>clasificacion</th>
                    </thead>
                    @foreach($duplicados as $duplicado)
                        <tr class="warning">
                            <td>{{ $duplicado->barra}}</td>
                            <td>{{ $duplicado->descriptor}}</td>
                            <td>{{ $duplicado->sku }}</td>
                            <td>{{ $duplicado->laboratorio}}</td>
                            <td >{{ $duplicado->clasificacionTerapeutica}}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>

    </div>
</div>