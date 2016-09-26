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
use App\ArchivoMaestraFCV;
use App\MaestraFCV;

class MaestraFCVController extends Controller
{
    function api_cargar_maestra(){
        
    }

    public function show_maestra_producto(){
        $maestraFCV = ArchivoMaestraFCV::all();
        return view('operacional.maestra.maestra-producto', ['maestras' => $maestraFCV]);
    }
    
    public function download_Maestra($idArchivoFinalInventario){
        $archivo = ArchivoFinalInventario::find($idArchivoFinalInventario);
        if(!$archivo)
            return view('errors.errorConMensaje', [
                'titulo' => 'Archivo No encontrado', 'descripcion' => 'El archivo que busca no se puede descargar.'
            ]);
        $download_archivo = $archivo->nombre_archivo;
        $file= public_path(). "/FSB/archivoFinalInventario/". "$download_archivo";
        $headers = array(
            'Content-Type: application/octet-stream',
        );
        return Response::download($file, $download_archivo, $headers);
    }
}
