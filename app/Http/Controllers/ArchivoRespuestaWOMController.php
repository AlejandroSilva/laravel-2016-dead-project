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
use Illuminate\Support\Facades\App;

class ArchivoRespuestaWOMController extends Controller {

    /**
     * ########################################################## Vistas
     */

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

    // GET archivo-respuesta-wom/{idArchivo}/pdf-preview
    function acta_vistaPreviaPDF($idArchivo){
        // validar que el archivo exista
        // todo: validar que el archivo ha sido "publicado"
        $archivoRespuesta = ArchivoRespuestaWOM::find($idArchivo);
        if(!$archivoRespuesta)
            return view('errors.errorConMensaje', [
                'titulo' => 'Nomina no encontrada',
                'descripcion' => 'La nomina que ha solicitado no ha sido encontrada. Verifique que el identificador sea el correcto y que el inventario no haya sido eliminado.'
            ]);
        // extraer un registro cualquiera para sacar algunos datos generales
        $registro = $archivoRespuesta->capturas->first();

        return view('pdfs.acta-auditoria-wom', [
            'liderWom' => $archivoRespuesta->liderWom,
            'runLiderWom' => $archivoRespuesta->runLiderWom,
            'liderSei' => $archivoRespuesta->liderSei,
            'runLiderSei' => $archivoRespuesta->runLiderSei,
            'fecha' => $registro->fechaCaptura,
            'organizacion' => $registro->codigoOrganizacion,
            'unidadesNuevo' => $archivoRespuesta->getUnidadesNuevo(),
            'unidadesEnUso' => $archivoRespuesta->getUnidadesEnUso(),
            'unidadesServicioTecnico' => $archivoRespuesta->getUnidadesServicioTecnico(),
            'unidadesTotal' => $archivoRespuesta->getUnidadesTotal(),
            'patentesTotal' => $archivoRespuesta->getPatentesTotal(),
            'primeraCaptura' => $archivoRespuesta->getPrimeraCaptura(),
            'ultimaCaptura' => $archivoRespuesta->getUltimaCaptura(),
        ]);
    }

    /**
     * ########################################################## Otros
     */

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
    function post_agregarArchivoConteo2(RespuestaWOMService $respuestaWOMService, Request $request, $idArchivo1){
        $user = Auth::user();

        // se adjunto un archivo?
        if (!$request->hasFile('file2'))
            return redirect()->route("indexAgregarRespuestaWOM")
                ->with('mensaje-error', "Debe adjuntar un archivo");

        // el archivo es valido?
        $archivo2 = $request->file('file2');
        if (!$archivo2->isValid())
            return redirect()->route("indexAgregarRespuestaWOM")
                ->with('mensaje-error', "El archivo no es válido");


        $res = $respuestaWOMService->agregarArchivoRespuestaWOM_conteo2($user, $idArchivo1, $archivo2);
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

    // GET archivo-respuesta-wom/{idArchivo}/descargar-excel
    function descargarExcel(RespuestaWOMService $respuestaWOMService, $idArchivo){
        $user = Auth::user();

        $res = $respuestaWOMService->generarExcel($user, $idArchivo);
        if(isset($res->error))
            //return response()->view('errors.403', [], 403);
            return response()->json($res->error, 400);
        else{
            return \ArchivosHelper::descargarArchivo($res->fullPath, $res->fileName);
        }
    }

    // GET archivo-respuesta-wom/{idArchivo}/descargar-txt
    function descargarTxt(RespuestaWOMService $respuestaWOMService, $idArchivo){
        $user = Auth::user();

        $res = $respuestaWOMService->generarTxt($user, $idArchivo);
        if(isset($res->error))
            //return response()->view('errors.403', [], 403);
            return response()->json($res->error, 400);
        else{
            return \ArchivosHelper::descargarArchivo($res->fullPath, $res->fileName);
        }
    }

    // GET archivo-respuesta-wom/{idArchivo}/descargar-pdf
    function descargarActaPdf($idArchivo) {
        // existe el archivo?
        $archivoRespuesta = ArchivoRespuestaWOM::find($idArchivo);
        if(!$archivoRespuesta)
            return view('errors.errorConMensaje', [
                'titulo' => 'Nomina no encontrada',
                'descripcion' => 'La nomina que ha solicitado no ha sido encontrada. Verifique que el identificador sea el correcto y que el inventario no haya sido eliminado.'
            ]);

        $nombreOriginal = $archivoRespuesta->nombreOriginal;
        $extensionIndex = strrpos($nombreOriginal, ".");
        $basename = substr($nombreOriginal, 0, $extensionIndex);

        if(App::environment('production')) {
            return \PDF::loadFile("http://sig.seiconsultores.cl/archivo-respuesta-wom/{$idArchivo}/preview-pdf")
                ->download($basename);
        }else{
            // stream, download
            return \PDF::loadFile("http://localhost/archivo-respuesta-wom/{$idArchivo}/preview-pdf")
                ->download($basename);  //->stream('nomina.pdf');
        }
    }
}
