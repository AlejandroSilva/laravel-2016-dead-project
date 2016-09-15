<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class TemporalController extends Controller {

    function show_index() {
        return view('temporal.index');
    }

    function post_archivo(Request $request){
//        $archivo = $request->file('elarchivo');
//        return view('temporal.enviarArchivo', [
//            'msg'=>$archivo = $archivo->getClientOriginalName()
//        ]);

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
        $archivo->move( public_path().'/otrosArchivos', $fileName);
        chmod(public_path().'/otrosArchivos/'.$fileName, 0774);   // 0744 por defecto

        return view('temporal.enviarArchivo', [
            'msg'=>'Recibido correctamente'
        ]);
    }
}
