<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
// Carbon
// DB
use DB;
use Response;
use Auth;
// Modelos
use App\ArchivoMaestraFCV;
use App\MaestraFCV;

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
                'titulo' => 'Error', 'descripcion' => 'Debe adjuntar el archivo excel.'
            ]);
        
        // el archivo es valido?
        $archivo = $request->file('file');
        if (!$archivo->isValid())
            return view('errors.errorConMensaje', [
                'titulo' => 'error', 'descripcion' => 'Archivo adjunto no es valido.'
            ]);

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
        $duplicados = MaestraFCV::skuDuplicados();
        if($duplicados->count()>0){
            return redirect()->route("maestraFCV");
        }
        $archivoMaestraFCV->setResultado("archivo cargado correctamente en la base de datos. ", true);  
        return redirect()->route("maestraFCV")
                         ->with('mensaje-exito',$archivoMaestraFCV->resultado);
    }
    
    public function show_maestra_producto(){
        $user = Auth::user();
        if(!$user || !$user->can('admin-maestra-fcv'))
            return response()->view('errors.403', [], 403);
        $archivosMaestraFCV = ArchivoMaestraFCV::all();
        $duplicados = MaestraFCV::skuDuplicados();
        return view('operacional.maestra.maestra-producto', ['archivosMaestraFCV' => $archivosMaestraFCV, 'duplicados' => $duplicados]);
    }
    
    public function download_Maestra($idArchivoMaestra){
        $user = Auth::user();
        if(!$user || !$user->can('admin-maestra-fcv'))
            return response()->view('errors.403', [], 403);
        $archivoMaestraFCV = ArchivoMaestraFCV::find($idArchivoMaestra);
        if(!$archivoMaestraFCV)
            return view('errors.errorConMensaje', [
                'titulo' => 'Archivo No encontrado', 'descripcion' => 'El archivo que busca no se puede descargar.'
            ]);
        $download_archivo = $archivoMaestraFCV->nombreArchivo;
        $file= public_path(). "/FCV/maestrasFCV/". "$download_archivo";
        $headers = array(
            'Content-Type: application/octet-stream',
        );
        return Response::download($file, $download_archivo, $headers);
    }
}
