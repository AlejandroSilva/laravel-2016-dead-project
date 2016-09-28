@extends('layouts.unacolumna')
@section('main-content')
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-success">
                <div class="panel-heading">{{ $titulo }}</div>
                <div class="panel-body">
                    {{ $descripcion }}
                </div>
            </div>
        </div>
    </div>
@endsection