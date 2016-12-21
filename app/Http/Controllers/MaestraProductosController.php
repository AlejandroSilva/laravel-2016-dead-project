<?php

namespace App\Http\Controllers;
use App\Services\MaestraWOMService;
use Illuminate\Http\Request;
use App\Http\Requests;
// Utils
use DB;
use Response;
use Auth;
// Modelos
use App\ArchivoMaestraProductos;
// Servicios
use App\Services\MaestraFCVService;

class MaestraProductosController extends Controller {
    /**
     * MAESTRA WOM
     */

    // GET maestra-wom
    function show_maestraWOM(){
        $user = Auth::user();
        if(!$user || !$user->can('wom-verMaestra'))
            return response()->view('errors.403', [], 403);

        $maestrasDeProductos = ArchivoMaestraProductos::buscar((object)[
            'idCliente' => 9, // WOM
            'orden' => 'desc'
        ]);
        return view('maestra-productos.index-wom', [
            'archivosMaestraProductos' => $maestrasDeProductos,
            'puedeSubirArchivo' => $user->can('wom-administrarMaestra')
        ]);
    }

    // POST maestra-productos/subir-maestra-wom
    function formpost_subirMaestraWOM(MaestraWOMService $maestraWOMService, Request $request){
        $user = Auth::user();

        // se adjunto un archivo?
        if (!$request->hasFile('file'))
            return redirect()->route("maestraWOM")
                ->with('mensaje-error', "Debe adjuntar la maestra");

        // el archivo es valido?
        $archivo = $request->file('file');
        if (!$archivo->isValid())
            return redirect()->route("maestraWOM")
                ->with('mensaje-error', "El archivo adjunto no es válido");


        $res = $maestraWOMService->agregarMaestraWOM($user, $archivo);

        // redireccionar a index
        if(isset($res->error)){
            if($res->codigo==401 || $res->codigo==403)
                return response()->view('errors.403', [], 403);
            else
                return redirect()->route("maestraWOM")->with('mensaje-error', $res->mensaje);
        }
        else
            return redirect()->route("maestraWOM")->with('mensaje-exito', "Productos cargados correctamente");
    }

    // GET maestra-wom/{idArchivo}/descargar-original
    function descargarOriginal_maestraWOM($idArchivo){
        // el metodo es el mismo para otros clientes, pero solo cambian los permisos
        $user = Auth::user();
        if(!$user || !$user->can('wom-verMaestra'))
            return response()->view('errors.403', [], 403);

        $archivoMaestra = ArchivoMaestraProductos::find($idArchivo);
        // solo la puede descargar en caso que efectivamente sea una maestra de WOM
        if($archivoMaestra->idCliente!=9)
            return response()->view('errors.403', [], 403);

        return \ArchivosHelper::descargarArchivo($archivoMaestra->getFullPath(), $archivoMaestra->nombreOriginal);
    }
}
