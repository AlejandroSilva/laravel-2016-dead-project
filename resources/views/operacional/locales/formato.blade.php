@extends('operacional.layoutOperacional')
@section('title', 'Formato')
@section('content')


    <h1 class="page-header">Editar formato</h1>

    <form class="form-horizontal" method="POST" action="/formatoLocales/formato/{{$formato->idFormatoLocal}}/editar">
        <input name="_method" type="hidden" value="PUT">
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
                <td><input type="text" class="form-control" name="nombre" value="{{$formato->nombre }}"
                           minlength="2"
                           maxlength="40"
                           required
                    >
                </td>
                <td><input type="text" class="form-control" name="siglas" value="{{$formato->siglas }}"
                           minlength="2"
                           maxlength="10"
                           required
                    >
                </td>
                <td><input type="number" class="form-control" name="produccionSugerida" value="{{$formato->produccionSugerida }}">
                </td>
                <td><input type="text" class="form-control" name="descripcion" placeholder="Escriba descripcción" value="{{$formato->descripcion }}">
                </td>
                <td><input type="submit" class="btn btn-block btn-primary" value="Modificar"></td>
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
