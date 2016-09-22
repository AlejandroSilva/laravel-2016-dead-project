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
        return response()->download($fileName, $file);
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
}
