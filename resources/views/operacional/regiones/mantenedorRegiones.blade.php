@extends('operacional.layoutOperacional')
@section('title', 'Regiones')
@section('content')


    <h1 class="page-header">Listado de Regiones</h1>

    <table class="table table-condensed table-bordered table-hover">
        <thead>
        <tr>
            <th>Cut Region</th>
            <th>Nombre</th>
            <th>Nombre Corto</th>
            <th>Numero</th>
            <th>Zona</th>
            <th>Opción</th>
        </tr>
        </thead>
        <tbody>
        @if(isset($regiones))


            @foreach($regiones as $region)

                <form class="form-horizontal" method="POST" action="/regiones/{{$region->cutRegion}}/editar">
                    <input name="_method" type="hidden" value="PUT">
                    <input name="_token" type="hidden" value="{{csrf_token()}}">
                    <tr>
                        <td>{{ $region->cutRegion}}</td>
                        <td>{{ $region->nombre}}</td>
                        <td>{{ $region->nombreCorto}}</td>
                        <td>{{ $region->numero}}</td>
                        <td>
                            <select name="idZona">
                                @foreach( $zonas as $zona)
                                    <option value="{{ $zona->idZona }}"    {{ $region->idZona==$zona->idZona? 'selected' :''  }}>
                                        {{ $zona->nombre}}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="submit" class="btn btn-primary btn-xs btn-block" value="Modificar">
                        </td>
                    </tr>
                </form>
            @endforeach
        @endif
        </tbody>
    </table>
@stop