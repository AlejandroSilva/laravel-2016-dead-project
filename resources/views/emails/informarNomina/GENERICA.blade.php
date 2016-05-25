<html>
<head></head>
<body>
    <p>SEI Consultores, informa nomina de personal para IG <b>{{ $local->nombre }}</b> se encuentra disponible.</p>

    {{-- Datos Inventario --}}
    <table style="margin-top: 16px; margin-bottom: 16px; width: 600px;">
        <tbody>
        <tr>
            <td style="border: 1px solid #ddd;">Cliente</td>
            <td style="border: 1px solid #ddd;"><b>{{ $inventario->local->cliente->nombreCorto }}</b></td>
            <td style="border: 1px solid #ddd;">Dotación asignada</td>
            <td style="border: 1px solid #ddd;">{{ $nomina->dotacionAsignada }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd;">Local</td>
            <td style="border: 1px solid #ddd;"><b>({{$inventario->local->numero}}) {{$inventario->local->nombre}}</b></td>
            <td style="border: 1px solid #ddd;"></td>
            <td style="border: 1px solid #ddd;"></td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd;">Fecha programada</td>
            <td style="border: 1px solid #ddd;"><b>{{$inventario->fechaProgramadaF()}}</b></td>
            <td style="border: 1px solid #ddd;"></td>
            <td style="border: 1px solid #ddd;"></td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd;">Hr. llegada lider</td>
            <td style="border: 1px solid #ddd;">{{$nomina->horaPresentacionLiderF()}}</td>
            <td style="border: 1px solid #ddd;"></td>
            <td style="border: 1px solid #ddd;"></td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd;">Hr. llegada equipo</td>
            <td style="border: 1px solid #ddd;">{{$nomina->horaPresentacionEquipoF()}}</td>
            <td style="border: 1px solid #ddd;"></td>
            <td style="border: 1px solid #ddd;"></td>
        </tr>
        </tbody>
    </table>

    {{-- Datos Local --}}
    <table style="width: 600px">
        <tbody>
            <tr>
                <td style="border: 1px solid #ddd;">Dirección</td>
                <td style="border: 1px solid #ddd;">{{ $inventario->local->direccion->direccion }}</td>
                <td style="border: 1px solid #ddd;">Hr.Apertura</td>
                <td style="border: 1px solid #ddd;">{{ $inventario->local->horaAperturaF() }}</td></tr>
            <tr>
                <td style="border: 1px solid #ddd;">Comuna</td>
                <td style="border: 1px solid #ddd;">{{ $inventario->local->direccion->comuna->nombre }}</td>
                <td style="border: 1px solid #ddd;">Hr.Cierre</td>
                <td style="border: 1px solid #ddd;">{{ $inventario->local->horaCierreF() }}</td></tr>
            <tr>
                <td style="border: 1px solid #ddd;">Región</td>
                <td style="border: 1px solid #ddd;">{{ $inventario->local->direccion->comuna->provincia->region->numero }}</td>
                <td style="border: 1px solid #ddd;">Teléfono 1</td>
                <td style="border: 1px solid #ddd;">{{ $inventario->local->codArea1." ".$inventario->local->telefono1 }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;">Formato Local</td>
                <td style="border: 1px solid #ddd;">{{ $inventario->local->formatoLocal->nombre  }}</td>
                <td style="border: 1px solid #ddd;">Teléfono 2</td>
                <td style="border: 1px solid #ddd;">{{ $inventario->local->codArea2." ".$inventario->local->telefono2 }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;"></td>
                <td style="border: 1px solid #ddd;"></td>
                <td style="border: 1px solid #ddd;">Correo</td>
                <td style="border: 1px solid #ddd;">{{ $inventario->local->emailContacto }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Tabla con la dotacion: IZQ dotacion, DER, foto Lider --}}
    <table style="margin-top: 16px; margin-bottom: 32px;">
        <tbody>
            <tr>
                <td>
                    <table style="max-width: 400px; vertical-align: middle;
                                margin-left: 32px; margin-right: 32px;
                                border: 1px solid #ddd">
                        <thead>
                            <tr>
                                <th style="border: 1px solid #ddd; padding: 8px;">N°</th>
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
                </td>
                <td style="max-width: 180px;">
                    {{-- Imagen del Lider --}}
                    @if( isset($lider))
                        @if($lider->imagenPerfil != '')
                            <img style="width: 80%; max-width: 130px; margin: 0 auto; padding: 5px;"
                                 src="<?php echo $message->embed(public_path().'/imagenPerfil/'.$lider->imagenPerfil); ?>"/>
                        @endif
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <p>Ante cualquier duda, enviar un correo electrónico a <a href="mailto:logistica@seiconsultores.cl">logistica@seiconsultores.cl</a>, o
        llamar al <a href="tel:(75 2) 747203">(75 2) 747203</a>.
    </p>

    <img src="<?php echo $message->embed(public_path()."/logo-sei-mail.png"); ?>"/>
</body>
</html>