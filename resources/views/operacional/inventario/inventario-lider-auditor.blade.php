@extends('operacional.layoutOperacional')
@section('title', 'Mis inventarios')
@section('content')

    <style>
        .tdfechaprogramada{
            width: 230px !important;
            font-size: 12px;
            text-align: center;
        }
        .th{
            font-size: 12px;
            text-align: center;
        }
        .td {
            font-size: 12px;
            text-align: center;
        }
        .tdopcion{
            font-size: 12px;
            width: 100px;

        }
        .tdopcion > a{
            width: 90%;
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
                                <th class="th">Fecha programada</th>
                                <th class="th">Cliente</th>
                                <th class="th">Ceco</th>
                                <th class="th">Local</th>
                                <th class="th">Región</th>
                                <th class="th">Comuna</th>
                                <th class="th">Dirección</th>
                                <th class="th">Lider</th>
                                <th class="th">Archivo Final</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($inventarios))
                                @foreach($inventarios as $inventario)
                                    <form method="POST" action="">
                                        <input name="_method" type="hidden" value="PUT">
                                    <tr>
                                        <td class="tdfechaprogramada">{{$inventario['inventario']['inventario_fechaProgramadaF']}}</td>
                                        <td class="td">{{$inventario['inventario']['local']['cliente']['nombreCorto']}}</td>
                                        <td class="td">{{$inventario['inventario']['local']['numero']}}</td>
                                        <td class="td">{{$inventario['inventario']['local']['nombre']}}</td>
                                        <td class="td">{{$inventario['inventario']['local']['region_numero']}}</td>
                                        <td class="td">{{$inventario['inventario']['local']['comuna_nombre']}}</td>
                                        <td class="td">{{$inventario['inventario']['local']['direccion']}}</td>
                                        <td class="td">{{$inventario['nombreLider']}}</td>
                                        <td class="tdopcion">
                                            <a href='/inventario/{{$inventario['inventario']['idInventario']}}/archivo-final' class="btn btn-primary btn-xs center-block">Ver</a>
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