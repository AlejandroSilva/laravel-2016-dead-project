<style>
    .tabla-nominas {
        font-size: 12px;
    }
    .tdTextoDerecha{
        text-align: right;
    }
</style>

<div class="panel panel-default">
    <div class="panel-heading">
        <span class="glyphicon glyphicon-calendar"></span>
            Indicadores de Gestión desde <b>{{$indicadoresGestion_desde}}</b> hasta <b>{{$indicadoresGestion_hasta}}</b>
    </div>
    <div class="panel-body">
        @if( sizeof($inventariosPeriodo)>0)
            <table class="table table-bordered table-hover table-condensed tabla-nominas">
                <thead>
                <tr>
                    <th class="th">Local</th>
                    <th class="th">Lider</th>
                    <th class="th">Items</th>
                    <th class="th">Horas trabajadas</th>
                    <th class="th">Nota Prom</th>
                    <th class="th">% error SEI</th>
                    <th class="th">Item rev cli</th>
                    <th class="th">% rev cli</th>
                    <th class="th">Consolidado PTT FCV</th>
                    <th class="th">diferencia neta</th>
                    <th class="th">Archivo Final</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($inventariosPeriodo as $inv)
                        @php( $acta = $inv->actaFCV )
                        @php( $datosDisponibles = $acta && $acta->estaPublicada())
                        <tr>
                            <td class="">{{$inv->local->cliente->nombreCorto}} {{$inv->local->numero}}</td>
                            <td class="">{{$datosDisponibles? $acta->usuario : ''}}</td>
                            <td class="tdTextoDerecha">{{$datosDisponibles? $acta->getUnidadesInventariadas(true) : ''}}</td>
                            <td class="tdTextoDerecha">{{$datosDisponibles? $acta->getDuracionConteo(true) : ''}}</td>
                            <td class="tdTextoDerecha">{{$datosDisponibles? $acta->getNotaPromedio() : ''}}</td>
                            <td class="tdTextoDerecha">{{$datosDisponibles? $acta->getPorcentajeErrorSei(true) : ''}}</td>
                            <td class="tdTextoDerecha">{{$datosDisponibles? $acta->getItemRevisadosCliente(true) : ''}}</td>
                            <td class="tdTextoDerecha">{{$datosDisponibles? $acta->getPorcentajeRevisionCliente(true) : ''}}</td>
                            <td class="tdTextoDerecha">{{$datosDisponibles? $acta->getConsolidadoPatentes(true) : ''}}</td>
                            <td class="tdTextoDerecha">{{$datosDisponibles? $acta->getDiferenciaNeto(true) : ''}}</td>
                            <td class="opciones">
                                <a class="btn btn-default btn-xs" href="/inventario/{{$inv->idInventario}}/archivo-final" target="_blank">
                                    acta
                                </a>
                                @if($inv->actaFCV)
                                    <a class="btn btn-primary btn-xs" href='archivo-final-inventario/{{$inv->actaFCV->idArchivoFinalInventario}}/descargar'>ZIP</a>
                                @else
                                    --
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2">Resumen actas actuales</td>
                        <td class="tdTextoDerecha">{{ $totalIndicadores->unidadesInventariadas }}</td>
                        <td class="tdTextoDerecha">{{ $totalIndicadores->horasTrabajadas }}</td>
                        <td class="tdTextoDerecha">{{ $totalIndicadores->nota_promedio }}</td>
                        <td class="tdTextoDerecha">{{ $totalIndicadores->porcentajeError_promedio }}</td>
                        <td class="tdTextoDerecha">{{ $totalIndicadores->itemsRevisadosCliente }}</td>
                        <td class="tdTextoDerecha">{{ $totalIndicadores->porcentajeRevisionCliente_promedio }}</td>
                        <td class="tdTextoDerecha">{{ $totalIndicadores->consolidadoPatentesFCV_total }}</td>
                        <td class="tdTextoDerecha">{{ $totalIndicadores->diferenciaNeta_total }}</td>
                        <td></td>
                    </tr>
                    {{-- Mes Actual --}}
                    <tr>
                        <td colspan="2">Actas de Octubre</td>
                        <td colspan="8"></td>
                        <td>
                            <a class="btn btn-primary btn-xs btn-block" href="/inventario/descargar-consolidado-fcv?fechaInicio=2016-10-01&fechaFin=2016-10-31">Descargar</a>
                        </td>
                    </tr>
                    {{-- Mes anterior --}}
                    <tr>
                        <td colspan="2">Actas de Septiembre</td>
                        <td colspan="8"></td>
                        <td>
                            <a class="btn btn-primary btn-xs btn-block" href="/inventario/descargar-consolidado-fcv?fechaInicio=2016-09-01&fechaFin=2016-09-30">Descargar</a>
                        </td>
                    </tr>
                {{-- 2016 --}}
                    <tr>
                        <td colspan="2">Actas de 2016</td>
                        <td colspan="8"></td>
                        <td>
                            <a class="btn btn-primary btn-xs btn-block" href="/inventario/descargar-consolidado-fcv?fechaInicio=2016-05-01">Descargar</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        @else
            <p>Sin inventarios</p>
        @endif
    </div>
</div>