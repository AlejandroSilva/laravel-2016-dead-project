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