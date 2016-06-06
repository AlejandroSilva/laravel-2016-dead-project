<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

// Modelos
use App\Clientes;
use App\Locales;

class ClientesController extends Controller {
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */

    // GET admin/clientes
    public function show_Lista(){
        $clientes = Clientes::get();

        return view('operacional.clientes.clientes', [
            'clientes' => $clientes
        ]);
    }

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */

    // GET api/clientes
    public function api_getClientes(){
        return Clientes::all();
    }
}
