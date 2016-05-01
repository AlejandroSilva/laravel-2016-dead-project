<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
// Modelos
use App\Comunas;

class GeoController extends Controller {
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */
    public function show_index(){
        return view('operacional.geo.geo-index');
    }


    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */

    public function api_getComunas(){
        $comunas = Comunas::with([
            'provincia.region.zona',
            'subgeo.geo'
        ])->get()->toArray();

        $comunas_formato =  array_map( [$this, 'darFormatoComuna'], $comunas );
        return response()->json($comunas_formato, 200);
    }

    /**
     * ##########################################################
     * API DE INTERACCION CON LA OTRA PLATAFORMA
     * ##########################################################
     */

    /**
     * ##########################################################
     * funciones privadas
     * ##########################################################
     */

    private function darFormatoComuna($comuna){
        return [
            // comuna
            'cutComuna' => $comuna['cutComuna'],
            'comuna' => $comuna['nombre'],
            // provincia
            'cutProvincia' => $comuna['provincia']['cutProvincia'],
            'provincia' => $comuna['provincia']['nombre'],
            // region
            'cutRegion' => $comuna['provincia']['region']['cutRegion'],
            'region' => $comuna['provincia']['region']['nombreCorto'],
            // zona
            'idZona' => $comuna['provincia']['region']['zona']['idZona'],
            'zona' => $comuna['provincia']['region']['zona']['nombre'],
            // Geo
            'idGeo' => $comuna['subgeo']['geo']['idGeo'],
            'geo' => $comuna['subgeo']['geo']['nombre'],
            // Subgeo
            'idSubgeo' => $comuna['subgeo']['idSubgeo'],
            'subgeo' => $comuna['subgeo']['nombre'],
            'totalLocales' => 99
        ];
    }
}
