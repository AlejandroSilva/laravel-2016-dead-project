@extends('layouts.unacolumna')
<style>
    .td-opciones .btn {
        width: 110px;
        text-align: left;
    }
    .texto-centrado{
        text-align: center;
    }
    .descargar-consolidado-link{
        width: 100%;
        margin: 5px 0;
        text-align: left !important;
    }
</style>

<div class="container">

    <h1>Archivos finales de inventarios FCV</h1>

    <div class="row">
        <div class="col-sm-3">

            {{-- PANEL BUSQUEDA --}}
            <div class="panel panel-default">
                <div class="panel-heading">
                    Buscar Inventarios
                </div>
                <div class="panel-body">
                    <form action="/programacionIG/archivos-finales-fcv" method="GET">
                        {{-- Numero de local --}}
                        <div class="form-group">
                            <label>Numero de Local</label>
                            <input class="form-control" name="ceco" placeholder="Numero de local" value={{$cecoBuscado}}>
                        </div>
                        {{-- Fecha programada --}}
                        <div class="form-group">
                            <label>Mes</label>
                            <select name="mes" class="form-control">
                                <option value="">Todos</option>
                                @foreach($mesesFechaProgramada as $mes)
                                    <option value="{{$mes['value']}}" {{$mesBuscado==$mes['value']? 'selected' : ''}}>
                                        {{$mes['text']}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Incluir inventarios sin archivo final--}}
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="incluirPendientes" {{$incluirPendientes? 'checked="selected"' : ''}}/>
                                incluir inventarios pendientes
                            </label>
                        </div>
                        {{-- Buscar --}}
                        <span class="input-group-btn">
                            <input type="submit" class="btn btn-primary btn-sm btn-block" value="Buscar"/>
                        </span>
                    </form>
                </div>
            </div>

            {{-- PANEL CONSOLIDADOS --}}
            <div class="panel panel-default">
                <div class="panel-heading">
                    Consolidado de Inventarios
                </div>
                <div class="panel-body">
                    @foreach($mesesConsolidados as $mes)
                        <a class="btn btn-primary descargar-consolidado-link"
                           href="/inventario/descargar-consolidado-fcv?mes={{$mes['value']}}&orden=desc">
                            <span class="glyphicon glyphicon-download-alt"></span>
                            Descargar {{$mes['text']}}
                        </a>
                    @endforeach
                </div>
            </div>

        </div>
        {{-- TABLA DE INVENTARIOS --}}
        <div class="col-sm-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Inventarios
                </div>
                <table class="table table-bordered table-hover table-condensed tabla-nominas">
                    <thead>
                    <tr>
                        <th class="th">Fecha programada</th>
                        <th class="th">Ceco</th>
                        <th class="th">Local</th>
                        <th class="th">Archivo Final</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if( count($inventarios)>0 )
                        @foreach($inventarios as $inv)
                            @php( $acta = $inv->actaFCV )
                            @php( $datosDisponibles = $acta && $acta->estaPublicada())
                            <tr>
                                <td>{{ $inv->fechaProgramadaF() }}</td>
                                <td>{{ $inv->local->numero }}</td>
                                <td>{{ $inv->local->nombre }}</td>
                                <td class="td-opciones">
                                    @if($inv->actaFCV && $datosDisponibles==true)
                                        <a class="btn btn-primary btn-xs" href='archivo-final-inventario/{{$inv->actaFCV->idArchivoFinalInventario}}/descargar'>
                                            <span class="glyphicon glyphicon-download-alt"></span>
                                            Descargar ZIP
                                        </a>
                                    @else
                                        <a class="btn btn-success btn-xs" href='/inventario/{{$inv->idInventario}}/archivo-final'>
                                            <span class="glyphicon glyphicon-plus"></span>
                                            Agregar
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="texto-centrado">
                                Sin inventarios {{ $cecoBuscado!=''? "para el local ".$cecoBuscado : '' }} en este periodo
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>