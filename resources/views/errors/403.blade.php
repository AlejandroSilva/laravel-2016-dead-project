@extends('layouts.root')
{{--@section('title', '')--}}

@section('body')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-danger">
                    <div class="panel-heading">Acceso restringido</div>

                    <div class="panel-body">
                        Su cuenta no tiene los privilegios para realizar esta acción, si esto es un error contacte con el departamento de informática.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
