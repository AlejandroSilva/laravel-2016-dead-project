<style>
    .tabla-nominas {
        font-size: 12px;
    }
</style>

<div class="panel panel-default">
    <div class="panel-heading">
        <span class="glyphicon glyphicon-calendar"></span> Mis próximos Inventarios
    </div>
    <div class="panel-body">
        @if( sizeof($nominas)>0)
            <table class="table table-bordered table-hover table-condensed tabla-nominas">
                <thead>
                    <tr>
                        <th class="th">Fecha programada</th>
                        <th class="th">Cliente</th>
                        <th class="th">CE</th>
                        <th class="th">Local</th>
                        <th class="th">Región</th>
                        <th class="th">Comuna</th>
                        <th class="th">Dirección</th>
                        <th class="th">Lider</th>
                        <th class="th">Cargo Usuario</th>
                        <th class="th">Archivo Final</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($nominas as $nomina)
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
                                <a href='/inventario/{{$nomina->inventario->idInventario}}/archivo-final' class="btn btn-primary btn-xs center-block">Ver</a>
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