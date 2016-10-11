<?php

namespace App\Http\Middleware;

use App\Locales;
use Closure;

class BuscarLocal {

    public function handle($request, Closure $next) {
        $local = Locales::find($request->idLocal);
        if(!$local)
            return response()->json(['idLocal' => 'Local no encontrado'], 404);

        $request->local = $local;
        return $next($request);
    }
}
