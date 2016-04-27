@extends('operacional.layoutOperacional')
@section('title', 'Clientes')
@section('content')


    <h1 class="page-header">Listado de clientes</h1>

    <form class="form-horizontal" method="POST" action="/admin/clientes">
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
                    <td><input type="text" class="form-control" name="nombre" placeholder="Ejemplo: Cruz verde"
                               minlength="2"
                               maxlength="50"
                               required
                        >
                    </td>
                    <td><input type="text" class="form-control" name="nombreCorto" placeholder="Ejemplo: CV"
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

    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Nombre Corto</th>
            <th>Opción</th>
        </tr>
        </thead>
        <tbody>
        @if(isset($clientes))
            @foreach($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->idCliente}}</td>
                    <td>{{ $cliente->nombre}}</td>
                    <td>{{ $cliente->nombreCorto}}</td>
                    <td>
                        <a href="/admin/cliente/{{ $cliente->idCliente }}/editar" class="btn btn-primary btn-xs btn-block">
                            Editar
                        </a>
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
@stop