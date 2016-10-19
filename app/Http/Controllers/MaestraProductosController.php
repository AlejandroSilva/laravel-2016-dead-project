<?php

namespace App\Http\Controllers;
use App\Clientes;
use Illuminate\Http\Request;
use App\Http\Requests;
// DB
use DB;
use Response;
use Auth;
// Modelos
use App\ArchivoMaestraProductos;

class MaestraProductosController extends Controller {

    // GET maestra-productos-fcv
    public function show_maestra_productos_fcv(){
        $user = Auth::user();
        if(!$user || !$user->can('admin-maestra-fcv'))
            return response()->view('errors.403', [], 403);

        $maestrasDeProductos = ArchivoMaestraProductos::buscar((object)[
            'idCliente' => 2, // FCV
            'orden' => 'desc'
        ]);
        //$productosDuplicados = ProductosFCV::skuDuplicados();
        return view('maestra-productos-fcv.index', [
            'archivosMaestraProductos' => $maestrasDeProductos
        ]);
    }


    // POST maestra-productos/subir-maestra-fcv
    //
    function subir_maestraFCV(Request $request){
        // ver que el usuario tenga los permisos correspondientes
        $user = Auth::user();
        if(!$user || !$user->can('admin-maestra-fcv'))
            return response()->view('errors.403', [], 403);
        
        // se adjunto un archivo?
        if (!$request->hasFile('file'))
            return redirect()->route("maestraFCV")
                ->with('mensaje-error', "Debe adjuntar la maestra");
        
        // el archivo es valido?
        $archivo = $request->file('file');
        if (!$archivo->isValid())
            return redirect()->route("maestraFCV")
                ->with('mensaje-error', "El archivo adjunto no es válido");

        // mover el archivo a la carpeta correspondiente, y crear un registro en la BD
        $clienteFCV = Clientes::find(2);
        $archivoMaestraFCV = $clienteFCV->agregarArchivoMaestra(Auth::user(), $archivo);

        // procesar archivo
        $resultadoProcesar = $archivoMaestraFCV->procesarArchivo();

        // mostrar index
        if(isset($resultadoProcesar->mensajeError))
            return redirect()->route("maestraFCV")->with('mensaje-error', $resultadoProcesar->mensajeError);
        else
            return redirect()->route("maestraFCV")->with('mensaje-exito', $resultadoProcesar->mensajeExito);
    }


    // GET maestra-productos/{idArchivoMaestra}/descargar
    //
    public function descargar_archivo($idArchivoMaestra){
        $user = Auth::user();
        // todo mejorar los permisos dependiendo del usuario
        if(!$user || !$user->can('admin-maestra-fcv'))
            return response()->view('errors.403', [], 403);

        $archivoMaestra = ArchivoMaestraProductos::find($idArchivoMaestra);
        return \ArchivosHelper::descargarArchivo($archivoMaestra->getFullPath(), $archivoMaestra->nombreArchivo);
    }
}
