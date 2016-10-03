<style>
    .dTable {
        display: table;
    }
    .tr {
        display: table-row;
    }
    .tr:hover {
        background: lightcyan;
    }
    .tr p:first-child {
        font-weight: bold;
        display: table-cell;
    }
    .tr p:nth-child(2) {
        display: table-cell;
    }
    .opciones-acta > form{
        display: inline-block;
    }
</style>
<div class="panel panel-default">
    <div class="panel-heading" align="center">
        <span class="glyphicon glyphicon-stats"></span>
        Acta Inventario
        @if( isset($acta) && $acta->estaPublicada())
            Publicada {{ $acta->publicadaPor? "por ".$acta->publicadaPor->nombreCorto() : '---'}}
        @endif
        @if( isset($acta) )
            <div class="opciones-acta pull-right">
                <form action="/inventario/{{$inventario->idInventario}}/publicar-acta" method="get" class="form-horizontal">
                    <input type="submit" class="btn btn-primary btn-xs" value="Editar Acta" disabled/>
                </form>
                {{-- publicar o despublicar acta--}}
                @if( $acta->estaPublicada() )
                    <form action="/inventario/{{$inventario->idInventario}}/despublicar-acta" method="post" class="form-horizontal">
                        <input type="hidden" value="{{ csrf_token() }}" name="_token">
                        <input type="submit" class="btn btn-info btn-xs" value="Des-publicar"/>
                    </form>
                @else
                    <form action="/inventario/{{$inventario->idInventario}}/publicar-acta" method="post" class="form-horizontal">
                        <input type="hidden" value="{{ csrf_token() }}" name="_token">
                        <input type="submit" class="btn btn-success btn-xs" value="Publicar"/>
                    </form>
                @endif
            </div>
        @endif
    </div>
    <div class="panel-body">
        {{-- muestr la tabla solo si el acta existe (han sido cargado sus datos en la BD) --}}
        @if( isset($acta) )
            <div class="col-md-3"> {{-- columna 1/4--}}
                <div class="dTable">
                    <div class="tr">
                        <p>Fecha Inventario</p>
                        <p>{{ $acta->fecha_toma }}</p>
                    </div>
                    <div class="tr">
                        <p>Cliente</p>
                        <p>{{  $acta->nombre_empresa}}</p>
                    </div>
                    <div class="tr">
                        <p>CECO</p>
                        <p>{{  $acta->cod_local}}</p>
                    </div>
                    <div class="tr">
                        <p>Supervisor</p>
                        <p>{{ $acta->usuario }}</p>
                    </div>
                    <div class="tr">
                        <p>QF</p>
                        <p>{{ $acta->administrador }}</p>
                    </div>
                    <div class="tr">
                        <p>Nota presentación</p>
                        <p>{{ $acta->nota1 }}</p>
                    </div>
                    <div class="tr">
                        <p>Nota supervisor</p>
                        <p>{{ $acta->nota2 }}</p>
                    </div>
                    <div class="tr">
                        <p>Nota conteo</p>
                        <p>{{ $acta->nota3 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3"> {{-- columna 2/4--}}
                <div class="dTable">
                    <div class="tr">
                        <p>Inicio Conteo</p>
                        <p>{{  $acta->getInicioConteo() }}</p>
                    </div>
                    <div class="tr">
                        <p>Fin conteo</p>
                        <p> {{  $acta->getFinConteo() }}</p>
                    </div>
                    <div class="tr">
                        <p>Fin revisión</p>
                        <p>{{  $acta->fecha_revision_grilla }}</p>
                    </div>
                    <div class="tr">
                        <p>Horas trabajadas</p>
                        <p>{{  $acta->getHorasTrabajadas() }}</p>
                    </div>
                    <div class="tr">
                        <p>Dotación Presupuestada</p>
                        <p>{{ $acta->presupuesto }}</p>
                    </div>
                    <div class="tr">
                        <p>Dotación Efectiva</p>
                        <p>{{ $acta->efectiva }}</p>
                    </div>
                    <div class="tr">
                        <p>Unidades Inventariadas</p>
                        <p>{{ $acta->getUnidadesInventariadas(true) }}</p>
                    </div>
                    <div class="tr">
                        <p>Unidades Teóricas</p>
                        <p>{{ $acta->getUnidadesTeoricas() }}</p>
                    </div>
                    <div class="tr">
                        <p>Unidades Ajustadas (Valor Absoluto)</p>
                        <p>{{ $acta->unid_absoluto_corregido_auditoria }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3"> {{-- columna 3/4--}}
                <div class="tr">
                    <p>PTT Total Inventariadas</p>
                    <p>{{ $acta->ptt_inventariadas }}</p>
                </div>
                <div class="tr">
                    <p>PTT Revisadas Totales</p>
                    <p>{{ $acta->aud1 }}</p>
                </div>
                <div class="tr">
                    <p>PTT Revisadas QF</p>
                    <p>{{ $acta->ptt_rev_qf }}</p>
                </div>
                <div class="tr">
                    <p>PTT Revisadas apoyo FCV 1</p>
                    <p>{{ $acta->ptt_rev_apoyo1 }}</p>
                </div>
                <div class="tr">
                    <p>PTT Revisadas apoyo FCV 2</p>
                    <p>{{ $acta->ptt_rev_apoyo2 }}</p>
                </div>
                <div class="tr">
                    <p>PTT Revisadas Supervisores FCV</p>
                    <p>{{ $acta->ptt_rev_supervisor_fcv }}</p>
                </div>
            </div>
            <div class="col-md-3"> {{-- columna 4/4--}}
                <div class="tr">
                    <p>Total SKU inventariados</p>
                    <p>(pendiente) {{ $acta->XXXXX }}</p>
                </div>
                <div class="tr">
                    <p>Total items inventariados</p>
                    <p>{{ $acta->total_items_inventariados }}</p> {{-- tot3 || total_items_inventariados --}}
                </div>
                <div class="tr">
                    <p>Total items cod interno</p>
                    <p>{{ $acta->total_items_inventariados }}</p>
                </div>
                <div class="tr">
                    <p>Items auditados</p>
                    <p>{{ $acta->aud2 }}</p> {{-- Aud2 || Item Auditados --}}
                </div>
                <div class="tr">
                    <p>Items revisados QF</p>
                    <p>{{ $acta->items_rev_qf }}</p>
                </div>
                <div class="tr">
                    <p>Items revisados apoyo CV 1</p>
                    <p>{{ $acta->items_rev_apoyo1 }}</p>
                </div>
                <div class="tr">
                    <p>Items revisados apoyo CV 2</p>
                    <p>{{ $acta->items_rev_apoyo2 }}</p>
                </div>
                <div class="tr">
                    <p>SKU auditados</p>
                    <p>(pendiente) {{ $acta->XXXX }}</p>
                </div>
                <div class="tr">
                    <p>Unidades corregidas en revisión previo ajuste</p>
                    <p>(pendiente) {{ $acta->XXXXX }}</p>
                </div>
                <div class="tr">
                    <p>Unidades corregidas</p>
                    <p>{{ $acta->unid_absoluto_corregido_auditoria }}</p>
                </div>
            </div>
        @else
            <div style="text-align: center;">
                No se han cargado los datos del acta
            </div>
        @endif
    </div>
</div>