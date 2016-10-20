@if( Auth::user()->can('activoFijo-verModulo'))
    <li class="">
        <a id="drop-logistica" href="#" class="dropdown-toggle" data-toggle="dropdown">
            Gestión Logistica <span class="caret"></span>
        </a>

        <ul class="dropdown-menu" aria-labelledby="drop-logistica">
            <li class="{{ Request::is('activo-fijo')? 'active': '' }}">
                <a href="{{ url('activo-fijo') }}">Control Activos Fijos</a>
            </li>
        </ul>
    </li>
@endif