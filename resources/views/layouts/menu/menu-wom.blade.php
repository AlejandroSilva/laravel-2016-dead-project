{{--@php( $mostrarEstadoGeneralAuditorias = Auth::user()->can('fcv-verEstadoGeneralAuditorias') )--}}
@php( $mostrarMaestra = Auth::user()->can('wom-verMaestra') )
@php( $mostrarArchivosRespuesta = Auth::user()->can('wom-verArchivosRespuesta') )
@php( $agregarArchivosRespuesta = Auth::user()->can('wom-subirArchivosRespusta') )

{{-- MENU FCV --}}
@if( $mostrarMaestra || $mostrarArchivosRespuesta || $agregarArchivosRespuesta)

    <li class="#">
    <a id="drop-fcv" href="#" class="dropdown-toggle" data-toggle="dropdown">
        WOM <span class="caret"></span>
    </a>
    <ul class="dropdown-menu" aria-labelledby="drop-wom">

        {{-- ARCHIVOS DE RESPUESTA --}}
        @if( $mostrarArchivosRespuesta )
            <li class="{{ Request::is('archivos-respuesta-wom')? 'active': '' }}">
                <a href="{{ url('archivos-respuesta-wom') }}">Ver archivos de respuesta WOM</a>
            </li>
            @if( $agregarArchivosRespuesta )
                <li class="{{ Request::is('agregar-archivos-respuesta-wom')? 'active': '' }}">
                    <a href="{{ url('agregar-archivos-respuesta-wom') }}">Agregar archivo de respuesta WOM</a>
                </li>
            @endif
        @endif

        {{-- MAESTRA DE PRODUCTOS --}}
        @if( $mostrarMaestra )
            <li role="separator" class="divider"></li>
            <li class="{{ Request::is('maestra-wom')? 'active': '' }}">
                <a href="{{ url('maestra-wom') }}">Ver maestra de productos WOM</a>
            </li>
        @endif
    </ul>
</li>

@endif