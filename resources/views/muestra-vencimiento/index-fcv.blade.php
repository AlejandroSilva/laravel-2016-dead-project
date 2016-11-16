@extends('layouts.root')
@section('title', 'Muestras de vencimiento FCV')

@section('body')
    <style  type="text/css">
        .container-tabla-archivos{
            max-height: 400px;
            overflow: overlay;
        }
        .tabla-archivos {
            font-size: 12px;
            padding: 0 !important;
            text-align: center;
        }
        .tabla-archivos .col-id{
            width: 20px;
        }
        .tabla-archivos th {
            text-align: center;
            padding: 0 !important;
        }
        .tabla-archivos td {
            text-align: center;
            padding-top: 1px ;
            padding-bottom: 1px ;
        }
    </style>

    <div class="container">
        <div class="row">
            <h1>Muestras de vencimiento, auditorias FCV</h1>

            {{-- archivos subidos --}}
            <div class="panel panel-primary">
                <div class="panel-heading" align="center" style="padding:0">
                    <span class="glyphicon glyphicon-folder-close"></span> Muestras de Vencimiento</div>
                <div class="container-tabla-archivos">
                    <table class="table table-responsive table-hover tabla-archivos table-bordered">
                        <thead>
                        <th class="col-id">ID</th>
                        <th>Nombre archivo</th>
                        <th>Subido por</th>
                        <th>Fecha y hora subida</th>
                        <th>Estado</th>
                        <th>Válida</th>
                        <th>Duplicados</th>
                        <th>Invalidos</th>
                        <th>Opciones</th>
                        </thead>
                        <tbody>
                        @foreach($archivosMuestrasVencimiento as $archivo)
                            <tr class="{{ $archivo->maestraValida? 'success' : 'warning' }}">
                                <td class="col-id">{{ $archivo->idArchivoMuestraVencimientoFCV}}</td>
                                <td>{{ $archivo->nombreOriginal }}</td>
                                <td>{{ $archivo->subidoPor? $archivo->subidoPor->nombreCorto() : '-' }}</td>
                                <td>{{ $archivo->created_at }}</td>
                                <td>{{ $archivo->resultado }}</td>
                                <td>{{ $archivo->maestraValida? 'válida' : 'con errores' }}</td>
                                <td> $archivo->getBarrasDuplicadas()->total </td>
                                <td> $archivo->getCamposVacios()->total </td>
                                <td>
                                    <a class="btn btn-default btn-xs" href='maestra-fcv/{{$archivo->idArchivoMaestra}}/ver-estado'>
                                        Ver estado
                                    </a>
                                    <a class="btn btn-default btn-xs" href='maestra-fcv/{{$archivo->idArchivoMaestra}}/actualizar-maestra'>
                                        Actualizar
                                    </a>
                                    {{-- mostrar link de descarga solo si la maestra es "valida" --}}
                                    <a class="btn btn-primary btn-xs"
                                       {{ $archivo->maestraValida? '' : "disabled=true" }}
                                       href="{{ $archivo->maestraValida? "maestra-fcv/$archivo->idArchivoMaestra/descargar-db" : "#" }}">
                                        Descargar
                                    </a>
                                    <a class="btn btn-default btn-xs" href='maestra-fcv/{{$archivo->idArchivoMaestra}}/descargar-original'>
                                        original
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- SUBIR ARCHIVO MUESTRA VENCIMIENTO --}}
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading" align="center">
                        <span class="glyphicon glyphicon-upload"></span> Subir muestra de vencimiento
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" action="/muestra-vencimiento-fcv/subir-muestra" method="post" enctype="multipart/form-data">
                            <input type="hidden" value="{{ csrf_token() }}" name="_token">

                            <div class="col-xs-12">
                                <input class="form-control" type="file" name="muestraVencimiento">
                            </div>

                            <div class="col-xs-12">
                                <input type="submit" class="btn btn-primary btn-sm btn-block" {{$puedeSubirArchivo? '' : 'disabled'}}
                                        value="Enviar" name="submit"
                                >
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
        </div>
    </div>
@endsection