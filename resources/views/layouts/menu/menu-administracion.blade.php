@php( $mostrarAdministrarLocales  = Auth::user()->can('administrar-locales') )
@php( $mostrarAdministrarStock    = Auth::user()->can('administrar-stock') )
@php( $mostrarAdministrarPermisos = Auth::user()->can('administrar-permisos') )
@php( $mostrarAdministrarPersonal = Auth::user()->can('administrar-personal') )

{{-- si puede ver cualquiera de los submenus, entonces mostrar el menu general--}}
@if( $mostrarAdministrarLocales || $mostrarAdministrarStock || $mostrarAdministrarPermisos || $mostrarAdministrarPersonal )

    <li class="dropdown {{ Request::is('admin*')? 'active':'' }}">
        <a id="drop-operacional" href="#" class="dropdown-toggle" data-toggle="dropdown">
            Administración<span class="caret"></span>
        </a>
        <ul class="dropdown-menu" aria-labelledby="drop-operacional">

            {{-- LOCALES --}}
            @if( $mostrarAdministrarLocales || $mostrarAdministrarStock)
                <li class="dropdown-header">Locales</li>
                @if( $mostrarAdministrarLocales && Auth::user()->hasRole('Administrador') )
                    <li class="{{ Request::is('admin/mantenedor-clientes')? 'active': '' }}">
                        <a href="{{ url('admin/mantenedor-clientes') }}">Mantenedor de Clientes</a>
                    </li>
                @endif
                {{-- Mantenedor Locales --}}
                @if( $mostrarAdministrarLocales )
                    <li class="{{ Request::is('admin/mantenedor-locales')? 'active': '' }}">
                        <a href="{{ url('admin/mantenedor-locales') }}">Mantenedor de Locales</a>
                    </li>
                @endif
                {{-- Mantenedor Correos --}}
                @if( $mostrarAdministrarLocales && Auth::user()->hasRole('Developer') )
                    <li class="{{ Request::is('admin/mantenedor-correos')? 'active': '' }}">
                        <a href="{{ url('admin/mantenedor-correos') }}">Mantenedor de Correos</a>
                    </li>
                @endif
                {{-- Stock de locales --}}
                @if( $mostrarAdministrarStock )
                    <li class="{{ Request::is('admin/actualizar-stock')? 'active': '' }}">
                        <a href="{{ url('admin/actualizar-stock') }}">Stock de Locales</a>
                    </li>
                @endif
                <li role="separator" class="divider"></li>
            @endif


            {{-- ROLES Y PERMISOS --}}
            @if( $mostrarAdministrarPermisos )
                <li class="dropdown-header">Roles y Permisos</li>
                <li class="{{ Request::is('admin/permissions')? 'active': '' }}">
                    <a href="{{ url('admin/permissions') }}">Mantenedor Permisos</a>
                </li>
                <li class="{{ Request::is('admin/roles')? 'active': '' }}">
                    <a href="{{ url('admin/roles') }}">Mantenedor Roles</a>
                </li>
                <li class="{{ Request::is('admin/usuarios-roles')? 'active': '' }}">
                    <a href="{{ url('admin/usuarios-roles') }}">Usuarios <-> Roles</a>
                </li>
                <li class="{{ Request::is('admin/permissions-roles')? 'active': '' }}">
                    <a href="{{ url('admin/permissions-roles') }}">Roles <-> Permisos</a>
                </li>
                <li role="separator" class="divider"></li>
            @endif

            {{-- Administrador de Personal --}}
            @if( $mostrarAdministrarPersonal )
                <li class="dropdown-header">Personal</li>
                <li class="{{ Request::is('admin/personal')? 'active': '' }}">
                    <a href="{{ url('admin/personal') }}">Mantenedor de Personal</a>
                </li>
            @endif

        </ul>
    </li>

@endif