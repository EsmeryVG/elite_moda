<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TienePermiso
{
    public function handle(Request $request, Closure $next, string $permiso)
    {
        if (! Auth::user()?->tienePermiso($permiso)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}