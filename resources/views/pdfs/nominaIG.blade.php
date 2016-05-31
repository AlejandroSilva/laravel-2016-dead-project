{{--layout/twocolumns--}}
@extends('layouts.root')

@section('hmtl-body')
    <style>
        .panelHeading_compacto {
            padding-top: 2px !important;
            padding-bottom: 2px !important;
        }
        /* ######## Paneles de Informacion ########*/
        .panelDatos_heading{
            padding-top: 2px !important;
            padding-bottom: 2px !important;
        }
        .tablaDatos {
            font-size: 12px;
        }
        .tablaDatos td {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        /* ############# Dotación Seleccionada #############*/
        .tablaDotacion {
            table-layout: fixed;
            font-size: 12px;
        }
        .tablaDotacion td {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        /* Correlativo */
        .colCorrelativo{
            width: 25px;
        }
        /* Usuario RUN */
        .colUsuarioRUN {
            width: 100px;;
        }
        /* Nombre */
        .colNombre {
            width: auto;
        }
        .colCargo {
            /*width: 80px;*/
            width: 80px;
        }
        .imgLider{
            margin: 0 auto;
            padding: 2px;
            max-width: 85px !important;
        }
    </style>
    <div class='container'>
        <div class="row">
            <div class="col-xs-12">
                {{-- Datos Inventario --}}
                <div class="panel panel-default">
                    <div class="panel-heading panelDatos_heading">Inventario</div>
                    <table class="table table-compact table-striped tablaDatos">
                        <tbody>
                        <tr>
                            <td>Cliente</td><td><b>{{ $inventario->local->cliente->nombreCorto }}</b></td>
                            <td>Dotación Operadores</td><td>{{ $nomina->dotacionOperadores }}</td>
                        </tr>
                        <tr>
                            <td>Local</td><td><b>({{$inventario->local->numero}}) {{$inventario->local->nombre}}</b></td>
                            <td>Dotación Total</td><td>{{ $nomina->dotacionTotal }}</td>
                        </tr>
                        <tr>
                            <td>Fecha programada</td><td><b>{{$inventario->fechaProgramadaF()}}</b></td>
                            <td></td><td></td>
                        </tr>
                        <tr>
                            <td>Hr. llegada lider</td><td>{{$nomina->horaPresentacionLiderF()}}</td>
                            <td></td><td></td>
                        </tr>
                        <tr>
                            <td>Hr. llegada equipo</td><td>{{$nomina->horaPresentacionEquipoF()}}</td>
                            <td></td><td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-xs-12">
                {{-- Datos Local --}}
                <div class="panel panel-default">
                    <div class="panel-heading panelDatos_heading">Local</div>
                    <table class="table table-compact tablaDatos">
                        <tbody>
                        <tr>
                            <td>Dirección</td><td>{{ $inventario->local->direccion->direccion }}</td>
                            <td>Hr.Apertura</td><td>{{ $inventario->local->horaAperturaF() }}</td></tr>
                        <tr>
                            <td>Comuna</td><td>{{ $inventario->local->direccion->comuna->nombre }}</td>
                            <td>Hr.Cierre</td><td>{{ $inventario->local->horaCierreF() }}</td></tr>
                        <tr>
                            <td>Región</td><td>{{ $inventario->local->direccion->comuna->provincia->region->numero }}</td>
                            <td>Teléfono 1</td><td>{{ $inventario->local->codArea1." ".$inventario->local->telefono1 }}</td>
                        </tr>
                        <tr>
                            <td>Formato Local</td><td>{{ $inventario->local->formatoLocal->nombre  }}</td>
                            <td>Teléfono 2</td><td>{{ $inventario->local->codArea2." ".$inventario->local->telefono2 }}</td>
                        </tr>
                        <tr>
                            <td></td><td></td>
                            <td>Correo</td><td>{{ $inventario->local->emailContacto }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Dotacion --}}
            <div class="col-xs-8">

                {{--Dotacion titular--}}
                <div class="panel panel-primary">
                    <div class="panel-heading panelHeading_compacto">Personal Asignado</div>
                    <table class="table table-striped table-bordered table-hover table-condensed tablaDotacion">
                        <colgroup>
                            <col class="colCorrelativo">
                            <col class="colUsuarioRUN">
                            <col class="colNombre">
                            <col class="colCargo">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>N°</th><th>RUN</th><th>Nombre</th><th>Cargo</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($lider))
                            <tr>
                                <td>L</td>
                                <td>{{ $lider->usuarioRUN."-".$lider->usuarioDV }}</td>
                                <td>{{ $lider->nombreCompleto() }}</td>
                                <td>Lider</td>
                            </tr>
                        @endif
                        @if(isset($supervisor))
                            <tr>
                                <td>S</td>
                                <td>{{ $supervisor->usuarioRUN."-".$supervisor->usuarioDV }}</td>
                                <td>{{ $supervisor->nombreCompleto() }}</td>
                                <td>Supervisor</td>
                            </tr>
                        @endif
                        @for($i=0; $i<sizeof($dotacionTitular); $i++)
                            <tr>
                                <td>{{$i+1}}</td>
                                <td>{{ $dotacionTitular[$i]->usuarioRUN."-".$dotacionTitular[$i]->usuarioDV }}</td>
                                <td>{{ $dotacionTitular[$i]->nombreCompleto() }}</td>
                                <td>Operador</td>
                            </tr>
                        @endfor
                        </tbody>
                    </table>
                </div>

                {{-- Dotacion Reemplazo--}}
                @if( sizeof($dotacionReemplazo)>0 )
                    <div class="panel panel-primary">
                        <div class="panel-heading panelHeading_compacto">Personal Reemplazo</div>
                        <table class="table table-striped table-bordered table-hover table-condensed tablaDotacion">
                            <colgroup>
                                <col class="colCorrelativo">
                                <col class="colUsuarioRUN">
                                <col class="colNombre">
                                <col class="colCargo">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>#</th><th>RUN</th><th>Nombre</th><th>Cargo</th>
                            </tr>
                            </thead>
                            <tbody>
                            @for($i=0; $i<sizeof($dotacionReemplazo); $i++)
                                <tr>
                                    <td>{{$i+1}}</td>
                                    <td>{{ $dotacionReemplazo[$i]->usuarioRUN."-".$dotacionReemplazo[$i]->usuarioDV }}</td>
                                    <td>{{ $dotacionReemplazo[$i]->nombreCompleto() }}</td>
                                    <td>Operador</td>
                                </tr>
                            @endfor
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
            {{-- Imagen Lider --}}
            @if( isset($lider) )
                @if( $lider->imagenPerfil!='' )
                    <div class="col-xs-2 col-xs-offset-1">
                        <div class="panel panel-primary">
                            <div class="panel-heading panelHeading_compacto">Lider</div>
                            <img class="img-responsive imgLider"
                                 src="/imagenPerfil/{{ $lider->imagenPerfil }}" alt="">
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
@stop