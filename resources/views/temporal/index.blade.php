<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Subir temporal</title>
</head>
<body>
{{--    {!! Form::open(array('url'=>'completado','method'=>'POST', 'files'=>true)) !!}--}}
    <form action="/completado" method="POST" accept-charset="UTF-8" enctype="multipart/form-data">

        <p>seleccione el archivo</p>
        <input multiple name="thefile" type="file">

        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit" value="Enviar">
    </form>
    {{--{!! Form::close() !!}--}}
</body>
</html>