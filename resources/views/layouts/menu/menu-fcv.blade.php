@php( $mostrarInformesFinalesInventario = Auth::user()->can('fcv-verInformesFinalesInventario') )
@php( $mostrarEstadoGeneralAuditorias = Auth::user()->can('fcv-verEstadoGeneralAuditorias') )
@php( $mostrarMaestra = Auth::user()->can('fcv-verMaestra') )
@php( $mostrarMuestras = Auth::user()->can('fcv-verMuestras') )


{{-- MENU FCV --}}
@if( $mostrarInformesFinalesInventario || $mostrarEstadoGeneralAuditorias || $mostrarMaestra || $mostrarMuestras )

    <li class="#">
    <a id="drop-fcv" href="#" class="dropdown-toggle" data-toggle="dropdown">
        FCV <span class="caret"></span>
    </a>
    <ul class="dropdown-menu" aria-labelledby="drop-fcv">

        {{-- SECCION: INVENTARIOS --}}
        {{-- INFORMES FINALES FCV --}}
        @if( $mostrarInformesFinalesInventario )
            <li class="{{ Request::is('inventarios/informes-finales-fcv')? 'active': '' }}">
                <a href="{{ url('inventarios/informes-finales-fcv') }}">Informes Finales Inventarios</a>
            </li>
        @endif

        {{-- SECCION: AUDITORIAS --}}
        {{-- ESTADO GENERAL DE AUDITORIAS--}}
        @if( $mostrarEstadoGeneralAuditorias )
            <li role="separator" class="divider"></li>
            <li class="{{ Request::is('auditorias/estado-general-fcv')? 'active': '' }}">
                <a href="{{ url('auditorias/estado-general-fcv') }}">Estado general de Auditorias</a>
            </li>
        @endif

        {{-- SECCION: ARCHIVOS --}}
        @if( $mostrarMaestra || $mostrarMuestras)
            <li role="separator" class="divider"></li>
            <li class="{{ Request::is('auditorias/estado-general-fcv')? 'active': '' }}">
                <a href="{{ url('auditoria/muestras') }}">Descargar muestras</a>
            </li>
        @endif


        {{-- SECCION: ARCHIVOS --}}
        {{--@if( $mostrarMaestra || $mostrarMuestras)--}}
            {{--<li role="separator" class="divider"></li>--}}
        {{--@endif--}}
        {{-- MAESTRA DE PRODUCTOS --}}
        {{--@if( $mostrarMaestra )--}}
            {{--<li class="{{ Request::is('maestra-fcv')? 'active': '' }}">--}}
                {{--<a href="{{ url('maestra-fcv') }}">Maestra de productos FCV</a>--}}
            {{--</li>--}}
        {{--@endif--}}
        {{-- MUESTRA VENCIMIENTO FCV--}}
        {{--@if( $mostrarMuestras && Auth::user()->hasRole('Developer') )--}}
            {{--<li class="{{ Request::is('muestra-vencimiento-fcv')? 'active': '' }}">--}}
                {{--<a href="{{ url('muestra-vencimiento-fcv') }}">(DEV) Muestra de vencimiento FCV</a>--}}
            {{--</li>--}}
        {{--@endif--}}
    </ul>
</li>

@endif