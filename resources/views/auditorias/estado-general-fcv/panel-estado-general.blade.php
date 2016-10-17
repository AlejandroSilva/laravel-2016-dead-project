<style>
    .table th{
        text-align: center;
    }
    .table > tbody{
        font-size: 14px;
    }
    .table td{
        text-align: center;
    }
    .label {
        font-size: 10px;
        width: 35px;
    }
</style>

<div class="panel panel-primary">
    <div class="panel-heading">
        Estado general de auditorías para el día <b>{{$ega_hoy}}</b>
    </div>
    <table class="table table-bordered table-condensed table-hover">
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th colspan="4">Realizadas</th>
                <th colspan="4">Pendientes</th>
            </tr>
            <tr>
                <th>Zona</th>
                <th>Total Mes</th>
                {{-- realizadas --}}
                <th>Optimo</th>
                <th>% Optimo</th>
                <th>Real</th>
                <th>% Real</th>
                {{-- Pendientes--}}
                <th>Optimo</th>
                <th>% Optimo</th>
                <th>Real</th>
                <th>% Real</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ega_zonas as $zona)
                {{-- dependiendo del porcentaje de avance, son los colores --}}
                @if($zona->realizadoPorcentajeDiferencia<-25)
                    {{--  -100% a -25% es PELIGRO --}}
                    @php( $LABEL_COLOR = 'label-danger' )
                @elseif( $zona->realizadoPorcentajeDiferencia>=-25 && $zona->realizadoPorcentajeDiferencia<0)
                    {{--   -25% a   0% es ADVERTENCIA --}}
                    @php( $LABEL_COLOR = 'label-warning' )
                @elseif( $zona->realizadoPorcentajeDiferencia>=0)
                    {{--  0 a 100% esta BIEN --}}
                    @php( $LABEL_COLOR = 'label-success' )
                @endif

                <tr>
                    <td>{{ $zona->nombreZona }}</td>
                    <td><b>{{ $zona->totalMes }}</b></td>
                    {{-- realizado --}}
                    <td>{{$zona->realizadoOptimo}}</td>
                    <td>{{ $zona->realizadoPorcentajeOptimo }}%</td>
                    <td>
                        <b>{{$zona->realizadoReal}}</b>
                        <span class="label {{$LABEL_COLOR}} pull-right" title="diferencia real-optimo">
                                        {{$zona->realizadoDiferencia}}
                                    </span>
                    </td>
                    <td>
                        <b>{{ $zona->realizadoPorcentajeReal }}%</b>
                        <span class="label {{$LABEL_COLOR}} pull-right" title="diferencia %real - %optimo">
                                        {{$zona->realizadoPorcentajeDiferencia}}%
                                    </span>
                    </td>
                    {{-- pendiente --}}
                    <td>{{ $zona->pendientesOptimo }}</td>
                    <td>{{ $zona->pendientePorcentajeOptimo }}%</td>
                    <td>
                        <b>{{$zona->pendientesReal}}</b>
                        <span class="label {{$LABEL_COLOR}} pull-right" title="diferencia real - optimo">
                            {{$zona->pendienteDiferencia}}
                        </span>
                    </td>
                    <td>
                        <b>{{ $zona->pendientePorcentajeReal }}%</b>
                        <span class="label {{$LABEL_COLOR}} pull-right" title="diferencia %real - %optimo">
                                        {{$zona->pendientePorcentajeDiferencia}}%
                                    </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfooter>
            <tr>
                <td><b>Total / Promedio</b></td>
                <td><b>{{$ega_totales->total}}</b></td>
                {{-- Realizadas --}}
                <td>{{$ega_totales->realizadoOptimo}}</td>
                <td>{{$ega_totales->realizadoPorcentajeOptimo}}%</td>
                <td>
                    <b>{{$ega_totales->realizadoReal}}</b>
                    <span class="label pull-right">&nbsp;&nbsp;&nbsp;</span>
                </td>
                <td>
                    <b>{{$ega_totales->realizadoPorcentajeReal}}%</b>
                    <span class="label pull-right">&nbsp;&nbsp;&nbsp;</span>
                </td>
                {{-- Pendientes --}}
                <td>{{$ega_totales->pendientesOptimo}}</td>
                <td>{{$ega_totales->pendientesPorcentajeOptimo}}%</td>
                <td>
                    <b>{{$ega_totales->pendientesReal}}</b>
                    <span class="label pull-right">&nbsp;&nbsp;&nbsp;</span>
                </td>
                <td>
                    <b>{{$ega_totales->pendientesPorcentajeReal}}%</b>
                    <span class="label pull-right">&nbsp;&nbsp;&nbsp;</span>
                </td>
            </tr>
        </tfooter>
    </table>
</div>