<?php use Illuminate\Support\Facades\Input; ?>
@extends('operacional.layoutOperacional')

@section('title', 'MantenedorRoles')

@section('content')
    <style>
        /* estilo para errores */
        .input-error{
            border-color: orangered;
            color: orangered;
        }

        /* Columna con el idPermiso */
        .thIdLocal {
            font-size: 15px;
            text-align: center;
        }
        .tdIdLocal {
            font-size: 13px;
            text-align: center;
        }

        /* Columna con la nombre */
        .thNombre {
            font-size: 15px;
            text-align: center;
        }
        .tdNombre {
            font-size: 15px;
            padding-left: 0 !important;
            padding-right: 0 !important;
            width: 100px;
        }
        .tdNombre > input{
            width: 220px;
            text-align: center;

        }
        .tdErrorEliminar {
            border-top-style: hidden !important;
            border-right-style: hidden !important;
            border-bottom-style: hidden !important;
            width: 300px !important;

        }

        .tdErrorEliminar > input{
            text-align: left;
            color: orangered;
            border: 0;
            width: 600px;
        }

        /* Columna con la Descripcion */
        .thDescripcion {
            font-size: 15px;
            text-align: center;
        }
        .tdDescripcion {
            font-size: 15px;
            padding-left: 0 !important;
            padding-right: 0 !important;
            width: 300px;
            text-align: center;
        }
        .tdDescripcion > input{
            width: 300px;
            text-align: center;
        }

        /* Columna con la opcion*/
        .thOpcion {
            font-size: 15px;
            text-align: center;
        }
        .tdOpcion {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdEliminar {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdEliminar >

    </style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <h1 class="page-header">Mantenedor de Roles</h1>
            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <th class="thIdLocal">ID</th>
                        <th class="thNombre">Nombre</th>
                        <th class="thDescripcion">Descripción</th>
                        <th class="thOpcion">Opcion</th>
                        <th class="thOpcion">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                @if(isset($roles))
                    @foreach($roles as $role)
                        <tr>
                            <form method="POST" action="/api/role/{{$role->id}}">
                                <input name="_method" type="hidden" value="PUT">
                                <td class="tdIdLocal">{{$role->id}}</td>
                                    <input type="hidden" value="{{ $role->id}}" name="id">
                                <td class="tdNombre">
                                    <input type="text" value="{{$role->name}}" name="name"
                                           class="{{ $role->id == Input::old('id') && $errors->error->has('name')? 'input-error' : '' }}"
                                    >
                                </td>
                                <td class="tdDescripcion">
                                    <input type="text" value="{{$role->description}}" name="description">
                                </td>
                                <td class="tdOpcion">
                                    <input type="submit" class="btn btn-primary btn-xs btn-block" value="Modificar">
                                </td>
                            </form>
                            <form method="POST" action="/api/role/{{$role->id}}">
                                <input name="_method" type="hidden" value="DELETE">
                                <td class="tdOpcion">
                                    <input type="submit" class="btn btn-primary btn-xs btn-block" value="Eliminar" name="eliminar"
                                    >
                                </td>
                                <td class="tdErrorEliminar">
                                    <input type="text" value="{{$errors->errorEliminar->first()==$role->id?'El rol no puede ser eliminado, se encuentra asignado a uno o mas usuarios':''}}"
                                           readonly>
                                </td>
                            </form>
                        </tr>
                    @endforeach
                @endif

                </tbody>
            </table>
            @if (count($errors->error) > 0)
                <div class="alert alert-danger">
                    <strong>Ha ocurrido un problema</strong>
                    <br><br>
                    <ul>
                        @if(isset($errors))
                            @foreach ($errors->error->all() as $error)
                                <li>{{ $error}}</li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            @endif
        </div>

    </div>
    <div class="row">
        <div class="col-md-6">
            <h4>Agregar</h4>
            <table class="table table-condensed table-bordered table-hover">
                <thead>
                <tr>
                    <th class="thNombre">Nombre</th>
                    <th class="thDescripcion">Descripción</th>
                    <th class="thOpcion">Opción</th>
                </tr>
                </thead>
                <tbody>
                    <form method="POST" action="/api/roles">
                        <tr>
                            <td class="tdNombre">
                                <input type="text" name="name" placeholder="inserte texto" required
                                       class="{{ (isset($errors) && $errors->has('name'))? 'input-error' : '' }}"
                                >
                            </td>
                            <td class="tdDescripcion">
                                <input type="text" name="description" placeholder="inserte texto">
                            </td>
                            <td class="tdOpcion">
                                <input type="submit" class="btn btn-primary btn-xs btn-block" value="Agregar">
                            </td>
                        </tr>
                    </form>
                </tbody>
            </table>
            @if (count($errors) > 0)
             <div class="alert alert-danger">
                <strong>Ha ocurrido un problema</strong>

                <br><br>

                <ul>
                    @if(isset($errors))
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    @endif

                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
