<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// importar los modelos
use App\Clientes;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ClientesController extends Controller {
    public function verLista(){
        $clientes = Clientes::get();

        return view('administracion.clientes.verLista', [
            'clientes' => $clientes
        ]);
    }
}
