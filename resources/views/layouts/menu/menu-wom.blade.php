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

        {{-- ARCHIVOS DE RESPUESTA --}}
        @if( $mostrarArchivosRespuesta )
            <li class="dropdown-header">Archivos de respuesta</li>
            <li class="{{ Request::is('archivos-respuesta-wom')? 'active': '' }}">
                <a href="{{ url('archivos-respuesta-wom') }}">Ver archivos de respuesta WOM</a>
            </li>
            <li class="{{ Request::is('agregar-archivos-respuesta-wom')? 'active': '' }}">
                <a href="{{ url('agregar-archivos-respuesta-wom') }}">Agregar archivo de respuesta WOM</a>
            </li>
        @endif

        {{-- MAESTRA DE PRODUCTOS --}}
        @if( $mostrarMaestra )
            <li class="dropdown-header">Maestra de Productos</li>
            <li class="{{ Request::is('maestra-wom')? 'active': '' }}">
                <a href="{{ url('maestra-wom') }}">Maestra de productos WOM</a>
            </li>
        @endif
    </ul>
</li>

@endif