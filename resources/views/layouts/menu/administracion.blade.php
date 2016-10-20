@if( Auth::user()->hasRole('Administrador') )
    <li class="{{ "dropdown " + (Request::is('admin*') ? 'active' : '')}}">
        {{-- MENU PRINCIPAL: GESTION PRIVILEGIOS --}}
        <a id="drop-operacional" href="#" class="dropdown-toggle" data-toggle="dropdown">
            Administración<span class="caret"></span>
        </a>
        <ul class="dropdown-menu" aria-labelledby="drop-operacional">

            {{-- Cliente / Locales --}}
            @if( Auth::user()->hasRole('Developer') )
                <li class="dropdown-header">Locales</li>
                <li class="{{ Request::is('admin/mantenedor-locales')? 'active': '' }}">
                    <a href="{{ url('admin/mantenedor-locales') }}">Mantenedor de Locales (Dev)</a>
                </li>
                <li role="separator" class="divider"></li>
            @endif

            {{-- Stock --}}
            @if( Auth::user()->can('admin-actualizarStock') )
                <li class="{{ Request::is('admin/actualizar-stock')? 'active': '' }}">
                    <a href="{{ url('admin/actualizar-stock') }}">Stock de Locales</a>
                </li>
            @endif

            {{-- Mantenedores de roles y permisos --}}
            <li class="{{ Request::is('admin/permissions')? 'active': '' }}">
                <a href="{{ url('admin/permissions') }}">Mantenedor Permisos</a>
            </li>
            <li class="{{ Request::is('admin/roles')? 'active': '' }}">
                <a href="{{ url('admin/roles') }}">Mantenedor Roles</a>
            </li>
            <li role="separator" class="divider"></li>
            <li class="{{ Request::is('admin/usuarios-roles')? 'active': '' }}">
                <a href="{{ url('admin/usuarios-roles') }}">Usuarios <-> Roles</a>
            </li>
            <li class="{{ Request::is('admin/permissions-roles')? 'active': '' }}">
                <a href="{{ url('admin/permissions-roles') }}">Roles <-> Permisos</a>
            </li>

            {{-- Administrador de Personal --}}
            @if( Auth::user()->hasRole('Developer') ){{--Auth::user()->can('admin-mantenedorLocales')--}}
            <li role="separator" class="divider"></li>
            <li class="{{ Request::is('personal')? 'active': '' }}">
                <a href="{{ url('personal') }}">Mantenedor de Personal (D)</a>
            </li>
            @endif

        </ul>
    </li>
@endif