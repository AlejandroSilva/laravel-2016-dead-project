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
                    @include('layouts.menu.menu-gestion-operacional')
                    {{-- FCV --}}
                    @include('layouts.menu.menu-fcv')
                    {{-- GESTION LOGISTICA --}}
                    @include('layouts.menu.menu-gestion-logistica')
                </ul>
            @endif

            {{-- MENU DERECHA--}}
            <ul class="nav navbar-nav navbar-right">
                @if( Auth::check() )
                    {{-- ADMINISTRACION --}}
                    @include('layouts.menu.menu-administracion')
                    {{-- USUARIO --}}
                    @include('layouts.menu.menu-usuario')
                @else
                    {{-- LOGIN --}}
                    <li><a href="/login">Login</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>