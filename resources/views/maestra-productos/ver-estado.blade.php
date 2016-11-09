@extends('layouts.root')
@section('title', 'Maestra Productos FCV')

@section('body')
    @php( $barrasDuplicadas = $archivoMaestra->getBarrasDuplicadas() )
    @php( $camposVacios = $archivoMaestra->getCamposVacios() )
    @php( $descriptoresDistintos = $archivoMaestra->getDescriptoresDistintos() )
    @php( $laboratoriosDistintos = $archivoMaestra->getLaboratoriosDistintos() )
    @php( $clasificacionesDistintas = $archivoMaestra->getClasificacionesDistintas() )

    <div class="container">
        @include('maestra-productos.header-maestra')

        {{-- Archivos enviados--}}
        <div class="row">
            <div class="col-xs-12">
                {{-- Datos generales --}}
                <div class="panel panel-primary">
                    <div class="panel-heading" align="center">
                        Datos
                    </div>
                    <table class="table table-responsive table-hover tabla-archivos table-bordered">
                        <tbody>
                            <tr><td>Archivo</td><td>{{ $archivoMaestra->nombreOriginal }}</td></tr>
                            <tr><td>Subido Por</td><td>{{ $archivoMaestra->subidoPor? $archivoMaestra->subidoPor->nombreCorto() : '-' }}</td></tr>
                            <tr><td>Fecha Subida</td><td>{{ $archivoMaestra->created_at }}</td></tr>
                            <tr><td>Total productos</td><td>{{ $archivoMaestra->getTotalProductos(true) }}</td></tr>
                            <tr>
                                <td>Estado</td>
                                <td class="{{$archivoMaestra->maestraValida? 'success' : 'danger'}}">
                                    {{ $archivoMaestra->resultado }}
                                    <form action="/maestra-fcv/{{$archivoMaestra->idArchivoMaestra}}/actualizar-estado" method="post">
                                        <input type="hidden" value="{{ csrf_token() }}" name="_token">
                                        <input type="submit" class="btn btn-success btn-xs" value="actualizar estado">
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Barras DUPLICADOS --}}
                <div class="panel {{ $barrasDuplicadas->total > 0? 'panel-danger' : 'panel-default' }}">
                    <div class="panel-heading" align="center">
                        Barras duplicadas
                    </div>
                    <table class="table table-responsive table-hover table-bordered">
                        @if( $barrasDuplicadas->total > 0 )
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>sku</th>
                                    <th>barra</th>
                                    <th>descriptor</th>
                                    <th>laboratorio</th>
                                    <th>clasificacion Terapeutica</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $barrasDuplicadas->productos as $producto)
                                    <tr>
                                        <td>{{ $producto->idProductoFCV }}</td>
                                        <td>{{ $producto->sku }}</td>
                                        <td class="danger">{{ $producto->barra }}</td>
                                        <td>{{ $producto->descriptor }}</td>
                                        <td>{{ $producto->laboratorio }}</td>
                                        <td>{{ $producto->clasificacionTerapeutica }}</td>
                                    </tr>
                                @endforeach
                        </tbody>
                        @else
                            <tbody>
                                <tr>
                                    <td colspan=6 style="text-align: center">Sin productos con problemas</td>
                                </tr>
                            </tbody>
                        @endif
                    </table>
                </div>

                {{-- CAMPOS VACIOS --}}
                <div class="panel {{ $camposVacios->total > 0? 'panel-danger' : 'panel-default' }}">
                    <div class="panel-heading" align="center">
                        Datos faltantes / Campos vacios
                    </div>
                    <table class="table table-responsive table-hover table-bordered">
                        @if($camposVacios->total > 0)
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>sku</th>
                                    <th>barra</th>
                                    <th>descriptor</th>
                                    <th>laboratorio</th>
                                    <th>clasificacion Terapeutica</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $camposVacios->productos as $producto)
                                    <tr>
                                        <td>{{ $producto->idProductoFCV }}</td>
                                        <td class="{{ $producto->sku==''? 'danger' :'' }}">{{ $producto->sku }}</td>
                                        <td class="{{ $producto->barra==''? 'danger' :'' }}">{{ $producto->barra }}</td>
                                        <td class="{{ $producto->descriptor==''? 'danger' :'' }}">{{ $producto->descriptor }}</td>
                                        <td class="{{ $producto->laboratorio==''? 'danger' :'' }}">{{ $producto->laboratorio }}</td>
                                        <td class="{{ $producto->clasificacionTerapeutica==''? 'danger' :'' }}">{{ $producto->clasificacionTerapeutica }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @else
                            <tbody>
                                <tr>
                                    <td colspan=6 style="text-align: center">Sin productos con problemas</td>
                                </tr>
                            </tbody>
                        @endif
                    </table>
                </div>

                {{-- DESCRIPTORES DISTINTOS --}}
                <div class="panel {{ $descriptoresDistintos->total > 0? 'panel-danger' : 'panel-default' }}">
                    <div class="panel-heading" align="center">
                        Descriptores distintos para el mismo SKU
                    </div>
                    <table class="table table-responsive table-hover table-bordered">
                        @if($descriptoresDistintos->total > 0)
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>sku</th>
                                    <th>barra</th>
                                    <th>descriptor</th>
                                    <th>laboratorio</th>
                                    <th>clasificacion Terapeutica</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $descriptoresDistintos->productos as $producto)
                                    <tr>
                                        <td>{{ $producto->idProductoFCV }}</td>
                                        <td>{{ $producto->sku }}</td>
                                        <td>{{ $producto->barra }}</td>
                                        <td class="danger">{{ $producto->descriptor }}</td>
                                        <td>{{ $producto->laboratorio }}</td>
                                        <td>{{ $producto->clasificacionTerapeutica }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @else
                            <tbody>
                                <tr>
                                    <td colspan=6 style="text-align: center">Sin productos con problemas</td>
                                </tr>
                            </tbody>
                        @endif
                    </table>
                </div>

                {{-- LABORATORIOS DISTINTOS --}}
                <div class="panel {{ $laboratoriosDistintos->total >0? 'panel-danger' : 'panel-default' }}">
                    <div class="panel-heading" align="center">
                        Laboratorios distintos para el mismo SKU
                    </div>
                    <table class="table table-responsive table-hover table-bordered">
                        @if( $laboratoriosDistintos->total >0 )
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>sku</th>
                                    <th>barra</th>
                                    <th>descriptor</th>
                                    <th>laboratorio</th>
                                    <th>clasificacion Terapeutica</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $laboratoriosDistintos->productos as $producto)
                                    <tr>
                                        <td>{{ $producto->idProductoFCV }}</td>
                                        <td>{{ $producto->sku }}</td>
                                        <td>{{ $producto->barra }}</td>
                                        <td>{{ $producto->descriptor }}</td>
                                        <td class="danger">{{ $producto->laboratorio }}</td>
                                        <td>{{ $producto->clasificacionTerapeutica }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @else
                            <tbody>
                                <tr>
                                    <td colspan=6 style="text-align: center">Sin productos con problemas</td>
                                </tr>
                            </tbody>
                        @endif
                    </table>
                </div>

                {{-- CLASIFICACIONES DISTINTAS --}}
                <div class="panel {{ $clasificacionesDistintas->total >0? 'panel-danger' : 'panel-default' }}">
                    <div class="panel-heading" align="center">
                        Clasificaciones terapeuticas distintas para el mismo SKU
                    </div>
                    <table class="table table-responsive table-hover table-bordered">
                        @if( $clasificacionesDistintas->total >0 )
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>sku</th>
                                <th>barra</th>
                                <th>descriptor</th>
                                <th>laboratorio</th>
                                <th>clasificacion Terapeutica</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach( $clasificacionesDistintas->productos as $producto)
                                <tr>
                                    <td>{{ $producto->idProductoFCV }}</td>
                                    <td>{{ $producto->sku }}</td>
                                    <td>{{ $producto->barra }}</td>
                                    <td>{{ $producto->descriptor }}</td>
                                    <td>{{ $producto->laboratorio }}</td>
                                    <td class="danger">{{ $producto->clasificacionTerapeutica }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        @else
                            <tbody>
                            <tr>
                                <td colspan=6 style="text-align: center">Sin productos con problemas</td>
                            </tr>
                            </tbody>
                        @endif
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection