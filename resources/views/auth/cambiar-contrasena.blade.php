@extends('layouts.root')
@section('title', 'Estado general Auditorias FCV')

@section('body')
    <style>
        .input-errorDelete{
            color: orangered;
            border: 0;
            width: 800px;
        }
    </style>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Cambiar contraseña</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="/auth/cambiar-contrasena">
                            {!! csrf_field() !!}

                            <div class="form-group">
                                <label class="col-md-4 control-label">Contraseña actual</label>

                                <div class="col-md-6">
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Contraseña nueva</label>

                                <div class="col-md-6">
                                    <input type="password" class="form-control" name="newpassword" required>
                                </div>
                            </div>
                            <div class="form-group">


                                <label class="col-md-4 control-label">Repetir contraseña</label>

                                <div class="col-md-6">
                                    <input type="password" class="form-control" name="repassword" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-btn fa-sign-in"></i>Cambiar
                                    </button>
                                </div>
                            </div>

                        </form>
                        <div><input type="text" value="{{Session::get('flash-message')}}" class="input-errorDelete" readonly></div>

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
        </div>
    </div>
@endsection
