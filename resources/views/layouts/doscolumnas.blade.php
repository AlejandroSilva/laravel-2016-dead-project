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
                    <li class="{{ Request::is('inventario*') ? 'active' : '' }}">
                        <a href="{{ url('programacion') }}">Gestión Operacional</a>
                    </li>
                    <li class="">
                        <a href="#">Gestión Logistica</a>
                    </li>
                    <li class="#">
                        <a href="#">Gestión Financiera</a>
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
    {{-- Utilizado para montar el top menu con React --}}
    {{-- <div id="rc-top-menu"></div> --}}
@stop

@section('hmtl-body')
    @yield('top')
    <div class='container-fluid'>
        <div class="col-sm-2">
            {{--@yield('main-sidebar')--}}
            @include('includes.main-sidebar')
        </div>
        <div class="col-sm-10">
            @yield('main-content')
        </div>
    </div>
@stop