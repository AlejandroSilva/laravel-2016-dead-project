<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- no cache --}}
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
    <meta http-equiv="pragma" content="no-cache" />
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

    <ul>
        @foreach ($archivos as $archivo)
            <li><a href="/descargar-otro/{{ $archivo->getFilename()  }}" download>{{ $archivo->getFilename() }}</a></li>
        @endforeach
    </ul>
</body>
</html>