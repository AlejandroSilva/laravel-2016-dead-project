<style>
    .tabla-nominas {
        font-size: 12px;
    }
</style>

<div class="panel panel-default">
    <div class="panel-heading">
        <span class="glyphicon glyphicon-calendar"></span> Mis inventarios como Líder/Supervisor/Operador, desde el {{$misNominasDesde}} al {{$misNominasHasta}}
    </div>
    <div class="panel-body">
        @if( sizeof($nominas)>0)
            <table class="table table-bordered table-hover table-condensed tabla-nominas">
                <thead>
                    <tr>
                        <th class="th">Fecha programada</th>
                        <th class="th">CLI</th>
                        <th class="th">CE</th>
                        <th class="th">Local</th>
                        <th class="th">Reg.</th>
                        <th class="th">Comuna</th>
                        <th class="th">Dirección</th>
                        <th class="th">Lider</th>
                        <th class="th">Cargo Usuario</th>
                        <th class="th">Archivo Final</th>
                        <th class="th">Nómina</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($nominas as $nomina)
                        @php( $acta = $nomina->inventario->actaFCV )
                        @php( $datosDisponibles = $acta && $acta->estaPublicada())
                        <tr>
                            <td class="tdfechaprogramada">{{$nomina->inventario->fechaProgramadaF()}}</td>
                            <td class="td">{{$nomina->inventario->local->cliente->nombreCorto}}</td>
                            <td class="td">{{$nomina->inventario->local->numero}}</td>
                            <td class="td">{{$nomina->inventario->local->nombre}}</td>
                            <td class="td">{{$nomina->inventario->local->direccion->comuna->provincia->region->numero}}</td>
                            <td class="td">{{$nomina->inventario->local->direccion->comuna->nombre}}</td>
                            <td class="td">{{$nomina->inventario->local->direccion->direccion}}</td>
                            <td class="td">{{$nomina->lider? $nomina->lider->nombreCorto() : '-'}}</td>
                            <td class="td">{{$nomina->cargoUsuario}}</td>
                            <td class="tdopcion">
                                <a class="btn btn-default btn-xs" href="/inventario/{{$nomina->inventario->idInventario}}/archivo-final-fcv" target="_blank">
                                    acta
                                </a>
                                @if($nomina->inventario->actaFCV)
                                    <a class="btn btn-primary btn-xs" href='inventario/archivo-final/{{$nomina->inventario->actaFCV->idArchivoFinalInventario}}/descargar-fcv'>ZIP</a>
                                @else
                                    --
                                @endif
                            </td>
                            <td>
                                @if( $nomina->informadaAlCliente() )
                                    <a href="/programacionIG/nomina/{{$nomina->idNomina}}" class="label label-primary" target="_blank">Informada</a>
                                @else
                                    <a href="/programacionIG/nomina/{{$nomina->idNomina}}" class="label label-default" target="_blank">Pendiente</a>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No tiene inventarios asignados esta semana</p>
        @endif
    </div>
</div>