<div class="panel panel-default">
    <div class="panel-heading" align="center">
        <span class="glyphicon glyphicon-plus-sign"></span> Subir archivo_salida_Acta.txt
    </div>
    <div class="panel-body">
        <form class="form-horizontal" action="/api/archivo-final-inventario/{{-- $acta->idInventario --}}/upload-txt" method="post" enctype="multipart/form-data">
            <input type="hidden" value="{{ csrf_token() }}" name="_token">

            <div class="col-sm-12">
                <input class="form-control" type="file" name="archivo_salida_acta_txt" disabled>
            </div>

            <div class="col-sm-12">
                <input type="submit" class="btn btn-primary btn-sm btn-block" name="submit" disabled></br>
            </div>

            {{-- mensaje de error o de exito luego de subir un archivo --}}
            <div class="col-sm-12">
                @if(session('mensaje-exito'))
                    <div class="alert alert-success" role="alert">
                        {{session('mensaje-exito')}}
                    </div>
                @endif
                @if(session('mensaje-error'))
                    <div class="alert alert-danger" role="alert">
                        {{session('mensaje-error')}}
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>
