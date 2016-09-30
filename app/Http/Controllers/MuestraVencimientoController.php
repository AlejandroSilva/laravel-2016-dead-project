<?php
namespace App\Http\Controllers;

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
        if(!$user || !$user->can('admin-maestra-fcv'))
            return response()->view('errors.403', [], 403);

        $archivos = ArchivoMuestraVencimientoFCV::all()
            ->sortByDesc('created_at');
        return response()->view('muestraVencimiento.index', [
            'archivos' => $archivos,
        ]);
    }

    // POST muestra-vencimiento-fcv/subir-muestra-fcv
    function api_subirMuestraFCV(Request $request){
        // verificar permisos
        $user = Auth::user();
        if(!$user || !$user->can('admin-maestra-fcv'))
            return response()->view('errors.403', [], 403);

        if (!$request->hasFile('muestraVencimiento'))
            return redirect()->route("indexMuestraVencimientoFCV")
                ->with('mensaje-error', 'Debe adjuntar la muestra de vencimiento');

        // el archivo es valido?
        $archivo_formulario = $request->file('muestraVencimiento');
        if (!$archivo_formulario->isValid())
            return redirect()->route("indexMuestraVencimientoFCV")
                ->with('mensaje-error', 'El archivo adjuntado no es valido.');

        // mover el archivo a la carpeta correspondiente
        $archivo = \ArchivosHelper::moverMuestraVencimientoFCV($archivo_formulario);
        // se guarda en la BD en registro del archivo enviado
        $archivoMuestraVencimiento = ArchivoMuestraVencimientoFCV::agregarArchivo(Auth::user(), $archivo);

        // 1) leer los datos del xlsx
        $resultadoExcel = \ExcelHelper::leerExcel($archivoMuestraVencimiento->getFullPath());
        if( $resultadoExcel->error!=null ){
            $archivoMuestraVencimiento->setResultado($resultadoExcel->error, false);
            return redirect()->route("indexMuestraVencimientoFCV")
                ->with('mensaje-error', $resultadoExcel->error);
        }

        // 2) parsear los datos al formato correcto
        $resultadoParseo = \MuestraVencimiento::parsearArrayMuestraFCV($resultadoExcel->datos, $archivoMuestraVencimiento->idArchivoMuestraVencimientoFCV);
        if( $resultadoParseo->error!=null ){
            $archivoMuestraVencimiento->setResultado($resultadoParseo->error, false);
            return redirect()->route("indexMuestraVencimientoFCV")
                ->with('mensaje-error', $resultadoParseo->error);
        }

        // insertar los datos en la BD
        $archivoMuestraVencimiento->agregarDatos($resultadoParseo->datos);

        // todo: validar: buscar duplicados o algun otro error

        // se agrego correctamente el archivo a la BD
        $archivoMuestraVencimiento->setResultado("Archivo cargado correctamente a la DB.", true);

        //return response()->json($archivoMuestraVencimiento);
        return redirect()->route("indexMuestraVencimientoFCV")
            ->with('mensaje-exito', $archivoMuestraVencimiento->resultado);
    }

    // GET muestra-vencimiento-fcv/{idMuestra}/descargar
    function descargar_muestraFCV($idArchivoMuestraVencimiento){
        // verificar permisos
        $user = Auth::user();
        if(!$user || !$user->can('admin-maestra-fcv'))
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
