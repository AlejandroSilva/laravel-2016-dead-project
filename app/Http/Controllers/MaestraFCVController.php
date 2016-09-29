<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
// Carbon
use Carbon\Carbon;
// DB
use DB;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
// Modelos
use App\Clientes;
use Response;
use App\ArchivoMaestraFCV;
use App\MaestraFCV;
use Auth;

class MaestraFCVController extends Controller
{
    function subir_maestra(Request $request){
        // ver que el usuario tenga los permisos correspondientes
        $user = Auth::user();
        if(!$user || !$user->can('admin-maestra-fcv'))
            return response()->view('errors.403', [], 403);
        
        // se adjunto un archivo?
        if (!$request->hasFile('file'))
            return view('errors.errorConMensaje', [
                'titulo' => 'error', 'descripcion' => 'Debe adjuntar el archivo excel.'
            ]);
            //return response()->json(['error' => 'Debe adjuntar el archivo.'], 400);

        // el archivo es valido?
        $archivo = $request->file('file');
        if (!$archivo->isValid())
            return view('errors.errorConMensaje', [
                'titulo' => 'error', 'descripcion' => 'Archivo adjunto no es valido.'
            ]);
            //return response()->json(['error' => 'El archivo adjuntado no es valido.'], 400);
        
        //Mover maestra a una carpeta en el servidor
        $moverArchivo=\ArchivoMaestraFCVHelper::moverAcarpeta($archivo);
        //Guardar archivo en la DB
        $archivoMaestraFCV = ArchivoMaestraFCV::agregarArchivoMaestra(Auth::user(), $moverArchivo);
        //Lee excel de maestra y lo asigna a un arreglo siempre y cuando no se origine un error
        $resultadoExcel = \ExcelHelper::leerExcel($archivoMaestraFCV->getFullPath());
        //Cuando no puede leer el excel retorna un error
        if($resultadoExcel->error!=null){
            $archivoMaestraFCV->setResultado($resultadoExcel->error, false);
            return view('errors.errorConMensaje',[
                'titulo' => 'error', 'descripcion' => $resultadoExcel->error
            ]);
        }
        //Parsear los datos del archivo
        $parseo = \ArchivoMaestraFCVHelper::parseo($resultadoExcel->datos, $archivoMaestraFCV->idArchivoMaestra);
        if($parseo->error!=null){
            $archivoMaestraFCV->setResultado($parseo->error, false);
            return redirect()->route("maestraFCV")
                ->with('mensaje-error', $parseo->error);
        }
        //insertando datos parseados en la BD
        $archivoMaestraFCV->guardarRegistro($parseo->datos);
        $archivoMaestraFCV->setResultado("archivo cargado correctamente en la base de datos", true);

        return view('success.successConMensaje',[
            'titulo' => 'Cargado correctamente', 'descripcion' => $archivoMaestraFCV->resultado
        ]);
    }
    
    public function show_maestra_producto(){
        $maestraFCV = ArchivoMaestraFCV::all();
        return view('operacional.maestra.maestra-producto', ['maestras' => $maestraFCV]);
    }
    
    public function download_Maestra($idArchivoMaestra){
        $maestra = ArchivoMaestraFCV::find($idArchivoMaestra);
        if(!$maestra)
            return view('errors.errorConMensaje', [
                'titulo' => 'Archivo No encontrado', 'descripcion' => 'El archivo que busca no se puede descargar.'
            ]);
        $download_archivo = $maestra->nombreArchivo;
        $file= public_path(). "/FCV/maestrasFCV/". "$download_archivo";
        $headers = array(
            'Content-Type: application/octet-stream',
        );
        return Response::download($file, $download_archivo, $headers);
    }
}
