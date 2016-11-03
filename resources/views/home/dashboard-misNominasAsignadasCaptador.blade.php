<div class="panel panel-primary">
    <div class="panel-heading">
        <span class="glyphicon glyphicon-calendar"></span>
        Auditor: Nóminas asignadas
    </div>

    @if( sizeof($inventariosPeriodo)>0)
        <table class="table table-bordered table-hover table-condensed tabla-nominas">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha programada</th>
                    <th>Cliente</th>
                    <th>Local</th>
                    <th>Turno</th>
                    <th>Fecha Limite</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nominasCaptador as $n)
                    <tr>
                        <td>{{ $n->idNomina }}</td>
                        <td>{{ $n->inventario->fechaProgramada }}</td>
                        <td>{{ $n->inventario->local->cliente->nombreCorto }}</td>
                        <td>{{ $n->inventario->local->nombre }}</td>
                        <td>{{ $n->turno }}</td>
                        <td>{{ $n->inventario->getFechaLimiteCaptador() }}</td>
                        <td>
                            <a class="btn btn-xs btn-primary" href="/nomina/{{ $n->idNomina }}">ver</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="panel-body">
            <p>Sin inventarios</p>
        </div>
    @endif
</div>