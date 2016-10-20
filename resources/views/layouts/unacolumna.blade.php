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
                {{-- MENU IZQUIERDA --}}
                @if( Auth::check() )
                    <ul class="nav navbar-nav">
                        {{-- GESTION OPERACIONAL --}}
                        @include('layouts.menu.gestion-operacional')
                        {{-- FCV --}}
                        @include('layouts.menu.fcv')
                        {{-- ACTIVO FIJO --}}
                        @include('layouts.menu.activo-fijo')
                    </ul>
                @endif

                {{-- MENU DERECHA--}}
                <ul class="nav navbar-nav navbar-right">
                    @if( Auth::check() )
                        {{-- ADMINISTRACION --}}
                        @include('layouts.menu.administracion')
                        {{-- USUARIO --}}
                        @include('layouts.menu.usuario')
                    @else
                        {{-- LOGIN --}}
                        <li><a href="/login">Login</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@stop

@section('hmtl-body')
    @yield('top')
    <div class='container-fluid'>
        @yield('main-content')
    </div>
@stop