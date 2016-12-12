<?php

namespace App\Http\Controllers;

use App\Auditorias;
use App\Services\MuestrasFCVService;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class MuestrasController extends Controller {

    // GET auditoria/muestras
    function show_indexBuscar(Request $request){
        $ceco = $request->ceco;
        $hoy = Carbon::now();

        $auditorias = (isset($ceco) && $ceco!=0)?
            Auditorias::buscar((object)[
                'ceco' => $ceco,
                'mes'  => $hoy,
            ])
            :
            Auditorias::buscar((object)[
                'idAuditor' => Auth::user()->id,
                'fechaInicio'  => $hoy->format('Y-m-d'),
                'fechaFin'  => $hoy->format('Y-m-d'),
            ]);
        return view('auditorias.muestras.index-buscar', [
            'auditorias' => $auditorias,
            'cecoBuscado' => $ceco
        ]);
    }

    // GET api/auditoria/{idAuditoria}/muestra-ird
    function descargarMuestraIrd($idAuditoria){
        $auditoria = Auditorias::find($idAuditoria);
        if(!$auditoria)
            return response()->view('errors.errorConMensaje', [
                'titulo' =>  'Auditoria no encontrada',
                'descripcion' => 'La auditoria que esta buscando no ha sido encontrada.',
            ]);

        if( $auditoria->getPathMuestraIrd()==null )
            return response()->view('errors.errorConMensaje', [
                'titulo' =>  'Muestra de IRD no disponible',
                'descripcion' => 'La muestra de IRD para este local no se encuentra disponible.',
            ]);

        return \ArchivosHelper::descargarArchivo($auditoria->getPathMuestraIrd(), $auditoria->nombreOriginalIrd);
    }

    // GET auditoria/{idAuditoria}/muestra-vencimiento
    function descargarMuestraVencimiento($idAuditoria){
        $auditoria = Auditorias::find($idAuditoria);
        if(!$auditoria)
            return response()->view('errors.errorConMensaje', [
                'titulo' =>  'Auditoria no encontrada',
                'descripcion' => 'La auditoria que esta buscando no ha sido encontrada.',
            ]);

        if( $auditoria->getPathMuestraVencimiento()==null )
            return response()->view('errors.errorConMensaje', [
                'titulo' =>  'Muestra de Vencimiento no disponible',
                'descripcion' => 'La muestra de Vencimiento para este local no se encuentra disponible.',
            ]);

        return \ArchivosHelper::descargarArchivo($auditoria->getPathMuestraVencimiento(), $auditoria->nombreOriginalVencimiento);
    }

    // POST auditoria/{idAuditoria}/muestra-ird
    function post_cargarMuestraIrd(MuestrasFCVService $muestrasFCVService, $idAuditoria, Request $request){
        // archivo adjuntado
        if (!$request->hasFile('muestraird'))
            return response()->json('Debe adjuntar el archivo zip.', 400);
        $archivo_formulario = $request->file('muestraird');
        if (!$archivo_formulario->isValid())
            return response()->json('El archivo adjuntado no es valido.', 400);

        $tmpPath = public_path().'/tmp/';
        $originalFilename = $archivo_formulario->getClientOriginalName();
        $filename = md5(uniqid(rand(), true)).'_'.$originalFilename;
        $archivo_formulario->move($tmpPath, $filename);

        $res = $muestrasFCVService->agregarArchivoIrd($idAuditoria, $tmpPath.$filename, $originalFilename);
        return response()->json( $res );
    }

    // POST auditoria/{idAuditoria}/muestra-vencimiento
    function post_cargarMuestraVencimiento(MuestrasFCVService $muestrasFCVService, $idAuditoria, Request $request){
        // archivo adjuntado
        if (!$request->hasFile('muestravencimiento'))
            return response()->json('Debe adjuntar el archivo zip.', 400);
        $archivo_formulario = $request->file('muestravencimiento');
        if (!$archivo_formulario->isValid())
            return response()->json('El archivo adjuntado no es valido.', 400);

        $tmpPath = public_path().'/tmp/';
        $originalFilename = $archivo_formulario->getClientOriginalName();
        $filename = md5(uniqid(rand(), true)).'_'.$originalFilename;
        $archivo_formulario->move($tmpPath, $filename);

        $res = $muestrasFCVService->agregarArchivoVencimiento($idAuditoria, $tmpPath.$filename, $originalFilename);
        return response()->json( $res );
    }
}
