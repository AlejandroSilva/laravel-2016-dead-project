<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

// Modelos
use App\Clientes;

class ClientesController extends Controller {
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */

    // GET admin/clientes
    public function verLista(){
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

    // GET clientes
    public function getClientes_json(){
        return Clientes::all();
    }

    // GET clientes/locales
    public function getClientesWithLocales_json(){
        return Clientes::allWithSimpleLocales();
    }

}
