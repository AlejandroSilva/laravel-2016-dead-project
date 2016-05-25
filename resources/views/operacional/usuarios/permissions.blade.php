@extends('operacional.layoutOperacional')

@section('title', 'Permissions')

@section('content')
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
        .tdPermisos{
            padding-left: 0 !important;
            padding-right: 0 !important;

        }
        .tdPermisos > input{
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
            var idPermission = target.dataset.permission
            var idRole = target.dataset.role
            var permisoFijado = target.dataset.permisoasignado
            var pet = new XMLHttpRequest();

            if(permisoFijado=="true"){
                console.log("eliminando permisos");

                var url = '/api/permission/'+idPermission+'/roles/'+idRole;
                pet.open('DELETE', url, true);
                pet.onreadystatechange = function(){
                    if(pet.readyState==4){
                        if(pet.status==200){
                            console.log("permiso eliminado corretamnete")
                            // quitar el "checked" del control
                            target.checked = false;
                            // asignar false al dataset.permisoasginado
                            target.dataset.permisoasignado = "false";
                        }
                        else
                            console.log("error al quitar permiso")
                    }
                }
                pet.send();
            }else{
                console.log("asignando permisos");
                var url = '/api/permission/'+idPermission+'/role/'+idRole;
                pet.open('POST', url, true);
                pet.onreadystatechange = function(){
                    if(pet.readyState==4){
                        if(pet.status==200){
                            console.log("permiso agregado corretamnete")
                            // agrgar el "checked" del control
                            target.checked = true;
                            target.dataset.permisoasignado = "true";
                        }
                        else
                            console.log("error al agregar el permisoo")
                    }
                }
                pet.send();
            }
        }
    </script>

    <div class="container-fluid">
        <div class="row">
            <div>
                <h1 class="page-header">Mantenedor permisos roles</h1>
            </div>

            <div class="col-md-6">
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

                        {{-- header con los nombres de los roles --}}
                        <tr>
                            <th class="thEncabezado"></th>
                            <th class="thEncabezado">Permisos-Roles</th>
                            @foreach($roles as $rol)
                                <th class="thEncabezado">
                                    <input type="text" value="{{$rol->name}}" title="{{$rol->description}}" readonly>
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @if(isset($permissions))
                            @foreach($permissions as $permission)
                                <tr>
                                    <td>{{$permission->id}}</td>
                                    <td class="tdPermisos">
                                        <input type="text" value="{{$permission->name}}" title="{{$permission->description}}" readonly>
                                    </td>
                                        @foreach($roles as $rol)
                                            <td class="tdInput">
                                                <input type="checkbox"
                                                   {{$rol->perms()->whereId($permission->id)->first() ? 'checked' : ''}}
                                                   data-role="{{$rol->id}}"
                                                   data-permission="{{$permission->id}}"
                                                   data-permisoasignado="{{$rol->perms()->whereId($permission->id)->first()?"true":"false"}}"
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
    </div>
