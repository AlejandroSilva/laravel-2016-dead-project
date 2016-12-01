<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Acta WOM</title>
    <link rel='stylesheet' href='/vendor/bootstrap/bootstrap.min.css'>

    <style>
        h1{
            padding: 40px;
        }
        .tablaDatos {
            font-size: 12px;
        }
        .tablaDatos td {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        .firma {
            text-align: center;
        }
        .firma > .line{
            border-top: 2px solid black;
            width: 100%;
        }

    </style>
</head>
<body>
    <div class='container'>
        <div class="col-xs-10 col-xs-offset-1">

            <div class="row">
                <h1>Acta de Auditoria IG WOM</h1>

                {{-- SUPERIOR IZQUIERDO--}}
                <div class="col-xs-6">
                    <div class="panel panel-default">
                        <table class="table table-compact table-striped tablaDatos">
                            <tbody>
                                {{--<tr>--}}
                                    {{--<td>Fecha</td><td> $fecha }}</td>--}}
                                {{--</tr>--}}
                                <tr>
                                    <td>Organización</td><td>{{ $archivo->organizacion }}</td>
                                </tr>
                                <tr>
                                    <td>Líder WOM</td><td>{{ $archivo->liderWom }}</td>
                                </tr>
                                <tr>
                                    <td>RUN Líder WOM</td><td>{{ $archivo->runLiderWom }}</td>
                                </tr>
                                {{-- LIDER SEI--}}
                                <tr>
                                    <td></td><td></td>
                                </tr>
                                <tr>
                                    <td>Líder SEI</td><td>{{ $archivo->liderSei }}</td>
                                </tr>
                                <tr>
                                    <td>RUN Líder SEI</td><td>{{ $archivo->runLiderSei  }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{--SUPERIOR DERECHO--}}
                <div class="col-xs-6">
                    <div class="panel panel-default">
                        <table class="table table-compact table-striped tablaDatos">
                            <tbody>
                                <tr>
                                    <td>Duración</td><td>{{ $archivo->tiempoTranscurrido }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                {{--<div class="col-xs-10 col-xs-offset-1">--}}
                <div class="col-xs-12">
                    <div class="panel panel-default">
                        <table class="table table-compact tablaDatos">
                            <tbody>
                                <tr>
                                    <td>Unidades "Nuevo"</td><td>{{ $archivo->unidadesNuevo }}</td>
                                </tr>
                                <tr>
                                    <td>Unidades "En uso"</td><td>{{ $archivo->unidadesUsado }}</td>
                                </tr>
                                <tr>
                                    <td>Unidades "En Prestamo"</td><td>{{ $archivo->unidadesPrestamo }}</td>
                                </tr>
                                {{--<tr>--}}
                                    {{--<td>Unidades "Servicio Técnico"</td><td>{{ $archivo->unidadesServTecnico }}</td>--}}
                                {{--</tr>--}}
                                <tr>
                                    <td>Total Unidades Contadas</td><td>{{ $archivo->unidadesContadas }}</td>
                                </tr>
                                <tr>
                                    <td>Total Patentes</td><td>{{ $archivo->pttTotal }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- FIRMA WOM --}}
                <div class="col-xs-4 col-xs-offset-1 firma">
                    {{--<img src="" alt="" width="200px" height="200px" class="img-rounded">--}}
                    <div style="margin-top: 100px"></div>
                    <div class="line"></div>
                    <h5><b>WOM</b></h5>
                    <h5>{{ $archivo->liderWom }}</h5>
                    <h5>{{ $archivo->runLiderWom }}</h5>
                    <h6>Líder WOM</h6>
                </div>

                {{-- FIRMA SEI--}}
                <div class="col-xs-4 col-xs-offset-2 firma">
                    {{--<img src="" alt="" width="200px" height="200px" class="img-rounded">--}}
                    <div style="margin-top: 100px"></div>
                    <div class="line"></div>
                    <h5><b>SEI Consultores</b></h5>
                    <h5>{{ $archivo->liderSei }}</h5>
                    <h5>{{ $archivo->runLiderSei }}</h5>
                    <h6>Auditor IG</h6>
                </div>
            </div>
        </div>
    </div>
</body>
</html>