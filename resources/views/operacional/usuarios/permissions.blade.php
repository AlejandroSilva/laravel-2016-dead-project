@extends('operacional.layoutOperacional')

@section('title', 'Permissions')

@section('content')
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

                    <tr>
                        @if(isset($permissions))
                            <th>Permisos-Roles</th>
                            @foreach($roles as $rol)

                                <th>{{$rol->name}}</th>

                            @endforeach
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                        @if(isset($permissions))
                            @foreach($permissions as $permission)
                                <tr>
                                    <td>{{$permission->name}}</td>
                                        @foreach($roles as $rol)
                                            <td><input type="checkbox"
                                                    {{$rol->perms()->whereId($permission->id)->first() ? 'checked' : ''}}
                                                    data-role="{{$rol->id}}"
                                                    data-permission="{{$permission->id}}"
                                                    data-permisoasignado="{{$rol->perms()->whereId($permission->id)->first()?"true":"false"}}"
                                                    onclick="changeUserRole(event);"
                                            ></td>
                                        @endforeach
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
