@extends('operacional.layoutOperacional')
@section('title', 'Indicadores')

@section('content')
    <style>
        .tabla-indicadores {
            font-size: 12px;
        }
    </style>

    <div class="row">
        <div class="container-fluid">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <span class="glyphicon glyphicon-stats"></span> Indicadores</div>
                    <div class="panel-body">
                        <table class="table table-bordered table-hover table-condensed tabla-indicadores">
                            <thead>
                            <tr>
                                <th class="th">Local</th>
                                <th class="th">Líder</th>
                                <th class="th">Items</th>
                                <th class="th">Horas IG</th>
                                <th class="th">Items HH</th>
                                <th class="th">Nota Prom</th>
                                <th class="th">%Error SEI</th>
                                <th class="th">Items Rev CII</th>
                                <th class="th">%Rev CII</th>
                                <th class="th">Ptt Rev CII</th>
                                <th class="th">Dif Estand</th>
                                <th class="th">Archivo</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>ddd</td>
                                    <td>ddd</td>
                                    <td>ddd</td>
                                    <td>ddd</td>
                                </tr>
                            </tbody>
                        </table>





                    </div>
                </div>
            </div>
        </div>
    </div>

@stop