<?php use Illuminate\Support\Facades\Input; ?>
@extends('operacional.layoutOperacional')
@section('title', 'Formato')
@section('content')
    <style>
        /* estilo para errores */
        .input-error{
            border-color: orangered;
            color: orangered;
        }
        /* Columna con el idCliente */
        .thIdLocal {
            font-size: 11px;
        }
        .tdIdLocal {
            font-size: 10px;
        }

        /* Columna de Cliente */
        .thIdCliente {
            font-size: 11px;
            text-align: center;
        }
        .tdIdCliente {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdIdCliente > select {
            width: 75px;
        }

        /* Columna con el formato local */
        .thFormato {
            font-size: 11px;
            text-align: center;
        }
        .tdFormato {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdFormato > select {
            width: 93px;
        }

        /* Columna con la jornada */
        .thJornada {
            font-size: 11px;
            text-align: center;
        }
        .tdJornada {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdJornada > select {
            width: 79px;
        }

        /* Columna con la numero */
        .thNumero {
            font-size: 11px;
        }
        .tdNumero {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;

        }
        .tdNumero > input{
            width: 28px;
        }

        /* Columna con la nombre */
        .thNombre {
            font-size: 11px;
            text-align: center;
        }
        .tdNombre {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdNombre > input{
            width: 90px;
        }

        /* Columna con la HoraApertura */
        .thHoraApertura {
            font-size: 11px;
            text-align: center;
        }
        .tdHoraApertura {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdHoraApertura > input {
            width: 60px;
        }

        /* Columna con la HoraCierre */
        .thHoraCierre {
            font-size: 11px;
            text-align: center;
        }
        .tdHoraCierre {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdHoraCierre > input {
            width: 55px;
        }

        /* Columna con la Email */
        .thEmail {
            font-size: 11px;
            text-align: center;
        }
        .tdEmail {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdEmail > input{
            width: 110px;
        }

        /* Columna con la CodArea1 */
        .thCodArea1 {
            font-size: 11px;
            text-align: center;
        }
        .tdCodArea1 {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdCodArea1 > input {
            width: 60px;
        }

        /* Columna con la CodArea2 */
        .thCodArea2 {
            font-size: 11px;
            text-align: center;
        }
        .tdCodArea2 {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdCodArea2 > input {
            width: 60px;
        }

        /* Columna con la telefono1 */
        .thTelefono1 {
            font-size: 11px;
        }
        .tdTelefono1 {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdTelefono1 > input {
            width: 65px;
        }

        /* Columna con la telefono2 */
        .thTelefono2 {
            font-size: 11px;
        }
        .tdTelefono2 {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdTelefono2 > input {
            width: 65px;
        }

        /* Columna con la stock */
        .thStock {
            font-size: 11px;
        }
        .tdStock {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdStock > input {
            width: 45px;
        }

        /* Columna con la fechaStocl */
        .thFechaStock {
            font-size: 11px;
            text-align: center;
        }
        .tdFechaStock {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdFechaStock > input {
            width: 60px;
        }

        /* Columna con la Comuna */
        .thComuna {
            font-size: 11px;
            text-align: center;
        }
        .tdComuna {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdComuna > select {
            width: 90px;
        }

        /* Columna con la Direccion */
        .thDireccion {
            font-size: 11px;
            text-align: center;
        }
        .tdDireccion {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdDireccion > input {
            width: 205px;
        }

        /* Columna direccion form agregar */

        .tdDireccion2 {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .tdDireccion2 > input {
            width: 240px;
        }

        /* Columna con la opcion*/
        .thOpcion {
            font-size: 11px;
            text-align: center;
        }
        .tdOpcion {
            font-size: 10px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

    </style>
    <script>
        function ShowSelected()
        {
            /* Para obtener el valor */
            var cod = document.getElementById("id").value;
            window.location.href = +cod;
        }
    </script>

    <div class="container-fluid">
        <div class="row">
            <div>
                <h1>Mantenedor de locales</h1>
                <h4 class="page-header">Buscar locales según cliente</h4>
            </div>
            <div class="col-md-2">
                <label className="control-label">Cliente</label>

                <tbody>
                    <td>
                        <select name="idCliente" id="id" onchange="ShowSelected();" class="form-control">
                            <option value="">Elija uno</option>
                            @foreach( $clientes as $cliente)

                                <option value="{{ $cliente->idCliente }}">
                                    {{ $cliente->nombreCorto}}{{"-"}}{{$cliente->nombre}}
                                </option>
                            @endforeach
                        </select>
                    </td>
                </tbody>
            </div>
        </div>
        <div class="row">
            <div>
                <h1 class="page-header">Locales</h1>
                <table class="table table-condensed table-bordered table-hover">
                    <thead>
                    <tr>
                        <th class="thIdLocal">ID</th>
                        <th class="thIdCliente">Cliente</th>
                        <th class="thFormato">Formato</th>
                        <th class="thJornada">Jornada</th>
                        <th class="thNumero">CE</th>
                        <th class="thNombre">Nombre</th>
                        <th class="thHoraApertura">Hora apertura</th>
                        <th class="thHoraCierre">Hora cierre</th>
                        <th class="thEmail">Email</th>
                        <th class="thCodArea1">Cód área1</th>
                        <th class="thTelefono1">Teléfono1</th>
                        <th class="thCodArea2">Cód área2</th>
                        <th class="thTelefono2">Teléfono2</th>
                        <th class="thStock">Stock</th>
                        <th class="thFechaStock">Fecha Stock</th>
                        <th class="thComuna">Comuna</th>
                        <th class="thDireccion">Dirección</th>
                        <th class="thOpcion">Opción</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($locales))
                        @foreach($locales as $local)
                            <form method="POST" action="/api/local/{{$local->idLocal}}/editar">
                                <input name="_method" type="hidden" value="PUT">
                                <tr>
                                    <td class="tdIdLocal">{{ $local->idLocal}}</td>
                                    <input type="hidden" value="{{ $local->idLocal}}" name="idLocal">
                                    <td class="tdIdCliente">
                                        <select name="idCliente" required {{ $user->can('programaLocales_modificar')? '' : 'disabled' }}>
                                            @foreach( $clientes as $cliente)
                                                <option value="{{ $cliente->idCliente }}"    {{ $local->idCliente==$cliente->idCliente? 'selected' :''  }}>
                                                    {{ $cliente->nombre}}
                                                </option>
                                            @endforeach
                                        </select>

                                    </td>
                                    <td class="tdFormato">
                                        <select name="idFormatoLocal" required {{ $user->can('programaLocales_modificar')? '' : 'disabled' }}>
                                            @foreach( $formatoLocales as $formatoLocal)
                                                <option value="{{ $formatoLocal->idFormatoLocal }}"    {{ $local->idFormatoLocal==$formatoLocal->idFormatoLocal? 'selected' :''  }}>
                                                    {{ $formatoLocal->nombre}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="tdJornada" >
                                        <select name="idJornadaSugerida" {{ $user->can('programaLocales_modificar')? '' : 'disabled' }}>
                                            @foreach( $jornadas as $jornada)
                                                <option value="{{ $jornada->idJornada }}"    {{ $local->idJornadaSugerida==$jornada->idJornada? 'selected' :''  }}>
                                                    {{ $jornada->nombre}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="tdNumero">
                                        <input type="text" value="{{ $local->numero}}" name="numero" required
                                               {{ $user->can('programaLocales_modificar')? '' : 'disabled' }}
                                               class="{{ $local->idLocal == Input::old('idLocal') && $errors->error->has('numero')? 'input-error' : '' }}"
                                        >
                                    </td>
                                    <td class="tdNombre">
                                        <input type="text" value="{{ $local->nombre }}" name="nombre" required maxlength="35"
                                               {{ $user->can('programaLocales_modificar')? '' : 'disabled' }}
                                               class="{{ $local->idLocal == Input::old('idLocal') && $errors->error->has('nombre')? 'input-error' : '' }}"
                                        >
                                    </td>
                                    <td class="tdHoraApertura">
                                        <input type="text" value="{{ $local->horaApertura}}" name="horaApertura"
                                                {{ $user->can('programaLocales_modificar')? '' : 'disabled' }}>
                                    </td>
                                    <td class="tdHoraCierre">
                                        <input type="text" value="{{ $local->horaCierre}}" name="horaCierre"
                                                {{ $user->can('programaLocales_modificar')? '' : 'disabled' }}>
                                    </td>
                                    <td class="tdEmail">
                                        <input type="email" value="{{ $local->emailContacto}}" name="emailContacto" maxlength="50"
                                                {{ $user->can('programaLocales_modificar')? '' : 'disabled' }}>
                                    </td>
                                    <td class="tdCodArea1">
                                        <input type="text" value="{{ $local->codArea1}}" name="codArea1"
                                                {{ $user->can('programaLocales_modificar')? '' : 'disabled' }} >
                                    </td>
                                    <td class="tdTelefono1">
                                        <input type="text" value="{{ $local->telefono1}}" name="telefono1"
                                                {{ $user->can('programaLocales_modificar')? '' : 'disabled' }}>
                                    </td>
                                    <td class="tdCodArea2">
                                        <input type="text" value="{{ $local->codArea2}}" name="codArea2"
                                                {{ $user->can('programaLocales_modificar')? '' : 'disabled' }}>
                                    </td>
                                    <td class="tdTelefono2">
                                        <input type="text" value="{{ $local->telefono2}}" name="telefono2"
                                                {{ $user->can('programaLocales_modificar')? '' : 'disabled' }}>
                                    </td>
                                    <td class="tdStock">
                                        <input type="text" value="{{ $local->stock}}" name="stock" required
                                                {{ $user->can('programaLocales_modificar')? '' : 'disabled' }}>
                                    </td>
                                    <td class="tdFechaStock">
                                        <input type="text" value="{{ $local->fechaStock}}" name="fechaStock"
                                               {{ $user->can('programaLocales_modificar')? '' : 'disabled' }} pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" title="Ejem: 2016-05-10" >
                                    </td>
                                    <td class="tdComuna">
                                        <select name="cutComuna" required {{ $user->can('programaLocales_modificar')? '' : 'disabled' }}>
                                            @foreach( $comunas as $comuna)
                                                <option value="{{ $comuna->cutComuna }}"    {{ $local->direccion->cutComuna == $comuna->cutComuna? 'selected' :''  }}>
                                                    {{ $comuna->nombre}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="tdDireccion">
                                        <input type="text" value="{{ $local->direccion->direccion }}" name="direccion" class="input" required maxlength="150"
                                                {{ $user->can('programaLocales_modificar')? '' : 'disabled' }}>
                                    </td>
                                    <td class="tdOpcion">
                                        <input type="submit" class="btn btn-primary btn-xs btn-block" value="Modificar"
                                                {{ $user->can('programaLocales_modificar')? '' : 'disabled' }}>
                                    </td>

                                </tr>
                            </form>
                        @endforeach
                    @endif
                    </tbody>
                </table>
                <div align="center">{!! $locales->links() !!}</div>

                @if (count($errors->error) > 0)
                    <div class="alert alert-danger">
                        <strong>Ha ocurrido un problema</strong>
                        <br><br>
                        <ul>
                            @if(isset($errors))
                                @foreach ($errors->error->all() as $error)
                                    <li>{{ $error}}</li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div>
                <h1>Agregar</h1>
                <table class="table table-condensed table-bordered table-hover">
                    <thead>
                    <tr>
                        <th class="thIdCliente">Cliente</th>
                        <th class="thFormato">Formato</th>
                        <th class="thJornada">Jornada</th>
                        <th class="thNumero">CE</th>
                        <th class="thNombre">Nombre</th>
                        <th class="thHoraApertura">Hora apertura</th>
                        <th class="thHoraCierre">Hora cierre</th>
                        <th class="thEmail">Email</th>
                        <th class="thCodArea1">Cód Area1</th>
                        <th class="thTelefono1">Teléfono1</th>
                        <th class="thCodArea2">Cód Area2</th>
                        <th class="thTelefono2">Teléfono2</th>
                        <th class="thStock">Stock</th>
                        <th class="thFechaStock">Fecha Stock</th>
                        <th class="thComuna">Comuna</th>
                        <th class="thDireccion">Dirección</th>
                        <th class="thOpcion">Opción</th>
                    </tr>
                    </thead>
                    <tbody>
                    <form method="POST" action="/api/local/nuevo">
                        <tr>
                            <td class="tdIdCliente">
                                <select name="idCliente" required {{ $user->can('programaLocales_agregar')? '' : 'disabled' }}>
                                    @foreach( $clientes as $cliente)
                                        <option value="{{ $cliente->idCliente }}" >
                                            {{ $cliente->nombre}}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="tdFormato">
                                <select name="idFormatoLocal" required {{ $user->can('programaLocales_agregar')? '' : 'disabled' }}>
                                    @foreach( $formatoLocales as $formatoLocal)
                                        <option value="{{ $formatoLocal->idFormatoLocal }}"    >
                                            {{ $formatoLocal->nombre}}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="tdJornada" >
                                <select name="idJornadaSugerida" required {{ $user->can('programaLocales_agregar')? '' : 'disabled' }}>
                                    @foreach( $jornadas as $jornada)
                                        <option value="{{ $jornada->idJornada }}"    >
                                            {{ $jornada->nombre}}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="tdNumero">
                                <input type="text" name="numero" required
                                       {{ $user->can('programaLocales_agregar')? '' : 'disabled' }}
                                       class="{{ (isset($errors) && $errors->has('numero'))? 'input-error' : '' }}"
                                >
                            </td>
                            <td class="tdNombre">
                                <input type="text" name="nombre" required maxlength="35"
                                       {{ $user->can('programaLocales_agregar')? '' : 'disabled' }}
                                       class="{{ (isset($errors) && $errors->has('nombre'))? 'input-error' : '' }}"

                                >
                            </td>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                            <td class="tdHoraApertura">
                                <input type="text" name="horaApertura"
                                        {{ $user->can('programaLocales_agregar')? '' : 'disabled' }}>
                            </td>
                            <td class="tdHoraCierre">
                                <input type="text" name="horaCierre"
                                        {{ $user->can('programaLocales_agregar')? '' : 'disabled' }}>
                            </td>
                            <td class="tdEmail">
                                <input type="text" name="emailContacto" maxlength="50"
                                        {{ $user->can('programaLocales_agregar')? '' : 'disabled' }}>
                            </td>
                            <td class="tdCodArea1">
                                <input type="text" name="codArea1"
                                        {{ $user->can('programaLocales_agregar')? '' : 'disabled' }}>
                            </td>
                            <td class="tdTelefono1">
                                <input type="text" name="telefono1"
                                        {{ $user->can('programaLocales_agregar')? '' : 'disabled' }}>
                            </td>
                            <td class="tdCodArea2">
                                <input type="text" name="codArea2"
                                        {{ $user->can('programaLocales_agregar')? '' : 'disabled' }}>
                            </td>
                            <td class="tdTelefono2">
                                <input type="text" name="telefono2"
                                        {{ $user->can('programaLocales_agregar')? '' : 'disabled' }}>
                            </td>
                            <td class="tdStock">
                                <input type="text" name="stock" required
                                        {{ $user->can('programaLocales_agregar')? '' : 'disabled' }} >
                            </td>
                            <td class="tdFechaStock">
                                <input type="text" name="fechaStock"
                                       {{ $user->can('programaLocales_agregar')? '' : 'disabled' }} pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" title="Ejem: 2016-05-10">
                            </td>
                            <td class="tdComuna">
                                <select name="cutComuna" required {{ $user->can('programaLocales_agregar')? '' : 'disabled' }}>
                                    @foreach( $comunas as $comuna)
                                        <option value="{{ $comuna->cutComuna }}"   >
                                            {{ $comuna->nombre}}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="tdDireccion2">
                                <input type="text" name="direccion" class="input" required maxlength="150"
                                        {{ $user->can('programaLocales_agregar')? '' : 'disabled' }}>
                            </td>
                            <td class="tdOpcion">
                                <input type="submit" class="btn btn-primary btn-xs btn-block" value="Agregar"
                                        {{ $user->can('programaLocales_agregar')? '' : 'disabled' }}>
                            </td>

                        </tr>
                    </form>
                    </tbody>
                </table>
                @if (count($errors) > 0)
                        <!-- Form Error List -->
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
