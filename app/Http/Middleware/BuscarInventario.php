<?php

namespace App\Http\Middleware;

use App\Inventarios;
use Closure;

class BuscarInventario {
    public function handle($request, Closure $next) {
        $inventario = Inventarios::find($request->idInventario);
        if(!$inventario) {
            if ($request->ajax() || $request->is('api/*'))
                return response()->json(['idInventario' => 'inventario no encontrado'], 404);
            else
                return view('errors.errorConMensaje', [
                    'titulo' => 'Inventario no encontrado', 'descripcion' => 'El inventario que busca no ha sido encontrado.'
                ]);
        }

        $request->inventario = $inventario;
        return $next($request);
    }
}
