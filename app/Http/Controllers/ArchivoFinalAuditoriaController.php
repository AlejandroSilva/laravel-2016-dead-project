<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ArchivoFinalAuditoriaController extends Controller {

    // GET archivo-final-auditoria/{idArchivoCruzVerde}/descargar-zip
    function api_descargarZIP($idArchivoCruzVerde){

        $resultadoBuscarArchivo = \ArchivoFinalAuditoriaFCV::buscarArchivo($idArchivoCruzVerde);

        // existe el archivo zip?
        if($resultadoBuscarArchivo->error){
            return response()->json($resultadoBuscarArchivo->error, 400);
        }

        //  Extraer el zip
        $resultadoExtraccion = \ArchivoFinalAuditoriaFCV::descomprimirZip($resultadoBuscarArchivo->fullPath);
        if( $resultadoExtraccion->error!=null ){
            return response()->json(['error'=>$resultadoExtraccion->error], 400);
        }

        // todo: buscar archivo, leer datos, cargar a la bd, mostrar la vista, generar el pdf, generar el xlsx, comprimir, y descargar el zip

        return response()->json($resultadoBuscarArchivo);
    }

}
