@extends('layouts.root')
@section('title', 'Muestras de auditoria')

@section('body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-10 col-sm-offset-1">
                <h2>Descargar muestras de auditoría</h2>

                <form action="/auditoria/muestras" method="GET" class="form-horizontal">
                    {{--<input type="hidden" value="{{ csrf_token() }}" name="_token">--}}

                    {{-- Archivo --}}
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 control-label">Local</label>
                        <div class="col-xs-12 col-sm-7">
                            <input class="form-control" type="text" name="ceco"
                                   value={{ isset($cecoBuscado)? $cecoBuscado : '' }}
                            >
                        </div>
                        <div class="col-xs-12 col-sm-3">
                            <input type="submit" class="btn btn-primary btn-sm btn-block" value="Buscar" />
                        </div>
                    </div>
                </form>

                <table class="table">
                    <thead>
                    <th>Local</th>
                    <th>Fecha programada</th>
                    <th class="hidden-xs">Auditor</th>
                    <th>M.IRD</th>
                    <th>M.Vencimiento</th>
                    </thead>
                    <tbody>
                    @if(count($auditorias)>0)
                        @for($i=0; $i<count($auditorias); $i++)
                            @php( $aud = $auditorias[$i] )
                            <tr>
                                <td><p title="{{$aud->idAuditoria}}">{{ $aud->local->numero }}</p></td>
                                <td>{{ $aud->fechaProgramada }}</td>
                                <td class="hidden-xs">{{ $aud->auditor->nombreCorto() }}</td>
                                <td>
                                    @if($aud->getPathMuestraIrd()!=null)
                                        <a href="/api/auditoria/{{$aud->idAuditoria}}/muestra-ird" class="btn btn-xs btn-primary">descargar</a>
                                    @else
                                        <button class="btn btn-xs btn-default">no disponible</button>
                                    @endif
                                </td>
                                <td>
                                    @if($aud->getPathMuestraVencimiento()!=null)
                                        <a href="/api/auditoria/{{$aud->idAuditoria}}/muestra-vencimiento" class="btn btn-xs btn-primary">descargar</a>
                                    @else
                                        <button class="btn btn-xs btn-default">no disponible</button>
                                    @endif
                                </td>
                            </tr>
                        @endfor
                    @else
                        <tr>
                            <td>No se encontraron datos</td>
                        </tr>
                    @endif
                    </tbody>
                </table>

            </div>
        </div>
    </div>
@stop