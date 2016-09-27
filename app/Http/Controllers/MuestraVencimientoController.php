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
        $archivos = ArchivoMuestraVencimientoFCV::all()
            ->sortByDesc('created_at');
        return response()->view('muestraVencimiento.index', [
            'archivos' => $archivos,
        ]);
    }

    // POST muestra-vencimiento-fcv/subir-muestra-fcv
    function api_subirMuestraFCV(Request $request){
        if (!$request->hasFile('muestraVencimiento'))
            return redirect()->route("indexMuestraVencimientoFCV")
                ->with('mensaje-error', 'Debe adjuntar la muestra de vencimiento');

        // el archivo es valido?
        $archivo = $request->file('muestraVencimiento');
        if (!$archivo->isValid())
            return redirect()->route("indexMuestraVencimientoFCV")
                ->with('mensaje-error', 'El archivo adjuntado no es valido.');

        // mover el archivo a la carpeta correspondiente
        $archivo = \ArchivosHelper::moverMuestraVencimientoFCV($archivo);
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
        // existe el registro en la BD?
        $archivoMVencimiento = ArchivoMuestraVencimientoFCV::find($idArchivoMuestraVencimiento);
        if(!$archivoMVencimiento)
            return response()->view('errors.errorConMensaje', [
                'titulo' =>  'Archivo no encontrado',
                'descripcion' => 'No hay registros del archivo que busca. Contactese con el departamento de informática.',
            ]);

        $fullPath = $archivoMVencimiento->getFullPath();
        $nombreOriginal = $archivoMVencimiento->nombre_original;
        // existe el archivo fisicamente en el servidor?
        if(!File::exists($fullPath))
            return response()->view('errors.errorConMensaje', [
                'titulo' =>  'Archivo no encontrado',
                'descripcion' => 'El archivo que busca no ha sido encontrado. Contactese con el departamento de informática.',
            ]);

        return response()
        ->download($fullPath, $nombreOriginal, [
            'Content-Type'=>'application/force-download',   // forzar la descarga en Opera Mini
            'Pragma'=>'no-cache',
            'Cache-Control'=>'no-cache, must-revalidate'
        ]);
    }

}
