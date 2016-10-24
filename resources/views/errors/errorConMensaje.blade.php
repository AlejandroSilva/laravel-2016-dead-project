@extends('layouts.root')
{{--@section('title', '')--}}

@section('body')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-danger">
                    <div class="panel-heading">{{ $titulo }}</div>
                    <div class="panel-body">
                        {{ $descripcion }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
