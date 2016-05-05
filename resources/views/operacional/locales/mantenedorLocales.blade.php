{{-- locales.listado --}}
@extends('operacional.layoutOperacional')

@section('title', 'Listado de Clientes')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-xs-6">
                <h1 class="page-header">CLientes</h1>
                <table class="table table-condensed table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Opción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach( $clientes as $cliente)
                            <tr>
                                <td>{{ $cliente->idCliente}}</td>
                                <td>{{ $cliente->nombre}}</td>
                                <td><a href="locales/cliente/{{$cliente->idCliente}}" class="btn btn-primary btn-xs btn-block">
                                        Seleccionar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                  </table>
             </div>
        </div>
    </div>