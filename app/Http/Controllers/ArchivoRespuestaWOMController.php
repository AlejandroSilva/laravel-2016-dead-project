<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
// Utils
use Auth;
// Services
use App\Services\RespuestaWOMService;
// Models
use App\ArchivoRespuestaWOM;

class ArchivoRespuestaWOMController extends Controller {

    // GET archivos-respuesta-wom
    function show_index(){
        $user = Auth::user();
        if(!$user || !$user->can('wom-verArchivosRespuesta'))
            return response()->view('errors.403', [], 403);

        $archivosRespuestaWOM = ArchivoRespuestaWOM::all();
        return view('auditorias.archivo-respuesta-wom.index', [
            'archivosRespuesta' => $archivosRespuestaWOM,
            'puedeSubirArchivo' => $user->can('wom-subirArchivosRespusta')
        ]);
    }

    // GET agregar-archivos-respuesta-wom
    function show_agregarArchivo(){
        $user = Auth::user();
        if(!$user || !$user->can('wom-verArchivosRespuesta'))
            return response()->view('errors.403', [], 403);

        return view('auditorias.archivo-respuesta-wom.agregar-respuesta', [
            'puedeSubirArchivo' => $user->can('wom-subirArchivosRespusta')
        ]);
    }

    // POST agregar-archivos-respuesta-wom
    function post_agregarArchivo(RespuestaWOMService $respuestaWOMService, Request $request){
        $user = Auth::user();

        // se adjunto un archivo?
        if (!$request->hasFile('file'))
            return redirect()->route("indexAgregarRespuestaWOM")
                ->with('mensaje-error', "Debe adjuntar un archivo");

        // el archivo es valido?
        $archivo = $request->file('file');
        if (!$archivo->isValid())
            return redirect()->route("indexAgregarRespuestaWOM")
                ->with('mensaje-error', "El archivo no es válido");


        $res = $respuestaWOMService->agregarArchivoRespuestaWOM($user, $archivo);
        if(isset($res->error)){
            return redirect()->route("indexAgregarRespuestaWOM")
                ->with('mensaje-error', $res->error);
        }else
            return redirect()->route("indexAgregarRespuestaWOM")
                ->with('mensaje-exito', "Archivo cargado correctamente");
    }

    // GET archivo-respuesta-wom/{idArchivo}/descargar-original
    function descargarOriginal($idArchivo){
        $user = Auth::user();
        if(!$user || !$user->can('wom-verArchivosRespuesta'))
            return response()->view('errors.403', [], 403);

        $archivoRespuesta = ArchivoRespuestaWOM::find($idArchivo);
        return \ArchivosHelper::descargarArchivo($archivoRespuesta->getFullPath(), $archivoRespuesta->nombreOriginal);
    }
}
