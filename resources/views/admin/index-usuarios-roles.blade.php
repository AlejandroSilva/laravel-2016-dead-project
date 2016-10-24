@extends('layouts.root')
@section('title', 'Usuarios - Roles')

@section('body')
    <style>
        /* estilos para checkbox */
        .thEncabezadoo{
            border-top-style: hidden !important;
            border-left-style: hidden !important;

        }
        .thEncabezado{
            text-align: center;
        }
        .thEncabezado > input{
            text-align: center;
            border: 0;
            font-size: 15px;
            width: 100px;
        }
        .tdUsuarios{
            padding-left: 0 !important;
            padding-right: 0 !important;

        }
        .tdUsuarios > input{
            border: 0;
            font-size: 13px;
            text-align: center;
            width: 190px;
        }
        .tdInput{

            padding-left: 0 !important;
            padding-right: 0 !important;
            padding-bottom: 0 !important;
            padding-top: 0 !important;
        }
        .tdInput > input{
            width: 100px;
            height: 20px;


        }
    </style>

    <script type="text/javascript">
        function changeUserRole(event) {
            event.preventDefault();

            var target = event.target
            var idUsuario = target.dataset.usuario
            var idRole = target.dataset.role
            var rolFijado = target.dataset.rolasignado
            var pet = new XMLHttpRequest();

            if(rolFijado=="true"){
                console.log("eliminando rol");

                var url = '/api/usuario/'+idUsuario+'/role/'+idRole;
                pet.open('DELETE', url, true);
                pet.onreadystatechange = function(){
                    if(pet.readyState==4){
                        if(pet.status==200){
                            console.log("rol eliminado corretamnete")
                            // quitar el "checked" del control
                            target.checked = false;
                            // asignar false al dataset.permisoasginado
                            target.dataset.rolasignado = "false";
                        }
                        else
                            console.log("error al quitar rol")
                    }
                }
                pet.send();
            }
            else{
                console.log("asignando roles");
                var url = '/api/usuario/'+idUsuario+'/role/'+idRole;
                pet.open('POST', url, true);
                pet.onreadystatechange = function(){
                    if(pet.readyState==4){
                        if(pet.status==200){
                            console.log("rol agregado corretamnete")
                            // agrgar el "checked" del control
                            target.checked = true;
                            target.dataset.rolasignado = "true";
                        }
                        else
                            console.log("error al agregar el rol")
                    }
                }
                pet.send();
            }
        }
    </script>

    <div class="container-fluid">
        <div class="row">
            <h1 class="page-header">Mantenedor Usuarios-Roles</h1>

            <table class="table table-condensed table-bordered table-hover">
                    <thead>
                    {{-- header con los id de los roles --}}
                    <tr>
                        <th colspan="2" class="thEncabezadoo"></th>
                        @foreach($roles as $rol)
                            <th class="thEncabezado">
                                <input type="text" value="{{$rol->id}}" title="{{$rol->description}}" readonly>
                            </th>

                        @endforeach
                    </tr>
                    <tr>
                        <th class="thEncabezado"></th>
                        <th class="thEncabezado">Usuarios-Roles</th>
                        @foreach($roles as $rol)
                            <th class="thEncabezado">
                                <input type="text" value="{{$rol->name}}" title="{{$rol->description}}" readonly>
                            </th>
                        @endforeach
                    </tr>
                    </thead>

                    <tbody>
                        @if(isset($users))
                            @foreach($users as $user)
                                <tr>
                                    <td>{{$user->id}}</td>
                                    <td class="tdUsuarios">
                                        <input type="text" value="{{$user->nombre1}} {{$user->apellidoPaterno}}" title="rut de usuario es {{$user->usuarioRUN}}" readonly>
                                    </td>

                                    @foreach($roles as $rol)
                                        <td class="tdInput">
                                            <input type="checkbox"
                                                   {{$user->hasrole($rol->name) ? 'checked' :''}}
                                                   data-rolasignado="{{$user->hasrole($rol->name)? "true":"false"}}"
                                                   data-role="{{$rol->id}}"
                                                   data-usuario="{{$user->id}}"
                                                   onclick="changeUserRole(event);"
                                            >
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
        </div>
    </div>
