<?php

namespace App\Http\Middleware;

use App\Clientes;
use Closure;

class BuscarCliente {
    public function handle($request, Closure $next) {
        $cliente = Clientes::find($request->idCliente);
        if(!$cliente)
            return response()->json(['idCliente' => 'Cliente no encontrado'], 404);

        $request->cliente = $cliente;
        return $next($request);
    }
}
