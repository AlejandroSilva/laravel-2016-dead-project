@extends('layouts.unacolumna')
<style  type="text/css">

    .tabledatos {
        font-size: 1px;
        padding: 0px !important;
    }

    .thstyle{
        border-left: 8px solid #ddd !important;
    }

    .tabledatos td {
        text-align: center;
        padding: 0px !important;

    }

    .tabledatos th {
        text-align: center;
        padding: 0px !important;
    }

    .tablefiles {
        font-size: 1px;
        padding: 0px !important;
        text-align: center;
    }

    .tablefiles th {
        text-align: center;
        font-size: 1px;
        width: 70px;
        padding: 0px !important;
    }

    .tablefiles td {
        text-align: center;
        padding: 0px !important;
    }

    .panel-body {
        padding: 0px !important;
    }

</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading" align="center">
                    <span class="glyphicon glyphicon-stats"></span>
                    Acta Inventario
                    <div class="btn-group pull-right">
                    <button class="btn btn-primary btn-xs" type="button" disabled aria-haspopup="true" aria-expanded="false">
                        Editar Acta
                    </button>
                    <button
                            class="btn btn-success btn-xs" type="button" disabled aria-haspopup="true" aria-expanded="false">
                        Publicar
                    </button>
                    <button
                            class="btn btn-info btn-xs" type="button" disabled aria-haspopup="true" aria-expanded="false">
                        Despublicar
                    </button>
                    </div>
                </div>
                <div class="panel-body">
                        <table id="tabledatos" class="table table-responsive table-hover tabledatos table-bordered">
                            <tr>
                                <tr><th>Fecha Inventario</th><td>{{ $acta->fecha_inventario }}</td>
                                <th class="thstyle">Inicio Conteo</th><td>{{ $acta->inicio_conteo }}</td>
                                <th class="thstyle">Unidades Ajustadas (Valor Absoluto)</th><td>{{ $acta->unidades_ajustadas }}</td>
                                <th class="thstyle">Ítem (SKU) Revisados</th><td>{{ $acta->item_revisados }}</td>
                            </tr>
                            <tr><th>Cliente</th><td>{{ $acta->cliente }}</td>
                                <th class="thstyle">Fin Conteo</th><td>{{ $acta->fin_conteo }}</td>
                                <th class="thstyle">PTT Total Inventariadas</th><td>{{ $acta->ptt_total_inventariadas }}</td>
                                <th class="thstyle">Ítem (SKU) Revisados QF</th><td>{{ $acta->item_revisados_qf }}</td>
                            </tr>
                            <tr><th>RUT</th><td>{{ $acta->rut }}</td>
                                <th class="thstyle">Fin Revisión</th><td>{{ $acta->fin_revisión }}</td>
                                <th class="thstyle">PTT Revisadas Totales</th><td>{{ $acta->ptt_revisadas_totales }}</td>
                                <th class="thstyle">Ítem Revisados Apoyo CV 1</th><td>{{ $acta->item_revisados_apoyo_cv_1 }}</td>
                            </tr>
                            <tr><th>Supervisor</th><td>{{ $acta->supervisor }}</td>
                                <th class="thstyle">Horas Trabajadas</th><td>{{ $acta->horas_trabajadas }}</td>
                                <th class="thstyle">PTT Revisadas QF</th><td>{{ $acta->ptt_revisadas_qf }}</td>
                                <th class="thstyle">Ítem Revisados Apoyo CV 2</th><td>{{ $acta->item_revisados_apoyo_cv_2 }}</td>
                            </tr>
                            <tr><th>Químico Farmacéutico</th><td>{{ $acta->quimico_farmaceutico }}</td>
                                <th class="thstyle">Dotación Presupuestada</th><td>{{ $acta->dotacion_presupuestada }}</td>
                                <th class="thstyle">PTT Revisadas apoyo CV 1</th><td>{{ $acta->ptt_revisadas_apoyo_cv_1 }}</td>
                                <th class="thstyle">Corregidas en revisión antes de sacar el ajuste</th><td>{{ $acta->unidades_corregidas_revision_previo_ajuste }}</td>
                            </tr>
                            <tr><th>Nota Presentación</th><td>{{ $acta->nota_presentacion }}</td>
                                <th class="thstyle">Dotación Efectivo</th><td>{{ $acta->dotacion_efectivo }}</td>
                                <th class="thstyle">PTT Revisadas apoyo CV 2</th><td>{{ $acta->ptt_revisadas_apoyo_cv_2 }}</td>
                                <th class="thstyle">Unidades Corregidas</th><td>{{ $acta->unidades_corregidas }}</td>
                            </tr>
                            <tr><th>Nota Supervisor</th><td>{{ $acta->nota_supervisor }}</td>
                                <th class="thstyle">Unidades Inventariadas</th><td>{{ $acta->unidades_inventariadas }}</td>
                                <th class="thstyle">PTT Revisadas Supervisores FCV</th><td>{{ $acta->ptt_revisadas_supervisores_fcv }}</td>
                                <th class="thstyle">Total Ítem</th><td>{{ $acta->total_item }}</td>
                            </tr>
                            <tr><th>Nota Conteo</th><td>{{ $acta->nota_conteo }}</td>
                                <th class="thstyle">Unidades Teóricas</th><td>{{ $acta->unidades_teoricas }}</td>
                                <th class="thstyle">Ítem (SKU) Total Inventariados</th><td>{{ $acta->item_total_inventariados }}</td>
                                <th class="thstyle"></th><td></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading" align="center"><span class="glyphicon glyphicon-folder-close"></span> Archivos</div>
                    <div style="height:110px;overflow:auto;" class="panel-body">
                    <table class="table table-responsive table-hover tablefiles table-bordered">
                            <thead>
                            <th>Fecha Subida</th>
                            <th>Subido por</th>
                            <th>Estado</th>
                            <th>Nombre Archivo</th>
                            <th>Opciones</th>
                            </thead>
                        @foreach($archivos_finales as $af)
                            <tr>
                                <td width="15%">{{ $af->created_at }}</td>
                                <td width="20%">{{ $af->usuario_auditor->nombreCompleto() }}</td>
                                <td width="30%">{{ $af->resultado }}</td>
                                <td width="20%">{{ $af->nombre_original }}</td>
                                <td>
                                    <a aria-haspopup="true" aria-expanded="false" href='/{{$af->idArchivoFinalInventario}}/descargar-zip' class="btn btn-primary btn-xs">Descargar</a>
                                </td>
                            </tr>
                        @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class='container-fluid'>
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading" align="center"><span class="glyphicon glyphicon-plus-sign"></span> Subir Archivo Final</div>
                <div class="panel-body">
                    <div align="center">
                    <form action="/api/archivo-final-inventario/{{ $acta->idInventario }}/upload-zip" method="post" enctype="multipart/form-data">
                        <label>Seleccione Archivo:</label>
                        <input type="file" name="archivoFinalZip" id="archivoFinalZip">
                        <br><input type="submit" class="btn btn-primary btn-xs" value="ZIP Archivo Final" name="submit"></br>
                        <input type="hidden" value="{{ csrf_token() }}" name="_token">
                    </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
                <div class="panel panel-default">
                <div class="panel-heading" align="center"><span class="glyphicon glyphicon-plus-sign"></span> Subir Archivo Final</div>
                <div align="center">
                    <form action="{{ URL::to('upload') }}" method="post" enctype="multipart/form-data">
                        <label>Seleccione Archivo:</label>
                        <input type="file" name="file" id="file" disabled>
                        <br><input type="submit" class="btn btn-primary btn-xs" disabled value="Archivo Acta TXT" name="submit"></br>
                        <input type="hidden" value="{{ csrf_token() }}" name="_token">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>