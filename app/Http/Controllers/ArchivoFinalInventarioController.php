<?php

namespace App\Http\Controllers;

use App\ActasInventariosFCV;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
// Nominas
use App\Inventarios;

class ArchivoFinalInventarioController extends Controller {

    // POST api/archivo-final-inventario/{idInventario}/upload-zip
    function api_uploadZIP(Request $request, $idInventario){
        // todo validar permisos

        // nomina existe?
        $inventario = Inventarios::find($idInventario);
        if(!$inventario)
            return response()->json(['error' => 'El inventario indicado no existe'], 400);
        $local = $inventario->local;
        $cliente = $local->cliente;

        // se adjunto un archivo?
        if (!$request->hasFile('archivoFinalZip'))
            return response()->json(['error' => 'Debe adjuntar el archivo zip.'], 400);

        // el archivo es valido?
        $archivo = $request->file('archivoFinalZip');
        if (!$archivo->isValid())
            return response()->json(['error' => 'El archivo adjuntado no es valido.'], 400);

        // mover el archivo a la carpeta correspondiente
        $archivoFinal = \ArchivoFinalInventarioFCV::moverACarpeta($archivo, $cliente->nombreCorto, $local->numero, $inventario->fechaProgramada);

        // paso 1) Extraer el archivo de acta del zip
        $resultadoExtraccion = \ArchivoFinalInventarioFCV::descomprimirZip($archivoFinal['fullPath']);
        if( $resultadoExtraccion->error!=null ){
            $inventario->agregarArchivoFinal(Auth::user(), $archivoFinal, $resultadoExtraccion->error);
            return response()->json(['error'=>$resultadoExtraccion->error], 400);
        }

        // paso 2) Parsear el archivo de acta si este existe
        $resultadoActa = \ArchivoFinalInventarioFCV::parsearActa($resultadoExtraccion->acta_v1, $resultadoExtraccion->acta_v2, $local->numero);
        if( $resultadoActa->error!=null ){
            $inventario->agregarArchivoFinal(Auth::user(), $archivoFinal, $resultadoActa->error);
            return response()->json(['error'=>$resultadoActa->error], 400);
        }

        // finalmente, actualizar el acta con los datos entregados
        $inventario->insertarOActualizarActa($resultadoActa->acta);
        $inventario->agregarArchivoFinal(Auth::user(), $archivoFinal, null);

        return response()->json($resultadoActa->acta);
    }


    public function show_inventario($idInventario){
        // existe el inventario?
        $inventario = Inventarios::find($idInventario);
        if(!$inventario)
            return response()->json(['error' => 'El inventario indicado no existe'], 400);
        $acta = $inventario->actaInventarioFCV;
        return view('operacional.inventario.inventario-archivofinal', ['acta'=>$acta] );
    }

}


