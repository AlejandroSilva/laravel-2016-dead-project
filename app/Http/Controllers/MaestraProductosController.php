<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
// DB
use DB;
use Response;
use Auth;
// Modelos
use App\ArchivoMaestraProductos;
// Servicios
use App\Services\MaestraFCVService;

class MaestraProductosController extends Controller {

    // GET maestra-fcv
    function show_maestraFCV(){
        $user = Auth::user();
        if(!$user || !$user->can('fcv-verMaestra'))
            return response()->view('errors.403', [], 403);

        $maestrasDeProductos = ArchivoMaestraProductos::buscar((object)[
            'idCliente' => 2, // FCV
            'orden' => 'desc'
        ]);
        return view('maestra-productos.index-fcv', [
            'archivosMaestraProductos' => $maestrasDeProductos,
            'puedeSubirArchivo' => $user->can('fcv-administrarMaestra')
        ]);
    }

    // GET maestra-fcv/{idMaestra}/ver-estado
    function show_verEstadoMaestraFCV($idMaestra){
        // validar usuario
        $user = Auth::user();
        if(!$user)
            return response()->view('errors.403', [], 403);

        // existe la maestra?
        $archivoMaestra = ArchivoMaestraProductos::find($idMaestra);
        if(!$archivoMaestra)
            return response()->view('errors.errorConMensaje', [
                'titulo' =>  'Maestra de productos no encontrada',
                'descripcion' => 'La maestra de productos que busca no ha sido encontrada. Contactese con el departamento de informática.',
        ]);

        return view('maestra-productos.ver-estado', [
            'archivoMaestra' => $archivoMaestra
        ]);
    }

    // POST maestra-fcv/{idMaestra}/actualizar-estado
    function formpost_actualizarEstadoFCV(MaestraFCVService $maestraFCVService, $idMaestra){
        $user = Auth::user();
        $archivoMaestra = ArchivoMaestraProductos::find($idMaestra);

        $maestraFCVService->validarProductosFCV($user, $archivoMaestra);
        return redirect()->route("verEstadoMaestraFCV", [
            'idMaestra' => $idMaestra
        ]);
    }

    // GET maestra-fcv/{idMaestra}/actualizar-maestra
    function show_actualizarMaestraFCV($idMaestra){
        return response()->json(['por desarrollar'], 501);
    }

    // POST maestra-productos/subir-maestra-fcv
    function formpost_subirMaestraFCV(MaestraFCVService $maestraFCVService, Request $request){
        $user = Auth::user();

        // se adjunto un archivo?
        if (!$request->hasFile('file'))
            return redirect()->route("maestraFCV")
                ->with('mensaje-error', "Debe adjuntar la maestra");
        
        // el archivo es valido?
        $archivo = $request->file('file');
        if (!$archivo->isValid())
            return redirect()->route("maestraFCV")
                ->with('mensaje-error', "El archivo adjunto no es válido");

        // ToDO: terminar esto en algun momento... con archivos grandes se cae, el desarrollo se detuvo...
        $res = $maestraFCVService->agregarMaestraFCV($user, $archivo);

        // redireccionar a index
        if(isset($res->error)){
            if($res->codigo==401 || $res->codigo==403)
                return response()->view('errors.403', [], 403);
            else
                return redirect()->route("maestraFCV")->with('mensaje-error', $res->mensaje);
        }
        else
            return redirect()->route("maestraFCV")->with('mensaje-exito', "Productos cargados correctamente");
    }


    // GET maestra-fcv/{idMaestra}/descargar-db
    function descargarDB_maestraFCV(MaestraFCVService $maestraFCVService, $idMaestra){
        $user = Auth::user();
        $archivoMaestra = ArchivoMaestraProductos::find($idMaestra);

        $res = $maestraFCVService->descargarMaestraDesdeDB($user, $archivoMaestra);
        if(isset($res->error)){
            if($res->codigo==403)
                return response()->view('errors.403', [], 403);
            else
                return response()->view('errors.errorConMensaje', [
                    'titulo' =>  $res->campo,
                    'descripcion' => $res->mensaje
                ]);
        }
        else
            return \ArchivosHelper::descargarArchivo($res->maestraPath, "maestra.xlsx");
    }

    // GET maestra-fcv/{idArchivo}/descargar-original
    function descargarOriginal_maestraFCV($idArchivo){
        // el metodo es el mismo para otros clientes, pero solo cambian los permisos
        $user = Auth::user();
        if(!$user || !$user->can('fcv-verMaestra'))
            return response()->view('errors.403', [], 403);

        $archivoMaestra = ArchivoMaestraProductos::find($idArchivo);
        return \ArchivosHelper::descargarArchivo($archivoMaestra->getFullPath(), $archivoMaestra->nombreArchivo);
    }

}
