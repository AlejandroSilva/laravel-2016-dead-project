@extends('operacional.layoutOperacional')
@section('title', 'Regiones')
@section('content')


    <div class="container">
        <div class="row">
            <div class="cold-md-12">
                <h1 class="page-header">Regiones</h1>
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
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h1>Zonas</h1>
                <table class="table table-condensed table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Opción</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($zonas))
                        @foreach($zonas as $zona)
                            <form class="form-horizontal" method="POST" action="regiones/zona/{{$zona->idZona}}/editar">
                                <input name="_method" type="hidden" value="PUT">
                                <input name="_token" type="hidden" value="{{csrf_token()}}">
                                <tr>
                                    <td>{{ $zona->idZona}}</td>
                                    <td>

                                            <input type="text" class="form-control" value="{{ $zona->nombre}}" name="nombre">

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
            </div>
            <div class="col-md-4">
                <h1>Agregar</h1>
                <form class="form-horizontal" method="POST" action="/regiones/zona">
                    <input name="_token" type="hidden" value="{{csrf_token()}}">
                    <table>
                        <tbody>
                        <tr>
                            <th>Nombre:</th>
                            <td>
                                <div class="col-xs-14">
                                    <input type="text" class="form-control" name="nombre" placeholder="Ejemplo: Norte Grande"
                                           minlength="2"
                                           maxlength="50"
                                           required
                                    >
                                </div>
                            </td>
                            <td><input type="submit" class="btn btn-block btn-primary" value="Agregar"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>

    </div>  {{-- container --}}
