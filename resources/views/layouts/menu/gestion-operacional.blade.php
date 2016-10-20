@if( Auth::user()->can('inventarios-verProgramacion') )
    <li class="dropdown {{Request::is('programacion*') ? 'active' : ''}}">
        <a id="drop-operacional" href="#" class="dropdown-toggle" data-toggle="dropdown">
            Gestión Operacional <span class="caret"></span>
        </a>
        <ul class="dropdown-menu" aria-labelledby="drop-operacional">
            {{-- PROGRAMACION DE INVENTARIOS --}}
            @if( Auth::user()->can('inventarios-verProgramacion') )
                <li class="dropdown-header">Inventarios</li>
                <li class="{{ Request::is('programacionIG/mensual')? 'active': '' }}">
                    <a href="{{ url('programacionIG/mensual') }}">Programación mensual IG</a>
                </li>
                <li class="{{ Request::is('programacionIG/semanal')? 'active': '' }}">
                    <a href="{{ url('programacionIG/semanal') }}">Programación semanal IG</a>
                </li>
                <li role="separator" class="divider"></li>
            @endif

            {{-- PROGRAMACION DE AUDITORIAS --}}
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
@endif