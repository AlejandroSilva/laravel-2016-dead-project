<html>
<head>
    <style>
        table, th, td {
            border: 1px solid black;
        }
    </style>
</head>
<body {{-- style="background: black; color: white"--}}>

    <p>SEI Consultores, informa que se encuentra disponible la nomina de personal para el local <b>{{ $local->nombre }}</b>,
    ubicado en {{$local->direccion->direccion}}.</p>
    <p>La fecha programada para la toma del inventario es el día <b>{{$fechaProgramada}}</b>. y la hora de presentación del Líder en el local es a las {{ $horaPresentacionLider }} horas.</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>RUN</th>
                <th>Cargo</th>
            </tr>
        </thead>
        <tbody>
            @for ($conteo=1; false; $conteo)
            @endfor

            {{-- Lider --}}
            <tr>
                <td>{{$conteo++}}</td>
                <td>{{ "$lider->nombre1 $lider->nombre2" }}</td>
                <td>{{ "$lider->usuarioRUN-$lider->usuarioDV" }}</td>
                <td>Lider</td>
            </tr>
            {{-- Supervisor (Opcional)--}}
            @if( isset($supervisor))
                <tr>
                    <td>{{$conteo++}}</td>
                    <td>{{ "$supervisor->nombre1 $supervisor->nombre2" }}</td>
                    <td>{{ "$supervisor->usuarioRUN-$supervisor->usuarioDV" }}</td>
                    <td>Supervisor</td>
                </tr>
            @endif
            {{-- Dotacion Titular --}}
            @foreach($dotacionTitular as $usuario)
                <tr>
                    <td>{{$conteo++}}</td>
                    <td>{{ "$usuario->nombre1 $usuario->nombre2" }}</td>
                    <td>{{ "$usuario->usuarioRUN-$usuario->usuarioDV" }}</td>
                    <td>Operador</td>
                </tr>
            @endforeach
            {{-- Dotacion Reemplazo --}}
            @foreach($dotacionReemplazo as $usuario)
                <tr>
                    <td>{{$conteo++}}</td>
                    <td>{{ "$usuario->nombre1 $usuario->nombre2" }}</td>
                    <td>{{ "$usuario->usuarioRUN-$usuario->usuarioDV" }}</td>
                    <td>Operador Reemplazo</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <img src="http://sig.seiconsultores.cl/logo-sei-mail.png">
</body>
</html>