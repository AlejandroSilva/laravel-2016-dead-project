@extends('operacional.layoutOperacional')
@section('title', 'Cliente')
@section('content')


    <h1 class="page-header">Editar cliente</h1>

    <form class="form-horizontal" method="POST" action="/admin/cliente/{{$cliente->idCliente}}/editar">
        <input name="_method" type="hidden" value="PUT">
        <input name="_token" type="hidden" value="{{csrf_token()}}">
        <table>
            <thead>
            <tr>
                <th>Nombre</th>
                <th>Nombre Corto</th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td><input type="text" class="form-control" name="nombre" value="{{$cliente->nombre}}"
                           minlength="2"
                           maxlength="50"
                           required
                    >
                </td>
                <td><input type="text" class="form-control" name="nombreCorto" value="{{$cliente->nombreCorto}}"
                           minlength="1"
                           maxlength="10"
                           required
                    >
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
