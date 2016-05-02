@extends('operacional.layoutOperacional')
@section('title', 'Formatos')
@section('content')


    <h1 class="page-header">Listado de formatos</h1>

    <form class="form-horizontal" method="POST" action="/formatoLocales">
        <input name="_token" type="hidden" value="{{csrf_token()}}">
        <table>
            <thead>
            <tr>
                <th>Nombre</th>
                <th>Siglas</th>
                <th>Producción Sugerida</th>
                <th>Descripccion</th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td><input type="text" class="form-control" name="nombre" placeholder="Ejemplo: Mall"
                           minlength="2"
                           maxlength="40"
                           required
                    >
                </td>
                <td><input type="text" class="form-control" name="siglas" placeholder="Ejemplo: MALL"
                           minlength="1"
                           maxlength="10"
                           required
                    >
                </td>
                <td><input type="number" class="form-control" name="produccionSugerida" placeholder="Ejemplo: 40000">
                </td>
                <td><input type="text" class="form-control" name="descripcion" placeholder="Escriba descripcción">
                </td>
                <td><input type="submit" class="btn btn-block btn-primary" value="Agregar"></td>
            </tr>
            </tbody>

        </table>
    </form>
    @if (count($errors) > 0)
            <!-- Form Error List -->
    <div class="alert alert-danger">
        <strong>Whoops! Something went wrong!</strong>

        <br><br>

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <table class="table table-condensed table-bordered table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Siglas</th>
            <th>Producción sugerida</th>
            <th>Descripción</th>
            <th>Opción</th>
        </tr>
        </thead>
        <tbody>
        @if(isset($formatos))
            @foreach($formatos as $formatos)
                <form class="form-horizontal" method="POST" action="/formatoLocales/formato/{{$formatos->idFormatoLocal}}/editar">
                    <input name="_method" type="hidden" value="PUT">
                    <input name="_token" type="hidden" value="{{csrf_token()}}">
                    <tr>
                        <td>{{ $formatos->idFormatoLocal}}</td>
                        <td>
                            <div class="col-xs-10">
                                <input type="text" class="form-control" value="{{ $formatos->nombre}}" name="nombre">
                            </div>
                        </td>
                        <td>
                            <div class="col-xs-6">
                                <input type="text" class="form-control" value="{{ $formatos->siglas}}" name="siglas">
                            </div>
                        </td>
                        <td>
                            <div class="col-xs-6">
                                <input type="number" class="form-control" value="{{ $formatos->produccionSugerida}}" name="produccionSugerida">
                            </div>
                        </td>
                        <td>
                            <div class="col-xs-13">
                                <input type="text" class="form-control" value="{{ $formatos->descripcion }}" name="descripcion">
                            </div>
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
    @if (count($errors) > 0)
            <!-- Form Error List -->
    <div class="alert alert-danger">
        <strong>Whoops! Something went wrong!</strong>

        <br><br>

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
