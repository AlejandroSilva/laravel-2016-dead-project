
{{-- MENU FCV --}}
<li class="#">
    <a id="drop-fcv" href="#" class="dropdown-toggle" data-toggle="dropdown">
        FCV <span class="caret"></span>
    </a>
    <ul class="dropdown-menu" aria-labelledby="drop-fcv">
        {{-- INVENTARIOS --}}
        <li class="dropdown-header">Inventarios</li>
        <li class="{{ Request::is('/informes-finales-inventarios-fcv')? 'active': '' }}">
            <a href="{{ url('/informes-finales-inventarios-fcv') }}">Informes Finales Inventarios</a>
        </li>

        {{-- AUDITORIAS --}}
        <li role="separator" class="divider"></li>
        <li class="dropdown-header">Auditorias</li>
        <li class="{{ Request::is('/auditorias/estado-general-fcv')? 'active': '' }}">
            <a href="{{ url('/auditorias/estado-general-fcv') }}">Estado general de Auditorias</a>
        </li>

        {{-- MAESTRA DE PRODUCTOS --}}
        <li role="separator" class="divider"></li>
        <li class="{{ Request::is('maestra-productos-fcv')? 'active': '' }}">
            <a href="{{ url('maestra-productos-fcv') }}">Maestra de productos FCV</a>
        </li>

        {{-- MUESTRA VENCIMIENTO FCV--}}
        @if( Auth::user()->hasRole('Developer') )
            <li class="{{ Request::is('muestra-vencimiento-fcv')? 'active': '' }}">
                <a href="{{ url('muestra-vencimiento-fcv') }}">Muestra de vencimiento FCV (DEV)</a>
            </li>
        @endif
    </ul>
</li>