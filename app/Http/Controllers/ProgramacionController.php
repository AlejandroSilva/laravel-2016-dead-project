<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
// Modelos
use App\Clientes;

class ProgramacionController extends Controller {
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */

    // GET programacion/
    public function showIndex(){
        return view('operacional.programacion.programacion-index');
    }

    // GET programacion/mensual
    public function showMensual(){
        $clientesWithLocales = Clientes::allWithSimpleLocales();
        return view('operacional.programacion.programacion-mensual', [
            'clientes' => $clientesWithLocales
        ]);
    }

    // GET programacion/semanal
    public function showSemanal(){
        return view('operacional.programacion.programacion-semanal');
    }

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */
}
