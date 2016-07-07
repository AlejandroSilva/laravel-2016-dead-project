{{--layout/twocolumns--}}
@extends('layouts.root')

@section('top')
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">Oportunidad</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    @if( Auth::check() )
                        <li class="{{ "dropdown " + (Request::is('inventario*') ? 'active' : '') }}">
                            {{-- MENU PRINCIPAL: GESTION OPERACIONAL --}}
                            <a id="drop-operacional" href="#" class="dropdown-toggle" data-toggle="dropdown">
                                Gestión Operacional <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="drop-operacional">
                                {{-- PROGRAMACIÓN INVENTARIO GENERAL --}}
                                @if( Auth::user()->can('programaInventarios_ver') )
                                    <li class="{{ Request::is('programacionIG/mensual')? 'active': '' }}">
                                        <a href="{{ url('programacionIG/mensual') }}">Programación mensual IG</a>
                                    </li>
                                    <li class="{{ Request::is('programacionIG/semanal')? 'active': '' }}">
                                        <a href="{{ url('programacionIG/semanal') }}">Programación semanal IG</a>
                                    </li>
                                @endif

                                {{-- PROGRAMACIÓN AUDITORIA INVENTARIO   --}}
                                @if( Auth::user()->can('programaAuditorias_ver') )
                                    <li role="separator" class="divider"></li>
                                    <li class="{{ Request::is('programacionAI')? 'active': '' }}">
                                        <a href="{{ url('programacionAI') }}">Estado general Auditorias</a>
                                    </li>
                                    <li class="{{ Request::is('programacionAI/mensual')? 'active': '' }}">
                                        <a href="{{ url('programacionAI/mensual') }}">Programación mensual AI</a>
                                    </li>
                                    <li class="{{ Request::is('programacionAI/semanal')? 'active': '' }}">
                                        <a href="{{ url('programacionAI/semanal') }}">Programación semanal AI</a>
                                    </li>
                                @endif

                                {{-- Nominas --}}
                                @if( Auth::user()->hasRole('SupervisorInventario') )
                                    <li role="separator" class="divider"></li>
                                    <li class="{{ Request::is('nominas/captadores')? 'active': '' }}">
                                        <a href="{{ url('nominas/captadores') }}">Nominas Captadores/as</a>
                                    </li>
                                @endif

                                {{-- GEO --}}
                                @if( Auth::user()->hasRole('Developer') )
                                    <li role="separator" class="divider"></li>
                                    <li class="{{ Request::is('geo/')? 'active': '' }}">
                                        <a href="{{ url('geo/') }}">(D) GEO gestor</a>
                                    </li>
                                    {{-- ya no se crean inventarios desde el menu, eliminar esto --}}
                                    {{--<li class="{{ Request::is('inventario/nuevo')? 'active': '' }}">--}}
                                    {{--<a href="{{ url('inventario/nuevo') }}">Nuevo Inventario</a>--}}
                                    {{--</li>--}}
                                @endif
                            </ul>
                        </li>

                        @if( Auth::user()->hasRole('Administrador') )
                            <li class="">
                                <a id="drop-logistica" href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    Gestión Logistica <span class="caret"></span>
                                </a>
                                @if( Auth::user()->hasRole('Developer') )
                                    <ul class="dropdown-menu" aria-labelledby="drop-logistica">
                                        <li class="{{ Request::is('activo-fijo')? 'active': '' }}">
                                            <a href="{{ url('activo-fijo') }}">Control Activos Fijos</a>
                                        </li>
                                    </ul>
                                @endif
                            </li>
                        @endif

                        @if( Auth::user()->hasRole('Administrador') )
                            <li class="#">
                                <a href="#">Gestión Financiera</a>
                            </li>
                        @endif
                    @endif
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    @if( Auth::check() && Auth::user()->hasRole('Administrador') )
                        <li class="{{ "dropdown " + (Request::is('admin*') ? 'active' : '')}}">
                            {{-- MENU PRINCIPAL: GESTION PRIVILEGIOS --}}
                            <a id="drop-operacional" href="#" class="dropdown-toggle" data-toggle="dropdown">
                                Administración<span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="drop-operacional">
                                {{-- Mantenedores de roles y permisos --}}
                                <li class="{{ Request::is('admin/permissions')? 'active': '' }}">
                                    <a href="{{ url('admin/permissions') }}">Mantenedor Permisos</a>
                                </li>
                                <li class="{{ Request::is('admin/roles')? 'active': '' }}">
                                    <a href="{{ url('admin/roles') }}">Mantenedor Roles</a>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li class="{{ Request::is('admin/usuarios-roles')? 'active': '' }}">
                                    <a href="{{ url('admin/usuarios-roles') }}">Asignación Usuarios/Roles</a>
                                </li>
                                <li class="{{ Request::is('admin/permissions-roles')? 'active': '' }}">
                                    <a href="{{ url('admin/permissions-roles') }}">Asignación Roles/Permisos</a>
                                </li>

                                {{-- Cliente / Locales --}}
                                @if( Auth::user()->hasRole('Developer') )
                                    <li role="separator" class="divider"></li>
                                    <li class="{{ Request::is('admin/locales')? 'active': '' }}">
                                        <a href="{{ route('admin.locales.lista') }}">Mantenedor de Locales (D)</a>
                                    </li>
                                @endif

                                @if( Auth::user()->can('admin-actualizarStock') )
                                    <li class="{{ Request::is('admin/stock')? 'active': '' }}">
                                        <a href="{{ url('admin/stock') }}">Stock de Locales</a>
                                    </li>
                                @endif

                            </ul>
                        </li>
                    @endif
                    @if( Auth::check() )
                        <li>
                            <a id="drop-user" href="#" class="dropdown-toggle" data-toggle="dropdown">
                                Bienvenido {{ Auth::user()->nombre1 }} <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="drop-operacional">
                                {{-- Menu usuario --}}
                                @if( Auth::user() )
                                    <li class="{{ Request::is('user/changePassword')? 'active': '' }}">
                                        <a href="{{ url('user/changePassword') }}">Cambiar contraseña</a>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    {{--<li><a href="#">Configuración</a></li>--}}
                                    <li><a href="/logout">Cerrar Sesión</a></li>
                                @endif
                            </ul>
                        </li>
                    @else
                        <li><a href="/login">Login</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    {{-- Utilizado para montar el top menu con React --}}
    {{-- <div id="rc-top-menu"></div> --}}
@stop

@section('hmtl-body')
    @yield('top')
    <div class='container-fluid'>
        @yield('main-content')
    </div>
@stop