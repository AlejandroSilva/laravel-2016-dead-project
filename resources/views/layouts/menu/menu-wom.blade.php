{{--@php( $mostrarEstadoGeneralAuditorias = Auth::user()->can('fcv-verEstadoGeneralAuditorias') )--}}
@php( $mostrarMaestra = Auth::user()->can('wom-verMaestra') )
@php( $mostrarArchivosRespuesta = Auth::user()->can('wom-verArchivosRespuesta') )

{{-- MENU FCV --}}
@if( $mostrarMaestra || $mostrarArchivosRespuesta)

    <li class="#">
    <a id="drop-fcv" href="#" class="dropdown-toggle" data-toggle="dropdown">
        WOM <span class="caret"></span>
    </a>
    <ul class="dropdown-menu" aria-labelledby="drop-wom">
        {{-- SECCION: AUDITORIAS --}}
        {{-- ESTADO GENERAL DE AUDITORIAS--}}
        {{--@if( $mostrarEstadoGeneralAuditorias )--}}
            {{--<li role="separator" class="divider"></li>--}}
            {{--<li class="dropdown-header">Auditorias</li>--}}
            {{--<li class="{{ Request::is('auditorias/estado-general-fcv')? 'active': '' }}">--}}
                {{--<a href="{{ url('auditorias/estado-general-fcv') }}">Estado general de Auditorias</a>--}}
            {{--</li>--}}
        {{--@endif--}}

        {{-- SECCION: ARCHIVOS --}}
        @if( $mostrarMaestra )
            <li class="dropdown-header">Archivos</li>
        @endif

        {{-- ARCHIVOS DE RESPUESTA --}}
        @if( $mostrarArchivosRespuesta )
            <li class="{{ Request::is('archivos-respuesta-wom')? 'active': '' }}">
                <a href="{{ url('archivos-respuesta-wom') }}">Archivos de respuesta WOM</a>
            </li>
        @endif

        {{-- MAESTRA DE PRODUCTOS --}}
        @if( $mostrarMaestra )
            <li class="{{ Request::is('maestra-wom')? 'active': '' }}">
                <a href="{{ url('maestra-wom') }}">Maestra de productos WOM</a>
            </li>
        @endif
    </ul>
</li>

@endif