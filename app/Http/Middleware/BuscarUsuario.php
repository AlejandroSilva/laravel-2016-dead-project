<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class BuscarUsuario {

    public function handle($request, Closure $next) {
        // el usuario existe?
        $usuario = User::find($request->idUsuario);
        if(!$usuario)
            return response()->json(['idUsuario'=>'El usuario no existe'], 400);

        $request->usuario = $usuario;
        return $next($request);
    }
}
