<ul class="nav nav-stacked">
    <li class="nav-header">
        {{-- ######### Gestión de Inventarios ######### --}}
        {{-- Programacion --}}
        <a href="#" data-toggle="collapse" data-target="#menu-programacion" class="{{ Request::is('programacion/*')? 'active': '' }}">Programación</a>
        <ul class="nav nav-stacked collapse {{ Request::is('programacion/*')? 'in': '' }}" id="menu-programacion">
            <li class="{{ Request::is('programacion/mensual')? 'active': '' }}">
                <a href="{{ url('programacion/mensual') }}">Programación mensual</a>
            </li>
            <li class="{{ Request::is('programacion/semanal')? 'active': '' }}">
                <a href="{{ url('programacion/semanal') }}">Programación semanal</a>
            </li>
        </ul>
        {{-- Inventario --}}
        <a href="#" data-toggle="collapse" data-target="#menu-inventario" class="{{ Request::is('inventario/*')? 'active': '' }}">Inventario</a>
        <ul class="nav nav-stacked collapse {{ Request::is('inventario/*')? 'in': '' }}" id="menu-inventario">
            <li class="{{ Request::is('inventario/lista')? 'active': '' }}">
                <a href="{{ url('inventario/lista') }}">Listado de inventarios</a>
            </li>
            <li class="{{ Request::is('inventario/nuevo')? 'active': '' }}">
                <a href="{{ url('inventario/nuevo') }}">Nuevo Inventario</a>
            </li>
        </ul>
        {{-- Nominas --}}
        <a href="#" data-toggle="collapse" data-target="#menu-nominas" class="{{ Request::is('nom*')? 'active': '' }}">Nominas</a>
        <ul class="nav nav-stacked collapse {{ Request::is('nom*')? 'in': '' }}" id="menu-nominas">
            <li class="{{ Request::is('nominas')? 'active': '' }}">
                <a href="{{ url('nominas') }}">Nominas</a>
            </li>
            <li class="{{ Request::is('nomFinales')? 'active': '' }}">
                <a href="{{ url('nomFinales') }}">Nominas Finales</a>
            </li>
        </ul>

        {{-- Gestión de Personal --}}
        <a href="#" data-toggle="collapse" data-target="#menu-personal" class="{{ Request::is('personal/*')? 'active': '' }}">Personal</a>
        <ul class="nav nav-stacked collapse {{ Request::is('personal/*')? 'in': '' }}" id="menu-personal">
            <li class="{{ Request::is('personal/lista')? 'active': '' }}">
                <a href="{{ url('personal/lista') }}">Lista</a>
            </li>
            <li class="{{ Request::is('personal/nuevo')? 'active': '' }}">
                <a href="{{ url('personal/nuevo') }}">Nuevo Inventario</a>
            </li>
        </ul>
        {{--
        <li class="{{ Request::is('personal/operadores') ? 'active' : '' }}">
            <a href="{{ url('personal/operadores') }}">Operadores</a>
        </li>
        --}}
    </li>
</ul>

{{--
<ul class="nav nav-stacked">
    <li class="nav-header"> <a href="#" data-toggle="collapse" data-target="#userMenu">Settings</i></a>
        <ul class="nav nav-stacked collapse in" id="userMenu">
            <li class="active"> <a href="#"><i class="glyphicon glyphicon-home"></i> Home</a></li>
            <li><a href="#"><i class="glyphicon glyphicon-envelope"></i> Messages <span class="badge badge-info">4</span></a></li>
            <li><a href="#"><i class="glyphicon glyphicon-cog"></i> Options</a></li>
            <li><a href="#"><i class="glyphicon glyphicon-comment"></i> Shoutbox</a></li>
            <li><a href="#"><i class="glyphicon glyphicon-user"></i> Staff List</a></li>
            <li><a href="#"><i class="glyphicon glyphicon-flag"></i> Transactions</a></li>
            <li><a href="#"><i class="glyphicon glyphicon-exclamation-sign"></i> Rules</a></li>
            <li><a href="#"><i class="glyphicon glyphicon-off"></i> Logout</a></li>
        </ul>
    </li>
    <li class="nav-header"> <a href="#" data-toggle="collapse" data-target="#menu2"> Reports <i class="glyphicon glyphicon-chevron-right"></i></a>

        <ul class="nav nav-stacked collapse" id="menu2">
            <li><a href="#">Information &amp; Stats</a>
            </li>
            <li><a href="#">Views</a>
            </li>
            <li><a href="#">Requests</a>
            </li>
            <li><a href="#">Timetable</a>
            </li>
            <li><a href="#">Alerts</a>
            </li>
        </ul>
    </li>
    <li class="nav-header">
        <a href="#" data-toggle="collapse" data-target="#menu3"> Social Media <i class="glyphicon glyphicon-chevron-right"></i></a>
        <ul class="nav nav-stacked collapse" id="menu3">
            <li><a href=""><i class="glyphicon glyphicon-circle"></i> Facebook</a></li>
            <li><a href=""><i class="glyphicon glyphicon-circle"></i> Twitter</a></li>
        </ul>
    </li>
</ul>
--}}