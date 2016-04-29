<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
// Modelos
use Auth;
use App\Regiones;
use App\Zonas;

class RegionesController extends Controller {

    public function showMantenedorRegiones(){
        $regiones = Regiones::all()->sortBy('cutRegion');
        $zonas = Zonas::all()->sortBy('idZona');
        return view('operacional.regiones.mantenedorRegiones', [
            'regiones' => $regiones,
            'zonas' => $zonas
        ]);
    }

    public function api_actualizar($cutRegion, Request $request){

        $region = Regiones::find($cutRegion);
        if($region) {
            if(isset($request->idZona)) {
                $region->idZona = $request->idZona;
            }
            $region->save();
            return Redirect::to("regiones");


        }else{
            return response()->json([], 404);
        }
    }

}