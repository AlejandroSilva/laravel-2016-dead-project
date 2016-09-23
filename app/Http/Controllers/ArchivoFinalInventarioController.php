<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
// Carbon
use Carbon\Carbon;
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

        // mover el archivo junto a los otros stocks enviados
        $timestamp = Carbon::now()->format("Y-m-d_h-i-s");
        $nombreOriginal = $archivo->getClientOriginalName();
        $fileName = "[$timestamp][$cliente->nombreCorto][$local->numero][$inventario->fechaProgramada] $nombreOriginal";
        $path = public_path()."/$cliente->nombreCorto/archivoFinalInventario/";
        // guardar el archivo en una carpeta publica, y cambiar los permisos para que el grupo pueda modifiarlos
        $archivo->move( $path, $fileName);
        chmod($path.$fileName, 0774);   // 0744 por defecto

        // extrer el archivo de acta del izp
        $resultadoExtraccion = \ArchivoFinalInventarioFCV::descomprimirZip($path.$fileName);
        if( $resultadoExtraccion->error!=null )
            return response()->json(['error'=>$resultadoExtraccion->error], 400);

        // Parsear el archivo de acta si este existe
        $acta = null;
        if( $resultadoExtraccion->acta_v2!=null )
            $acta = \ArchivoFinalInventarioFCV::parsearActa_v2($resultadoExtraccion->acta_v2);
        else if( $resultadoExtraccion->acta_v1!=null )
            $acta = \ArchivoFinalInventarioFCV::parsearActa_v1($resultadoExtraccion->acta_v1);

        // revisar que se haya parseado y encontrado correctaemnte el acta
        if($acta==null)
            return response()->json(['error'=>'No se encontro un archivo de acta dentro del zip'], 400);

        // verificar que el CECO local indicado en el acta, es el mismo que el CECO del local inventariado
        $ceco_acta = $acta['ceco_local'];
        $ceco_inventario = $inventario->local->numero;
        if($ceco_acta!=$ceco_inventario){
            return response()->json(
                ['error'=>"El local indicado en el acta, no corresponde con el inventario seleccionado (acta:$ceco_acta|inventario:$ceco_inventario"],
                400
            );
        }

        // finalmente, actualizar el acta con los datos entregados
        $inventario->insertarOActualizarActa($acta);
        return response()->json($acta);
    }
    function indicadores(){
        //dd("fff");
      return view('operacional.clientes.indicadores');
    }
}
