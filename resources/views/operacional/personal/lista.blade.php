@extends('operacional.layoutOperacional')

@section('title', 'Personal')

@section('content')
    <h1 class="page-header">
        Personal
        <span class="pull-right">
            <a href="{{ route('personal.nuevo') }}" class="btn btn-primary">Crear nuevo</a>
        </span>
    </h1>

    <table class="table">
        <thead>
            <tr>
                <th>id</th>
                <th>Rol</th>
                <th>RUN</th>
                <th>Nombres</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Fecha Nacimiento</th>
                <th>Telefono1</th>
                <th>Telefono2</th>
                <th>Email</th>
                <th>Opciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personal as $persona)
                <tr>
                    <td>{{ $persona->id}}</td>
                    <td>
                        <p>
                            @foreach($persona->roles as $role)
                                {{ $role->name }}
                            @endforeach
                        </p>
                    </td>
                    <td>{{ $persona->RUN }}</td>
                    <td>{{ $persona->nombre1." ".$persona->nombre2 }}</td>
                    <td>{{ $persona->apellidoPaterno }}</td>
                    <td>{{ $persona->apellidoMaterno }}</td>
                    <td>{{ $persona->fechaNacimiento }}</td>
                    <td>{{ $persona->telefono1 }}</td>
                    <td>{{ $persona->telefono2 }}</td>
                    <td>{{ $persona->email }}</td>
                    <td>
                        <a href="#" class="btn btn-primary btn-xs btn-block">
                            Editar
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop