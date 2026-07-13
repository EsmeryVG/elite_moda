<?php

namespace App\Http\Middleware;

use App\Models\SesionCaja;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TieneSesionCajaAbierta
{
    public function handle(Request $request, Closure $next)
    {
        $tieneSesionAbierta = SesionCaja::abiertas()->exists();

        if (! $tieneSesionAbierta) {
            if (Auth::user()->esAdministrador()) {
                return redirect()->route('sesiones_caja.abrir')
                    ->with('warning', 'No hay ninguna sesión de caja abierta. Ábrela para comenzar a vender.');
            }

            return redirect()->route('sesiones_caja.index')
                ->with('warning', 'No hay ninguna sesión de caja abierta. Contacta a un administrador.');
        }

        return $next($request);
    }
}