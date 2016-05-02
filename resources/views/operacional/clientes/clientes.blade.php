@extends('operacional.layoutOperacional')
@section('title', 'Clientes')
@section('content')


    <h1 class="page-header">Listado de clientes</h1>

    <form class="form-horizontal" method="POST" action="/clientes">
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

    <table class="table table-condensed table-bordered table-hover">
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
                <form class="form-horizontal" method="POST" action="clientes/cliente/{{$cliente->idCliente}}/editar">
                    <input name="_method" type="hidden" value="PUT">
                    <input name="_token" type="hidden" value="{{csrf_token()}}">
                        <tr>
                            <td>{{ $cliente->idCliente}}</td>
                            <td>
                                <div class="col-xs-5">
                                    <input type="text" class="form-control" value="{{ $cliente->nombre}}" name="nombre">
                                </div>
                            </td>

                            <td>
                                <div class="col-xs-3">
                                    <input type="text" class="form-control" value="{{ $cliente->nombreCorto}}" name="nombreCorto">
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
