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
     * MAESTRA FARMACIA CRUZ VERDE
     */
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
        // solo la puede descargar en caso que efectivamente sea una maestra de FCV
        if($archivoMaestra->idCliente!=2)
            return response()->view('errors.403', [], 403);

        return \ArchivosHelper::descargarArchivo($archivoMaestra->getFullPath(), $archivoMaestra->nombreOriginal);
    }

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
