<style>
    .tabla-nominas {
        font-size: 12px;
    }
</style>

<div class="panel panel-default">
    <div class="panel-heading">
        <span class="glyphicon glyphicon-calendar"></span> Indicadores de Gestión {{$hoy}}
    </div>
    <div class="panel-body">
        @if( sizeof($inventariosAyer)>0)
            <table class="table table-bordered table-hover table-condensed tabla-nominas">
                <thead>
                <tr>
                    <th class="th">Local</th>
                    <th class="th">Lider</th>
                    <th class="th">Items</th>
                    <th class="th">Horas trabajadas</th>
                    <th class="th">Items HH</th>
                    <th class="th">Nota Prom</th>
                    <th class="th">% error SEI</th>
                    <th class="th">Item rev cli</th>
                    <th class="th">% rev cli</th>
                    <th class="th">ptt rev cli</th>
                    <th class="th">diferencia neta</th>
                    <th class="th">Archivo Final</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($inventariosAyer as $inv)
                        @php( $acta = $inv->actaFCV )
                        @php( $mostrarDatos = $acta && $acta->estaPublicada())
                        <tr>
                            <td class="">{{$inv->local->cliente->nombreCorto}} {{$inv->local->numero}}</td>
                            <td class="">{{$mostrarDatos? $acta->usuario : ''}}</td>
                            <td class="">{{$mostrarDatos? $acta->getUnidadesInventariadas(true) : ''}}</td>
                            <td class="">{{$mostrarDatos? $acta->getHorasTrabajadas() : ''}}</td>
                            <td class="">{{$mostrarDatos? $acta->getItemsHH(true) : ''}}</td>
                            <td class="">{{$mostrarDatos? $acta->getNotaPromedio() : ''}}</td>
                            <td class="">{{$mostrarDatos? $acta->getPorcentajeErrorSei() : ''}}</td>
                            <td class="">{{$mostrarDatos? $acta->getItemRevisadosCliente(true) : ''}}</td>
                            <td class="">{{$mostrarDatos? $acta->getPorcentajeRevisionCliente(true) : ''}}</td>
                            <td class="">{{$mostrarDatos? $acta->getPatentesRevisadasTotales(true) : ''}}</td>
                            <td class="">{{$mostrarDatos? $acta->getDiferenciaNeta(true) : ''}}</td>
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
                        <td colspan="11">Resumen actas</td>
                        <td>
                            <a class="btn btn-primary btn-xs btn-block" href="/inventario/descargar-consolidado-fcv">Descargar</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        @else
            <p>Sin inventarios</p>
        @endif
    </div>
</div>