<?php
namespace App\Http\Controllers;

use App\Providers\MuestraVencimientoFCVProvider;
use App\Services\MuestraVencimientoFCVService;
use Illuminate\Http\Request;
use Auth;
use File;
// Modelos
use App\ArchivoMuestraVencimientoFCV;

class MuestraVencimientoController extends Controller {

    // GET muestra-vencimiento-fcv
    function show_indexFCV(){
        // verificar permisos
        $user = Auth::user();
        if(!$user || !$user->can('fcv-verMuestras'))
            return response()->view('errors.403', [], 403);

        return response()->view('muestra-vencimiento.index-fcv', [
            'archivosMuestrasVencimiento' => ArchivoMuestraVencimientoFCV::all()->sortByDesc('created_at'),
            'puedeSubirArchivo' => $user->can('fcv-administrarMuestras')
        ]);
    }

    // POST muestra-vencimiento/subir-muestra-fcv
    function post_subirMuestraFCV(MuestraVencimientoFCVService $mvencimientoService, Request $request){
        $user = Auth::user();

        // verificar que venga un archivo en el POST
        if (!$request->hasFile('muestraVencimiento'))
            return redirect()->route("indexMuestraVencimientoFCV")
                ->with('mensaje-error', 'Debe adjuntar la muestra de vencimiento');

        // el archivo es valido?
        $archivoFormulario = $request->file('muestraVencimiento');
        if (!$archivoFormulario->isValid())
            return redirect()->route("indexMuestraVencimientoFCV")
                ->with('mensaje-error', 'El archivo adjuntado no es valido.');

        $res = $mvencimientoService->agregarArchivoMuestra($user, $archivoFormulario);
        return response()->json($res);

        // redireccionar a index
//        if(isset($res->error)){
//            if($res->codigo==401 || $res->codigo==403)
//                return response()->view('errors.403', [], 403);
//            else
//                return redirect()->route("indexMuestraVencimientoFCV")->with('mensaje-error', $res->mensaje);
//        }
//        else
//            return redirect()->route("indexMuestraVencimientoFCV")->with('mensaje-exito', "Productos cargados correctamente");
    }

    // GET muestra-vencimiento-fcv/{idMuestra}/descargar
    function descargar_muestraFCV($idArchivoMuestraVencimiento){
        // verificar permisos
        $user = Auth::user();
        if(!$user || !$user->can('fcv-verMuestras'))
            return response()->view('errors.403', [], 403);

        // existe el registro en la BD?
        $archivoMVencimiento = ArchivoMuestraVencimientoFCV::find($idArchivoMuestraVencimiento);
        if(!$archivoMVencimiento)
            return response()->view('errors.errorConMensaje', [
                'titulo' =>  'Archivo de vencimiento no encontrado',
                'descripcion' => 'No hay registros del archivo de vencimiento que busca. Contactese con el departamento de informática.',
            ]);

        $fullPath = $archivoMVencimiento->getFullPath();
        $nombreOriginal = $archivoMVencimiento->nombre_original;
        return \ArchivosHelper::descargarArchivo($fullPath, $nombreOriginal);
    }

}
