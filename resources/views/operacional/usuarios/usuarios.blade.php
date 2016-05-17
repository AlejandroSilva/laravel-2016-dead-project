@extends('operacional.layoutOperacional')

@section('title', 'Usuarios')

@section('content')
    <script type="text/javascript">
        function changeUserRole(id, role, setx) {


            if(setx==true){
                var pet = new XMLHttpRequest();
                var url = '/api/usuario/'+id+'/role/'+role;
                pet.open('DELETE', url, true);
                pet.send();
            }
            else{
                var pet = new XMLHttpRequest();
                var url = '/api/usuario/'+id+'/role/'+role;
                pet.open('POST', url, true);
                pet.send();
                //console.log("post")
            }
        }
    </script>

    <div class="container-fluid">
        <div class="row">
            <div>
                <h1>Mantenedor usuarios roles</h1>
            </div>

            <div class="col-md-6">
                <h1 class="page-header">Usuarios</h1>
                <table class="table table-condensed table-bordered table-hover">
                    <thead>

                    <tr>
                        @if(isset($roles))
                            <th>usuarios-roles</th>
                            @foreach($roles as $rol)

                                <th>{{$rol->name}}</th>

                            @endforeach
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                        @if(isset($users))
                            @foreach($users as $user)
                                <tr>
                                    <td>{{$user->nombre1}}</td>
                                    @foreach($roles as $rol)
                                        <td><input type="checkbox"
                                                   data-bool="{{$user->hasrole($rol->name)? "true":"false"}}"
                                                   {{ $user->hasrole($rol->name) ? 'checked' :'' }}
                                                   onchange="changeUserRole(evt{{$user->id}}, {{$rol->id}});"></td>
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
