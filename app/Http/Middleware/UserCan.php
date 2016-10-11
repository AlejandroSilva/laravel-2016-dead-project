<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class UserCan {

    public function handle($request, Closure $next, $permission){
        // Verificar que el usuario tenga los permisos para crear un local
        $user = Auth::user();
        if(!$user || !$user->can($permission)){
            // dependiendo de la ruta, muestra una pagina o un json
            if($request->is('api/*'))
                return response()->json(['Auth' => 'no tiene permisos para realizar esta accion'], 403);
            else
                return view('errors.403');
        }

        return $next($request);
    }
}
