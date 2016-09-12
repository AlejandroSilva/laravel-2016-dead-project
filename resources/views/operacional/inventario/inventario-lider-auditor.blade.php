@extends('operacional.layoutOperacional')
@section('title', 'Mis inventarios')
@section('content')

    <style>

        .tdfechaprogramada{
            width: 100px !important;
            font-size: 18px;
            text-align: center;

        }
        .thfechaInventario{
            font-size: 20px;
            text-align: center;
        }
        .td {
            font-size: 15px;
            text-align: center;
        }
        .thOpcion{
            font-size: 20px;
            text-align: center;
        }
        .tdopcion{
            font-size: 10px;
            width: 180px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdopcion > a{
            width: 120px;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4><span class="glyphicon glyphicon-calendar"></span> Mis inventarios</h4>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-hover table-condensed">
                            <thead>
                            <tr>
                                <th class="thidInventario">Fecha programada</th>
                                <th class="thfechaInventario">Cliente</th>
                                <th class="thfechaInventario">Ceco</th>
                                <th class="thfechaInventario">Región</th>
                                <th class="thfechaInventario">Comuna</th>
                                <th class="thfechaInventario">Lider</th>
                                <th class="thOpcion">Archivo Final</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($inventarios))
                                @foreach($inventarios as $inventario)
                                    <form method="POST" action="">
                                        <input name="_method" type="hidden" value="PUT">
                                    <tr>
                                        <td class="tdfechaprogramada">{{ $inventario->fechaProgramada}}</td>
                                        <td class="td">{{$inventario->local->cliente->nombreCorto}}</td>
                                        <td class="td">{{$inventario->local->numero}}</td>
                                        <td class="td">{{$inventario->local->region_numero}}</td>
                                        <td class="td">{{$inventario->local->comuna_nombre}}</td>
                                        <td class="td">Lider</td>
                                        <td class="tdopcion">
                                            <a href='/inventario/{{$inventario->idInventario}}/archivo-final' class="btn btn-default center-block">Ver</a>
                                            {{--<button href="hola" type="button" class="btn btn-default center-block">Dato</button>--}}
                                        </td>
                                    </tr>
                                    </form>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop