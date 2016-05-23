<html>
<head></head>
<body>
    <p>SEI Consultores, informa nomina de personal para inventario general <b>{{ $local->nombre }}</b>, con horario de
        presentación Líder: <b>{{ $horaPresentacionLider }} hrs</b>, y equipo <b>{{ $horaPresentacionLider }} hrs</b>, en
        el local ubicado en <b>{{$local->direccion->direccion}}</b> es la siguiente:</p>

    <div style="margin-top: 32px; margin-bottom: 32px;">
        <table style="max-width: 400px; display: inline-block; vertical-align: middle;
                    margin-left: 32px; margin-right: 32px;
                    border: 1px solid #ddd">
            <thead>
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px;">#</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Nombre</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">RUN</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Cargo</th>
                </tr>
            </thead>
            <tbody>
                {{-- truco para asignar una variable "local" en blade --}}
                @for ($conteo=1; false; $conteo)
                @endfor

                {{-- Lider --}}
                @if( isset($lider))
                    <tr>
                        <td style="border: 1px solid #ddd;">{{$conteo++}}</td>
                        <td style="border: 1px solid #ddd;">{{ "$lider->nombre1 $lider->nombre2" }}</td>
                        <td style="border: 1px solid #ddd;">{{ "$lider->usuarioRUN-$lider->usuarioDV" }}</td>
                        <td style="border: 1px solid #ddd;">Lider</td>
                    </tr>
                @endif
                {{-- Supervisor (Opcional)--}}
                @if( isset($supervisor))
                    <tr>
                        <td style="border: 1px solid #ddd;">{{$conteo++}}</td>
                        <td style="border: 1px solid #ddd;">{{ "$supervisor->nombre1 $supervisor->nombre2" }}</td>
                        <td style="border: 1px solid #ddd;">{{ "$supervisor->usuarioRUN-$supervisor->usuarioDV" }}</td>
                        <td style="border: 1px solid #ddd;">Supervisor</td>
                    </tr>
                @endif
                {{-- Dotacion Titular --}}
                @foreach($dotacionTitular as $usuario)
                    <tr>
                        <td style="border: 1px solid #ddd;">{{$conteo++}}</td>
                        <td style="border: 1px solid #ddd;">{{ "$usuario->nombre1 $usuario->nombre2" }}</td>
                        <td style="border: 1px solid #ddd;">{{ "$usuario->usuarioRUN-$usuario->usuarioDV" }}</td>
                        <td style="border: 1px solid #ddd;">Operador</td>
                    </tr>
                @endforeach
                {{-- Dotacion Reemplazo --}}
                @foreach($dotacionReemplazo as $usuario)
                    <tr>
                        <td style="border: 1px solid #ddd;">{{$conteo++}}</td>
                        <td style="border: 1px solid #ddd;">{{ "$usuario->nombre1 $usuario->nombre2" }}</td>
                        <td style="border: 1px solid #ddd;">{{ "$usuario->usuarioRUN-$usuario->usuarioDV" }}</td>
                        <td style="border: 1px solid #ddd;">Operador Reemplazo</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="max-width: 180px; display: inline-block; vertical-align: top;">
            {{-- Imagen del Lider --}}
            @if( isset($lider))
                @if($lider->imagenPerfil != '')
                    <img style="width: 100%; margin: 0; padding: 5px;"
                         src="<?php echo $message->embed(public_path().'/imagenPerfil/'.$lider->imagenPerfil); ?>"/>
                @endif
            @endif
        </div>
    </div>

    <p>Ante cualquier duda, enviar un correo electrónico a <a href="mailto:logistica@seiconsultores.cl">logistica@seiconsultores.cl</a>, o
        llamar al <a href="tel:(75 2) 747203">(75 2) 747203</a>.
    </p>

    <img src="<?php echo $message->embed(public_path()."/logo-sei-mail.png"); ?>"/>
</body>
</html>