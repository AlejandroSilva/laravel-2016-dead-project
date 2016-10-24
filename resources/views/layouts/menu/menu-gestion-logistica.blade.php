@php( $mostrarActivoFijo = Auth::user()->can('activoFijo-verModulo') )

@if( $mostrarActivoFijo )
    <li class="">
        <a id="drop-logistica" href="#" class="dropdown-toggle" data-toggle="dropdown">
            Gestión Logistica <span class="caret"></span>
        </a>
        <ul class="dropdown-menu" aria-labelledby="drop-logistica">
            {{-- ACTIVO FIJO--}}
            <li class="{{ Request::is('activo-fijo')? 'active': '' }}">
                <a href="{{ url('activo-fijo') }}">Control Activos Fijos</a>
            </li>
        </ul>
    </li>
@endif