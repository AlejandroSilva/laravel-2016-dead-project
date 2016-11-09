<style>

    .titulo-inventario h2{
        margin-bottom: 0;
    }
    .titulo-inventario h3{
        margin: 0;
    }
    .nav-header {
        margin-top: 20px;
        margin-bottom: 10px;
    }
    .nav-header > li > a {
        padding: 5px 15px !important;
    }
</style>

<div class="page-header row">
    <div class="col-xs-6 titulo-inventario">
        <h2>
            Maestra de productos FCV
        </h2>
        {{--<h3><small>martes 25 de octubre, 2016</small></h3>--}}
    </div>
    <div class="col-xs-6">
        <ul class="nav nav-pills navbar-right nav-header">
            <li>
                <a href="{{ url("maestra-fcv")  }}">
                    <span class="glyphicon glyphicon-arrow-left"></span> Volver
                </a>
            </li>
            <li class="{{ Request::is('*/ver-estado')? 'active': '' }}">
                {{-- por ahora solo para el cliente fcv, cambiar la ruta si hay otro cliente --}}
                <a href="{{ url("maestra-fcv/$archivoMaestra->idArchivoMaestra/ver-estado")  }}">
                    ver estado
                </a>
            </li>
            <li class="disabled"><a href="#">actualizar</a></li>
            <li class="{{ $archivoMaestra->maestraValida? '' : 'disabled' }}"><a href="#">Descargar</a></li>
        </ul>
    </div>
</div>
