<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Jobs\InformarArchivoRespuestaWOM;
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
    function show_index(Request $request){
        $user = Auth::user();
        if(!$user || !$user->can('wom-verArchivosRespuesta'))
            return response()->view('errors.403', [], 403);

        $query = ArchivoRespuestaWOM::with([]);
        if(isset($request->ceco) && $request->ceco!=""){
            $query->where('organizacion', $request->ceco);
        }
        $archivosRespuestaWOM = $query->get()->sortByDesc('created_at');

        $totalAuditoriasConNota = $archivosRespuestaWOM->filter(function($a){return isset($a->evaluacionAServicioSEI);})->count();
        return view('auditorias.archivo-respuesta-wom.index', [
            'puedeAdministrar'  => $user->can('wom-administrarArchivosRespuesta'),
            'archivosRespuesta' => $archivosRespuestaWOM,
            'puedeSubirArchivo' => $user->can('wom-subirArchivosRespusta'),
            'cecoBuscado'       => $request->ceco,
            // totales
            'totalNuevo'        => number_format($archivosRespuestaWOM->sum('unidadesNuevo'), 0, ',', '.'),
            'totalUsado'        => number_format($archivosRespuestaWOM->sum('unidadesUsado'), 0, ',', '.'),
            'totalPrestamo'     => number_format($archivosRespuestaWOM->sum('unidadesPrestamo'), 0, ',', '.'),
            'totalServTecnico'  => number_format($archivosRespuestaWOM->sum('unidadesServTecnico'), 0, ',', '.'),
            'totalUnidades'     => number_format($archivosRespuestaWOM->sum('unidadesContadas'), 0, ',', '.'),
            'totalPatentes'     => number_format($archivosRespuestaWOM->sum('pttTotal'), 0, ',', '.'),
            'totalAuditorias'   => number_format($archivosRespuestaWOM->count(), 0, ',', '.'),

            'promedioNotas'     => $totalAuditoriasConNota>0?
                number_format($archivosRespuestaWOM->sum('evaluacionAServicioSEI')/$totalAuditoriasConNota, 1, ',', '.') : ''
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
        $archivoRespuesta = ArchivoRespuestaWOM::find($idArchivo);

        // buscar firma
        $pathFirmaVacia = '/WOM/sin-firma-wom.jpg';
        $zipPath = $archivoRespuesta->getFullPath();
        $archivoFirma = \ArchivosHelper::extraerArchivo($zipPath, 'My Documents/Firma.jpg');
        if( isset($archivoFirma->error) )
            $archivoFirma = \ArchivosHelper::extraerArchivo($zipPath, "My Documents/Firma_$archivoRespuesta->organizacion.jpg");

        $pathFirmaWom = isset($archivoFirma->error) ?
             $pathFirmaVacia : str_replace( public_path(), '', $archivoFirma->fullpath );

        if(!$archivoRespuesta)
            return view('errors.errorConMensaje', [
                'titulo' => 'Nomina no encontrada',
                'descripcion' => 'La nomina que ha solicitado no ha sido encontrada. Verifique que el identificador sea el correcto y que el inventario no haya sido eliminado.'
            ]);

        return view('pdfs.acta-auditoria-wom', [
            'archivo' => $archivoRespuesta,
            'firmaWom' => $pathFirmaWom,
            'firmaSei' => $pathFirmaVacia
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


        $res = $respuestaWOMService->agregarZipRespuestaWOM($user, $archivo);

        if(isset($res->error)){
            // si el archivo no se puedo agregar por algun error, eliminarlo
            $res->archivoRespuesta->eliminar();

            return redirect()->route("indexAgregarRespuestaWOM")
                ->with('mensaje-error', $res->error);
        }else{
            dispatch(new InformarArchivoRespuestaWOM($res->archivoRespuesta));
            return redirect()->route("indexAgregarRespuestaWOM")
                ->with('mensaje-exito', "Archivo cargado correctamente");
        }
    }

    // GET archivo-respuesta-wom/{idArchivo}/descargar-original
    function descargarZip($idArchivo){
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

        $res = $respuestaWOMService->generarTxtConteoFinal($user, $idArchivo);
        if(isset($res->error))
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

    // GET archivo-respuesta-wom/descargar-consolidado
    function descargarConsolidado(RespuestaWOMService $respuestaWOMService){
        $res = $respuestaWOMService->descargarConsolidado(null);
        if(isset($res->error))
            return response()->json($res->error, 400);
        else{
            return \ArchivosHelper::descargarArchivo($res->xlsxPath, 'consolidado WOM.xlsx');
        }
    }
}
