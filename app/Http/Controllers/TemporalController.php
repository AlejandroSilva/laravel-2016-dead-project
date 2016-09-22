<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use File;

class TemporalController extends Controller {
    function show_index() {
        $archivos = File::allFiles(public_path().'/otros-archivos' );
        return view('temporal.index', [
            'archivos' => $archivos
        ]);
    }
    function descargar_otro($file){
        $fileName = public_path().'/otros-archivos/'.$file;
        return response()
            ->download($fileName, $file, [
                'Content-Type'=>'application/force-download',   // forzar la descarga en Opera Mini
                'Pragma'=>'no-cache',
                'Cache-Control'=>'no-cache, must-revalidate'
            ]);
    }
    function post_archivo(Request $request){
        if (!$request->hasFile('thefile'))
            return view('temporal.enviarArchivo', [
                'msg'=>'Debe seleccionar un archivo.'
            ]);

        // revisar que el archivo sea valido
        $archivo = $request->file('thefile');
        if (!$archivo->isValid())
            return view('temporal.enviarArchivo', [
                'msg'=>'El archivo enviado no es valido, intentelo nuevamente'
            ]);

        $fileName = $archivo->getClientOriginalName();
        $archivo->move( public_path().'/otros-archivos/', $fileName);
        chmod(public_path().'/otros-archivos/'.$fileName, 0777);   // 0744 por defecto

        return view('temporal.enviarArchivo', [
            'msg'=>'Recibido correctamente'
        ]);
    }

    // GET usuarioComoOperador/{runUsuario}
    function usuarioComoOperador($usuarioRUN){
        $user = \App\User::where('usuarioRUN', $usuarioRUN)->first();
        if(!$user)
            return response()->json('run no encontrado', 200);

        $datos = $user->__nominasComoOperador__sin_uso__;
        return response()->json(
            $datos->map(function($nomina){
                $inventario = $nomina->inventario;
                $local = $inventario->local;
                $cliente = $local->cliente;
                return "Local: $cliente->nombreCorto $local->nombre, Fecha: $inventario->fechaProgramada, idNomina: $nomina->idNomina";
            })
        );
    }
}
