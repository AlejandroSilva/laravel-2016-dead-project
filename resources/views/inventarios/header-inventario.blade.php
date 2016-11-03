<style>
    .titulo-inventario h2{
        margin-bottom: 0;
    }
    .titulo-inventario h3{
        margin: 0;
    }
    .nav-inventario {
        margin-top: 20px;
        margin-bottom: 10px;
    }
    .nav-inventario > li > a {
        padding: 5px 15px !important;
    }
</style>

<div class="container fluid">
    <div class="page-header row">
        <div class="col-xs-6 titulo-inventario">
            <h2>
                {{$inventario->local->cliente->nombreCorto}}
                <b>{{$inventario->local->numero}}</b>: {{$inventario->local->nombre}}
            </h2>
            <h3><small>{{$inventario->fechaProgramadaF()}}</small></h3>
        </div>
        <div class="col-xs-6">
            <ul class="nav nav-pills navbar-right nav-inventario">
                <li class="active"><a href="#">Nomina Día</a></li>
                <li class="disabled"><a href="#">Nomina Noche</a></li>
                <li class=""><a href="#">Archivo Final</a></li>
                <li class=""><a href="#">Historial</a></li>
            </ul>
        </div>
    </div>
</div>