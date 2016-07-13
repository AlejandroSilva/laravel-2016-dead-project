<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
// Modelos
use App\Comunas;

class OtrosController extends Controller {
    
    // GET api/comunas
    function api_comunas(){
        return response()->json(
            Comunas::all()->map(function($comuna){
                return [
                    'cutComuna' => $comuna->cutComuna,
                    'nombre' => $comuna->nombre,
                ];
            })
        );
    }
}
