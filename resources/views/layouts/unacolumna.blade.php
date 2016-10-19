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
                <a class="navbar-brand" href="/">SIG</a>
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
                                {{-- INVENTARIOS --}}
                                @if( Auth::user()->can('programaInventarios_ver') )
                                    <li class="dropdown-header">Inventarios</li>
                                    <li class="{{ Request::is('programacionIG/mensual')? 'active': '' }}">
                                        <a href="{{ url('programacionIG/mensual') }}">Programación mensual IG</a>
                                    </li>
                                    <li class="{{ Request::is('programacionIG/semanal')? 'active': '' }}">
                                        <a href="{{ url('programacionIG/semanal') }}">Programación semanal IG</a>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                @endif

                                {{-- AUDITORIAS --}}
                                @if( Auth::user()->can('programaAuditorias_ver') )
                                    <li class="dropdown-header">Auditorias</li>
                                    <li class="{{ Request::is('programacionAI/mensual')? 'active': '' }}">
                                        <a href="{{ url('programacionAI/mensual') }}">Programación mensual AI</a>
                                    </li>
                                    <li class="{{ Request::is('programacionAI/semanal')? 'active': '' }}">
                                        <a href="{{ url('programacionAI/semanal') }}">Programación semanal AI</a>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                @endif

                                {{-- Nominas --}}
                                @if( Auth::user()->hasRole('SupervisorInventario') )
                                    <li class="{{ Request::is('nominas/captadores')? 'active': '' }}">
                                        <a href="{{ url('nominas/captadores') }}">Nominas Captadores/as</a>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                @endif
                            </ul>
                        </li>

                        {{-- MENU FCV--}}
                        @if( Auth::check() )
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
                        @endif

                        @if( Auth::user()->can('activoFijo-verModulo') )
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

                        {{--@if( Auth::user()->hasRole('Administrador') )--}}
                            {{--<li class="#">--}}
                                {{--<a href="#">Gestión Financiera</a>--}}
                            {{--</li>--}}
                        {{--@endif--}}
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
                                    <a href="{{ url('admin/usuarios-roles') }}">Usuarios <-> Roles</a>
                                </li>
                                <li class="{{ Request::is('admin/permissions-roles')? 'active': '' }}">
                                    <a href="{{ url('admin/permissions-roles') }}">Roles <-> Permisos</a>
                                </li>

                                {{-- Cliente / Locales --}}
                                @if( Auth::user()->hasRole('Developer') )
                                    <li role="separator" class="divider"></li>
                                    <li class="{{ Request::is('admin/mantenedor-locales')? 'active': '' }}">
                                        <a href="{{ url('admin/mantenedor-locales') }}">Mantenedor de Locales (Dev)</a>
                                    </li>
                                @endif

                                @if( Auth::user()->can('admin-actualizarStock') )
                                    <li class="{{ Request::is('admin/stock')? 'active': '' }}">
                                        <a href="{{ url('admin/stock') }}">Stock de Locales</a>
                                    </li>
                                @endif

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
                    @if( Auth::check() )
                        <li>
                            <a id="drop-user" href="#" class="dropdown-toggle" data-toggle="dropdown">
                                {{ Auth::user()->nombre1 }} <span class="caret"></span>
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