@extends('layouts.root')
@section('title', 'Mantenedor Clientes')

@section('body')
    <style>
        .td-input > input{
            width: 100%;
        }
        .tr-nuevo > td{
            padding-top: 3em !important;
            border: none !important;
        }

    </style>

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">


            <h1 class="page-header">Mantenedor de Clientes</h1>
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>NombreCorto</th>
                        <th colspan="2">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientes as $cliente)
                        <tr>
                            <form method="POST" action="/api/cliente/{{ $cliente->idCliente }}">
                                <input name="_method" type="hidden" value="PUT">
                                <td>
                                    {{ $cliente->idCliente }}
                                    <input type="hidden" value="{{ $cliente->idCliente }}" name="id">
                                </td>
                                <td class="td-input">
                                    <input type="text" value="{{ $cliente->nombre }}" name="nombre" class="">
                                </td>
                                <td class="td-input">
                                    <input type="text" value="{{ $cliente->nombreCorto }}" name="nombreCorto">
                                </td>
                                <td>
                                    <input type="submit" class="btn btn-primary btn-xs btn-block" value="Modificar">
                                </td>
                            </form>
                            {{-- boton eliminar opcional (solo para clientes que no tienen locales --}}
                            @if( $cliente->locales->count()==0)
                                <form method="POST" action="/api/cliente/{{ $cliente->idCliente }}">
                                    <input name="_method" type="hidden" value="DELETE">
                                    <td>
                                        <input type="submit" class="btn btn-primary btn-xs btn-block" value="Eliminar" name="eliminar">
                                    </td>
                                </form>
                            @endif
                        </tr>
                    @endforeach

                    <tr class="tr-nuevo">
                        <form method="POST" action="/cliente">
                            {{ csrf_field() }}
                            <td></td>
                            <td class="td-input">
                                <input type="text" value="{{ old('nombre') }}" name="nombre">
                            </td>
                            <td class="td-input">
                                <input type="text" value="{{ old('nombreCorto') }}" name="nombreCorto">
                            </td>
                            <td>
                                <input type="submit" class="btn btn-success btn-xs btn-block" value="Agregar nuevo">
                            </td>
                        </form>
                    </tr>
                </tbody>
            </table>
                @if (count($errors->nuevo->all()) > 0)
                    <div class="alert alert-danger">
                        <ul>
                        @if(isset($errors))
                            @foreach ($errors->nuevo->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        @endif
                        </ul>
                    </div>
                @endif
        </div>
    </div>
@stop