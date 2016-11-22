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
                                <tr>
                                    <td>Fecha</td><td>{{ $fecha }}</td>
                                </tr>
                                <tr>
                                    <td>Organización</td><td>{{ $organizacion }}</td>
                                </tr>
                                <tr>
                                    <td>Líder WOM</td><td>{{ $liderWom }}</td>
                                </tr>
                                <tr>
                                    <td>RUN Líder WOM</td><td>{{ $runLiderWom }}</td>
                                </tr>
                                {{-- LIDER SEI--}}
                                <tr>
                                    <td></td><td></td>
                                </tr>
                                <tr>
                                    <td>Líder SEI</td><td>{{ $liderSei }}</td>
                                </tr>
                                <tr>
                                    <td>RUN Líder SEI</td><td>{{ $runLiderSei  }}</td>
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
                                    <td>Primera Captura</td><td>{{ $primeraCaptura }}</td>
                                </tr>
                                <tr>
                                    <td>Última Captura</td><td>{{ $ultimaCaptura  }}</td>
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
                                    <td>Unidades "Nuevo"</td><td>{{ $unidadesNuevo }}</td>
                                </tr>
                                <tr>
                                    <td>Unidades "En uso"</td><td>{{ $unidadesEnUso }}</td>
                                </tr>
                                <tr>
                                    <td>Unidades "Servicio Técnico"</td><td>{{ $unidadesServicioTecnico }}</td>
                                </tr>
                                <tr>
                                    <td>Total Unidades Contadas</td><td>{{ $unidadesTotal }}</td>
                                </tr>
                                <tr>
                                    <td>Total Patentes</td><td>{{ $patentesTotal  }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- FIRMA WOM --}}
                <div class="col-xs-3 col-xs-offset-2 firma">
                    {{--<img src="" alt="" width="200px" height="200px" class="img-rounded">--}}
                    <div style="margin-top: 100px"></div>
                    <div class="line"></div>
                    <h5><b>WOM</b></h5>
                    <h5>{{ $liderWom }}</h5>
                    <h6>Lider WOM</h6>
                </div>

                {{-- FIRMA SEI--}}
                <div class="col-xs-3 col-xs-offset-2 firma">
                    {{--<img src="" alt="" width="200px" height="200px" class="img-rounded">--}}
                    <div style="margin-top: 100px"></div>
                    <div class="line"></div>
                    <h5><b>Empresa externa</b></h5>
                    <h5>{{ $liderSei }}</h5>
                    <h6>Auditor IG</h6>
                </div>
            </div>
        </div>
    </div>
</body>
</html>