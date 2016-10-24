@extends('layouts.root')
@section('title', 'Maestra Productos FCV')

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
        {{-- Archivos enviados--}}
        <div class="row">
            <div class="col-xs-12">
                <h1>Maestra de productos FCV</h1>

                <div class="panel panel-primary">
                <div class="panel-heading" align="center"><span class="glyphicon glyphicon-folder-close"></span> Archivos enviados</div>
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
                        @foreach($archivosMaestraProductos as $archivo)
                            <tr class="{{ $archivo->maestraValida? 'success' : 'warning' }}">
                                <td class="col-id">{{ $archivo->idArchivoMaestra }}</td>
                                <td>{{ $archivo->nombreOriginal }}</td>
                                <td>{{ $archivo->subidoPor? $archivo->subidoPor->nombreCorto() : '-' }}</td>
                                <td>{{ $archivo->created_at }}</td>
                                <td>{{ $archivo->resultado }}</td>
                                <td>{{ $archivo->maestraValida? 'válida' : 'con errores' }}</td>
                                <td>{{ count($archivo->getProductosDuplicados()) }}</td>
                                <td>{{ count($archivo->getProductosInvalidos()) }}</td>
                                <td>
                                    <a class="btn btn-primary btn-xs" href='maestra-productos/{{$archivo->idArchivoMaestra}}/descargar-fcv'>
                                        Descargar
                                    </a>
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
            {{-- SUBIR MAESTRA --}}
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading" align="center"><span class="glyphicon glyphicon-upload"></span> Subir maestra de productos</div>
                    <div class="panel-body" align="center">
                        <form class="form-horizontal" action="/maestra-productos/subir-maestra-fcv" method="post" enctype="multipart/form-data">
                            <input type="hidden" value="{{ csrf_token() }}" name="_token">
                            <div class="col-xs-12">
                                <input class="form-control" type="file" name="file" {{$puedeSubirArchivo? '' : 'disabled'}}>
                            </div>
                            <div class="col-xs-12">
                                <input type="submit" class="btn btn-primary btn-sm btn-block" {{$puedeSubirArchivo? '' : 'disabled'}}
                                       value="Enviar" name="submit"
                                >
                            </div>

                            @if(session('mensaje-exito'))
                                <br><div class="col-sm-12">
                                    <div class="alert alert-success" role="alert">
                                        {{session('mensaje-exito')}}
                                    </div>
                                </div></br>
                            @endif
                            @if(session('mensaje-error'))
                                <br><div class="col-sm-12">
                                <div class="alert alert-danger" role="alert">
                                    {{session('mensaje-error')}}
                                </div>
                                </div></br>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection