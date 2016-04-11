{{--layout/twocolumns--}}
@extends('layouts.root')

@section('top')
    <nav class="navbar navbar-default navbar-inverse navbar-fixed-top">
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
                                <li class="{{ Request::is('programacionIG/mensual')? 'active': '' }}">
                                    <a href="{{ url('programacionIG/mensual') }}">Programación mensual IG</a>
                                </li>
                                <li class="{{ Request::is('programacionIG/semanal')? 'active': '' }}">
                                    <a href="{{ url('programacionIG/semanal') }}">Programación semanal IG</a>
                                </li>
                                <li role="separator" class="divider"></li>

                                {{-- PROGRAMACIÓN AUDITORIA INVENTARIO   --}}
                                <li class="{{ Request::is('programacionAI/mensual')? 'active': '' }}">
                                    <a href="{{ url('programacionAI/mensual') }}">Programación mensual AI</a>
                                </li>
                                <li class="{{ Request::is('programacionAI/semanal')? 'active': '' }}">
                                    <a href="{{ url('programacionAI/semanal') }}">Programación semanal AI</a>
                                </li>
                                <li role="separator" class="divider"></li>


                                {{-- INVENTARIO --}}
                                <li class="{{ Request::is('inventario/lista')? 'active': '' }}">
                                    <a href="{{ url('inventario/lista') }}">Lista de inventarios</a>
                                </li>
                                {{-- ya no se crean inventarios desde el menu, eliminar esto --}}
                                {{--<li class="{{ Request::is('inventario/nuevo')? 'active': '' }}">--}}
                                    {{--<a href="{{ url('inventario/nuevo') }}">Nuevo Inventario</a>--}}
                                {{--</li>--}}
                                <li role="separator" class="divider"></li>

                                {{-- Nominas --}}
                                <li class="{{ Request::is('nominas')? 'active': '' }}">
                                    <a href="{{ url('nominas') }}">Nominas</a>
                                </li>
                                <li class="{{ Request::is('nomFinales')? 'active': '' }}">
                                    <a href="{{ url('nomFinales') }}">Nominas Finales</a>
                                </li>
                                <li role="separator" class="divider"></li>

                                {{-- Personal --}}
                                <li class="{{ Request::is('personal/lista')? 'active': '' }}">
                                    <a href="{{ url('personal/lista') }}">Lista de Personal</a>
                                </li>
                                <li class="{{ Request::is('personal/nuevo')? 'active': '' }}">
                                    <a href="{{ route('personal.nuevo') }}">Nuevo Personal</a>
                                </li>
                            </ul>
                        </li>

                        <li class="">
                            <a href="#">Gestión Logistica</a>
                        </li>
                        <li class="#">
                            <a href="#">Gestión Financiera</a>
                        </li>
                    @endif
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    @if( Auth::check() )
                        <li><a href="#">Bienvenido {{ Auth::user()->nombre1 }}</a></li>
                        {{--<li><a href="#">Configuración</a></li>--}}
                        <li><a href="/logout">Cerrar Sesión</a></li>
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
        {{--<div class="col-sm-2">--}}
            {{--@include('includes.main-sidebar')--}}
        {{--</div>--}}
        {{--<div class="col-sm-12">--}}
            @yield('main-content')
        {{--</div>--}}
    </div>
@stop