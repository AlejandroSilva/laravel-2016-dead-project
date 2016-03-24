@extends('operacional.layoutOperacional')

@section('title', 'Usuarios')

@section('content')
    {{--<h1 class="page-header">Usuarios</h1>--}}

    <form class="form-horizontal" method="POST" action="/personal/nuevo">
        <div class="row">
            <div class="col-sm-6">

                {{-- DATOS PERSONALES --}}
                <div class="row">
                    <h4 class="page-header">Datos personales</h4>
                </div>
                {{-- RUN --}}
                <div class="form-group">
                    <label class="col-sm-2">RUN</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="RUN" placeholder="Ej. 12.345.987-K"
                               minlength="9"
                               maxlength="12"
                               required>
                    </div>
                </div>
                {{-- Nombres --}}
                <div class="form-group">
                    <label class="col-sm-2">Nombres</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="nombre1" placeholder="Primer nombre"
                               minlength="3"
                               maxlength="20"
                               required>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="nombre2" placeholder="Segundo nombre"
                               minlength="3"
                               maxlength="20"
                               required>
                    </div>
                </div>
                {{-- Apellidos --}}
                <div class="form-group">
                    <label class="col-sm-2">Apellidos</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="apellidoPaterno" placeholder="Apellido paterno"
                               minlength="3"
                               maxlength="20"
                               required>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="apellidoMaterno" placeholder="Apellido materno"
                               minlength="3"
                               maxlength="20"
                               required>
                    </div>
                </div>
                {{-- Fecha Nacimiento--}}
                <div class="form-group">
                    <label class="col-sm-2">Fecha de Nacimiento</label>
                    <div class="col-sm-5">
                        <input type="date" class="form-control" name="fechaNacimiento" placeholder="Ej. 1985-03-01"
                               required>
                    </div>
                </div>

                {{-- Telefono 1 --}}
                <div class="form-group">
                    <label class="col-xs-2">Telefono 1</label>
                    <div class="col-xs-5">
                        <input type="text" class="form-control" name="telefono1" placeholder="Ej. +56 9 86741897">
                    </div>
                </div>
                {{-- Telefono 2 --}}
                <div class="form-group">
                    <label class="col-xs-2">Telefono 2</label>
                    <div class="col-xs-5">
                        <input type="text" class="form-control" name="telefono2" placeholder="Ej. +56 9 86741897">
                    </div>
                </div>
                {{-- Email --}}
                <div class="form-group">
                    <label class="col-xs-2">Email </label>
                    <div class="col-xs-5">
                        <input type="email" class="form-control" name="email" placeholder="Ej. jperez@seiconsultores.cl"
                               required>
                    </div>
                </div>

                {{-- OTROS --}}
                <div class="row">
                    <h4 class="page-header">Otros</h4>
                </div>
                <div class="form-group">
                    <label class="col-xs-2">Tipo</label>
                    <div class="col-xs-5">
                        <select class="form-control" disabled>
                            <option value="1">Externo</option>
                            <option value="2">Contratado por SEI</option>
                        </select>
                    </div>
                </div>
                {{-- Bloqueado--}}
                <div class="form-group">
                    <label class="col-xs-2">Bloquear</label>
                    <div class="col-xs-5">
                        <a href="#" class="btn btn-block btn-primary" disabled>Bloquear usuario</a>
                    </div>
                </div>

            </div>


            <div class="col-sm-6">
                <div class="row">
                    <h4 class="page-header">Roles</h4>
                    <p>PENDIENTE... mostrar una lista de todos los roles que existen en el sistema</p>
                    <p>se podran seleccionar uno o varios roles que puede realizar el usuario (lider, captador, reclutador, etc.)</p>
                </div>
            </div>
        </div>
        {{--<input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
        {{ csrf_field() }}



        {{-- ACEPTAR --}}
        <div class="row">
            <div class="col-sm-6">
                {{-- Mostrar mensaje de exito --}}
                @if( isset($mensaje) )
                    <div class="alert alert-success">
                        <p>{{ $mensaje }}</p>
                    </div>
                @endif

                {{-- Mostrar la lista de errores si existe--}}
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <input type="submit" class="btn btn-block btn-primary" value="Agregar">
            </div>
        </div>
    </form>

    {{--
    <p>los usuarios son personal de SEI, tienen acceso al sistema, entre ellos
        estan <b>operadores</b>, <b>lideres</b>, etc.</p>
    <ul>
        <li>ver lista usuarios SEI</li>
        <li>crear usuarios (dar de alta)</li>
        <ul>
            <li>nombre, rut</li>
            <li>rol/cargo</li>
            <li>zonas de accion</li>
        </ul>
        <li>editar usuarios</li>
        <li>dar de baja / dar de alta usuarios</li>
        <li>asignar o quitar permisos permisos</li>
    </ul>
    --}}
@stop