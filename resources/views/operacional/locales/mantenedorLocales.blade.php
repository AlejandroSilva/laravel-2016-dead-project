{{-- locales.listado --}}
@extends('operacional.layoutOperacional')

@section('title', 'Listado de Clientes')

@section('content')
    <script>
        function ShowSelected()
        {
            /* Para obtener el valor */
            var cod = document.getElementById("id").value;

            console.log(cod);
            window.location.href = '/admin/cliente/'+cod;
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
                    <select name="idCliente" id="id" onchange="ShowSelected(this);" class="form-control">
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
    </div>