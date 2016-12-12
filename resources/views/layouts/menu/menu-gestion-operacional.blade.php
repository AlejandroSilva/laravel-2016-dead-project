@php($mostrarPInventarios = Auth::user()->can('inventarios-verProgramacion') )
@php($mostrarPAuditorias  = Auth::user()->can('auditorias-verProgramacion') )

@if( $mostrarPInventarios || $mostrarPAuditorias )
    <li class="dropdown {{Request::is('programacion*') ? 'active' : ''}}">
        <a id="drop-operacional" href="#" class="dropdown-toggle" data-toggle="dropdown">
            Gestión Operacional <span class="caret"></span>
        </a>
        <ul class="dropdown-menu" aria-labelledby="drop-operacional">
            {{-- PROGRAMACION DE INVENTARIOS --}}
            @if( $mostrarPInventarios )
                <li class="{{ Request::is('inventarios/programacion-mensual')? 'active': '' }}">
                    <a href="{{ url('inventarios/programacion-mensual') }}">Programación mensual IG</a>
                </li>
                <li class="{{ Request::is('inventarios/programacion-semanal')? 'active': '' }}">
                    <a href="{{ url('inventarios/programacion-semanal') }}">Programación semanal IG</a>
                </li>
                <li role="separator" class="divider"></li>
            @endif

            {{-- PROGRAMACION DE AUDITORIAS --}}
            @if( $mostrarPAuditorias )
                <li class="{{ Request::is('auditorias/programacion-mensual')? 'active': '' }}">
                    <a href="{{ url('auditorias/programacion-mensual') }}">Programación mensual AI</a>
                </li>
                <li class="{{ Request::is('auditorias/programacion-semanal')? 'active': '' }}">
                    <a href="{{ url('auditorias/programacion-semanal') }}">Programación semanal AI</a>
                </li>
                <li role="separator" class="divider"></li>
            @endif

            {{-- NOMINAS DE CAPTADORES --}}
            @if( $mostrarPInventarios && Auth::user()->hasRole('Developer') )
                <li class="{{ Request::is('nominas/captadores')? 'active': '' }}">
                    <a href="{{ url('nominas/captadores') }}">Nominas Captadores/as (Dev)</a>
                </li>
                <li role="separator" class="divider"></li>
            @endif
        </ul>
    </li>
@endif