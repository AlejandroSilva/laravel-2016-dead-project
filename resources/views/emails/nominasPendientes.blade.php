<html>
<head>
    <style>
        table, th, td {
            border: 1px solid black;
        }
    </style>
</head>
<body {{-- style="background: black; color: white"--}}>

    {{-- NOMINAS URGENTES: proximos 7 dias --}}
    <h1>Nominas Urgentes (próximos 2 días). <small>({{ $hoy }} al {{ $hoy_mas_2dias }})</small></h1>
    <p>Los inventarios a realizar en los próximos 2 días que <b>no se encuentran con nominas</b> son:</p>
    <table>
        <thead>
            <tr>
                <th>Fecha Programada</th>
                <th>Cliente</th>
                <th>CECO</th>
                <th>Tienda</th>
                <th>Dirección</th>
            </tr>
        </thead>
        <tbody>
            @if( count($inventarios_nominasUrgentes)>0 )
                @foreach($inventarios_nominasUrgentes as $inventario)
                    <tr>
                        <td>{{ $inventario['inventario_fechaProgramada'] }}</td>
                        <td>{{ $inventario['cliente_nombreCorto'] }}</td>
                        <td>{{ $inventario['local_numero'] }}</td>
                        <td>{{ $inventario['local_nombre'] }}</td>
                        <td>{{ $inventario['direccion'] }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5">Sin nominas pendientes</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- NOMINAS PENDIENTES: proximos 7 dias --}}
    <h1>Nominas Pendientes (próximos 7 días) <small>({{ $hoy }} al {{ $hoy_mas_7dias }})</small></h1>
    <p>Los inventarios a realizar en los próximos 7 días que <b>no se encuentran con nominas</b> son:</p>
    <table>
        <thead>
        <tr>
            <th>Fecha Programada</th>
            <th>Cliente</th>
            <th>CECO</th>
            <th>Tienda</th>
            <th>Dirección</th>
        </tr>
        </thead>
        <tbody>
        @if( count($inventarios_nominasPendientes)>0 )
            @foreach($inventarios_nominasPendientes as $inventario)
                <tr>
                    <td>{{ $inventario['inventario_fechaProgramada'] }}</td>
                    <td>{{ $inventario['cliente_nombreCorto'] }}</td>
                    <td>{{ $inventario['local_numero'] }}</td>
                    <td>{{ $inventario['local_nombre'] }}</td>
                    <td>{{ $inventario['direccion'] }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5">Sin nominas pendientes</td>
            </tr>
        @endif
        </tbody>
    </table>

    <img src="http://sig.seiconsultores.cl/logo-sei-mail.png">
</body>
</html>