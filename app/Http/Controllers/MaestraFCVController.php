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
    function subir_maestra(Request $request)
    {
        // se adjunto un archivo?
        if (!$request->hasFile('file'))
            return view('errors.errorConMensaje', [
                'titulo' => 'error', 'descripcion' => 'Debe adjuntar el archivo.'
            ]);
            //return response()->json(['error' => 'Debe adjuntar el archivo.'], 400);

        // el archivo es valido?
        $archivo = $request->file('file');
        if (!$archivo->isValid())
            return view('errors.errorConMensaje', [
                'titulo' => 'error', 'descripcion' => 'Archivo adjunto no es valido.'
            ]);
            //return response()->json(['error' => 'El archivo adjuntado no es valido.'], 400);
        
        //Mover maestra 
        $maestraFinal = \ArchivoMaestraFCVHelper::moverAcarpeta($archivo);
        MaestraFCV::agregarArchivoMaestra(Auth::user(), $maestraFinal);
        \ArchivoMaestraFCVHelper::guardarRegistro($maestraFinal);
        return response()->json(['guardado'], 200);
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
