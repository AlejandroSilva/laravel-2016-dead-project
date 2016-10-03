<div class="panel panel-default">
    <div class="panel-heading" align="center">
        <span class="glyphicon glyphicon-plus-sign"></span> Subir archivo final ZIP
    </div>
    <div class="panel-body">
        <form class="form-horizontal" action="/inventario/{{$inventario->idInventario}}/subir-zip-fcv" method="post" enctype="multipart/form-data">
            <input type="hidden" value="{{ csrf_token() }}" name="_token">

            <div class="col-sm-12">
                <input class="form-control" type="file" name="archivoFinalZip">
            </div>

            <div class="col-sm-12">
                <input type="submit" class="btn btn-primary btn-sm btn-block" name="submit"></br>
            </div>

            {{-- mensaje de error o de exito luego de subir un archivo --}}
            <div class="col-sm-12">
                @if(session('mensaje-exito-zip'))
                    <div class="alert alert-success" role="alert">
                        {{session('mensaje-exito-zip')}}
                    </div>
                @endif
                @if(session('mensaje-error-zip'))
                    <div class="alert alert-danger" role="alert">
                        {{session('mensaje-error-zip')}}
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>