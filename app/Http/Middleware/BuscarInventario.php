<?php

namespace App\Http\Middleware;

use App\Inventarios;
use Closure;

class BuscarInventario {
    public function handle($request, Closure $next) {
        $inventario = Inventarios::find($request->idInventario);
        if(!$inventario)
            return response()->json(['idInventario' => 'inventario no encontrado'], 404);

        $request->inventario = $inventario;
        return $next($request);
    }
}
