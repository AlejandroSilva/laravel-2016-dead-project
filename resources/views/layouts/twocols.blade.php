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
                <a class="navbar-brand" href="#">SIG</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="{{ Request::is('inventario/*') ? 'active' : '' }}">
                        <a href="{{ url('inventario/programa') }}">Gestión de Inventario</a>
                    </li>
                    <li class="{{ Request::is('usuarios/*') ? 'active' : '' }}">
                        <a href="{{ url('usuarios/usuarios') }}">Gestión de personal</a>
                    </li>
                    <li class="{{ Request::is('admin/*') ? 'active' : '' }}">
                        <a href="{{ url('admin/clientes') }}">Administracion de Clientes</a>
                    </li>
                    {{--<li class="dropdown">--}}
                        {{--<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>--}}
                        {{--<ul class="dropdown-menu">--}}
                            {{--<li><a href="#">Action</a></li>--}}
                            {{--<li><a href="#">Another action</a></li>--}}
                            {{--<li><a href="#">Something else here</a></li>--}}
                            {{--<li role="separator" class="divider"></li>--}}
                            {{--<li class="dropdown-header">Nav header</li>--}}
                            {{--<li><a href="#">Separated link</a></li>--}}
                            {{--<li><a href="#">One more separated link</a></li>--}}
                        {{--</ul>--}}
                    {{--</li>--}}
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#">Perfil</a></li>
                    <li><a href="#">Configuración</a></li>
                    <li class="activeX"><a href="#">Salir <span class="sr-only">(current)</span></a></li>
                </ul>
            </div>
        </div>
    </nav>
@stop

@section('hmtl-body')
    @yield('top')
    <div class='container-fluid'>
        <div class="row">
            <div class="col-sm-3 col-md-2 sidebar">
                @yield('sidebar')
            </div>
            <div class="col-sm-9 col-md-10 main">
                @yield('content')
            </div>
        </div>
    </div>
@stop