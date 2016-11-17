<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
// Utils
//use DB;
//use Response;
use Auth;
// Models
use App\ArchivoMaestraProductos;

class ArchivoRespuestaWOMController extends Controller {

    // GET archivos-respuesta-wom
    function show_index(){
        // TODO ...........

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
}
