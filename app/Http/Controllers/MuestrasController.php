<?php

namespace App\Http\Controllers;

use App\Auditorias;
use App\Services\MuestrasFCVService;
use Illuminate\Http\Request;

use App\Http\Requests;

class MuestrasController extends Controller {
    // GET auditoria/{idAuditoria}/muestras
    function show_index(){
        return response()->json("vista: muestras de la auditoria");
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
