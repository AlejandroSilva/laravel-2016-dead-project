<style  type="text/css">
    .tabla-documentos {
        font-size: 12px;
    }
    .tabla-documentos {
        display: block;
        height: 200px;
        overflow-y: scroll;
    }
    .tabla-documentos td{
        white-space: nowrap;
    }
</style>

<div class="panel panel-default">
    <div class="panel-heading" align="center">
        <span class="glyphicon glyphicon-folder-close"></span> Archivos finales enviados
    </div>
    <div class="panel-body tabla-documentos">
        <table class="table table-hover tablefiles table-bordered">
            <thead>
                <th>documento</th>
                <th>subido por</th>
                <th>fecha subida</th>
                <th>acta valida</th>
                <th>resultado</th>
                <th>opciones</th>
            </thead>
            @if( sizeof($archivos_finales)>0 )
                @foreach($archivos_finales as $archivo)
                    <tr class="{{ $archivo->actaValida? 'success' : 'warning' }}">
                        <td>{{ $archivo->nombre_original }}</td>
                        <td>{{ $archivo->subidoPor? $archivo->subidoPor->nombreCorto() : '-' }}</td>
                        <td>{{ $archivo->created_at }}</td>
                        <td>{{ $archivo->actaValida? 'valida' : 'con errores' }}</td>
                        <td>{{ $archivo->resultado }}</td>
                        <td>
                            <a href='/archivo-final-inventario/{{$archivo->idArchivoFinalInventario}}/descargar' class="btn btn-primary btn-xs">Descargar</a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr class="warning">
                    <td colspan="6" style="text-align: center">No se ha cargado ningún archivo</td>
                </tr>
            @endif
        </table>
    </div>
</div>