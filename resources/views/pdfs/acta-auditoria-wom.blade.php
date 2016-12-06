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
        .td-50 {
            width: 60%;
        }
        .td-75 {
            width: 70%;
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
                <h1>Acta de Auditoría IG WOM</h1>
                <div class="col-xs-12">
                    <h4>Datos generales</h4>
                </div>
                {{-- SUPERIOR IZQUIERDO--}}
                <div class="col-xs-12">
                    <div class="panel panel-default">
                        <table class="table table-compact table-striped tablaDatos">
                            <tbody>
                                <tr>
                                    <td class="td-50">Organización</td><td>{{ $archivo->organizacion }}</td>
                                </tr>
                                <tr>
                                    <td class="td-50">Líder WOM</td><td>{{ $archivo->liderWom }}</td>
                                </tr>
                                <tr>
                                    <td class="td-50">RUN Líder WOM</td><td>{{ $archivo->runLiderWom }}</td>
                                </tr>
                                {{-- LIDER SEI--}}
                                <tr>
                                    <td></td><td></td>
                                </tr>
                                <tr>
                                    <td class="td-50">Líder SEI</td><td>{{ $archivo->liderSei }}</td>
                                </tr>
                                <tr>
                                    <td class="td-50">RUN Líder SEI</td><td>{{ $archivo->runLiderSei  }}</td>
                                </tr>
                                <tr>
                                    <td class="td-50">Duración</td><td>{{ $archivo->tiempoTranscurrido }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Conteo --}}
            <div class="row">
                <div class="col-xs-12">
                    <h4>Resumen conteo</h4>
                    <div class="panel panel-default">
                        <table class="table table-compact tablaDatos">
                            <tbody>
                                <tr>
                                    <td class="td-75">Unidades "Nuevo"</td><td>{{ $archivo->unidadesNuevo }}</td>
                                </tr>
                                <tr>
                                    <td class="td-75">Unidades "En uso"</td><td>{{ $archivo->unidadesUsado }}</td>
                                </tr>
                                <tr>
                                    <td class="td-75">Unidades "En Préstamo"</td><td>{{ $archivo->unidadesPrestamo }}</td>
                                </tr>
                                <tr>
                                    <td class="td-75">Total Unidades Contadas</td><td>{{ $archivo->unidadesContadas }}</td>
                                </tr>
                                <tr>
                                    <td class="td-75">Total Patentes</td><td>{{ $archivo->pttTotal }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Checklist --}}
            <div class="row">
                <div class="col-xs-12">
                    <h4>Checklist</h4>
                    <div class="panel panel-default">
                        <table class="table table-compact tablaDatos">
                            <tbody>
                            <tr>
                                <td class="td-75">Identifico todos sectores</td><td>{{ $archivo->identificoTodosLosSectores }}</td>
                            </tr>
                            <tr>
                                <td class="td-75">Identifico ESTADO de teléfonos</td><td>{{ $archivo->identificoEstadoDeTelefonos }}</td>
                            </tr>
                            <tr>
                                <td class="td-75">Identifico cajas SIM abiertas</td><td>{{ $archivo->identificoCajasSIMAbiertas }}</td>
                            </tr>
                            <tr>
                                <td class="td-75">Presenta ordenado sus productos</td><td>{{ $archivo->presentaOrdenadoSusProductos }}</td>
                            </tr>
                            <tr>
                                <td class="td-75">Se realizó segundo conteo a teléfonos</td><td>{{ $archivo->seRealizoSegundoConteATelefonos }}</td>
                            </tr>
                            <tr>
                                <td class="td-75">Escaneo cajas abiertas de SIM uno a uno</td><td>{{ $archivo->escaneoCajasAbiertasSIMUnoAUno }}</td>
                            </tr>
                            <tr>
                                <td class="td-75">Tiene buena disposición y explica AI</td><td>{{ $archivo->tieneBuenaDisposicionYExplica }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Evaluación --}}
            <div class="row">
                <div class="col-xs-12">
                    <h4>Evaluación</h4>
                    <div class="panel panel-default">
                        <table class="table table-compact tablaDatos">
                            <tbody>
                            <tr>
                                <td class="td-75">Evaluación</td><td>{{ $archivo->evaluacionAServicioSEI }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- FIRMA WOM --}}
                <div class="col-xs-4 col-xs-offset-1 firma">
                    <img src="{{$firmaWom}}" alt="" width="150px" height="150px" class="img-rounded">
                    <div class="line"></div>
                    <h5><b>WOM</b></h5>
                    <h5>{{ $archivo->liderWom }}</h5>
                    <h5>{{ $archivo->runLiderWom }}</h5>
                    <h6>Líder WOM</h6>
                </div>

                {{-- FIRMA SEI--}}
                <div class="col-xs-4 col-xs-offset-2 firma">
                    <img src="{{$firmaSei}}" alt="" width="150px" height="150px" class="img-rounded">
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