<?php

namespace App\Http\Controllers;

use App\Models\SesionCaja;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CuadreDiarioController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->filled('fecha')
            ? Carbon::parse($request->fecha)
            : now();

        $sesiones = SesionCaja::with('caja', 'usuarioApertura', 'usuarioCierre')
            ->where('estado', 'cerrada')
            ->whereDate('fecha_cierre', $fecha)
            ->orderBy('caja_id')
            ->orderBy('fecha_apertura')
            ->get();

        $totalEsperado = $sesiones->sum('monto_cierre_esperado');
        $totalReal = $sesiones->sum('monto_cierre_real');
        $totalDiferencia = $sesiones->sum('diferencia');
        $sesionesConDiferencia = $sesiones->where('diferencia', '!=', 0)->count();

        return view('cuadre_diario.index', compact(
            'sesiones', 'fecha', 'totalEsperado', 'totalReal', 'totalDiferencia', 'sesionesConDiferencia'
        ));
    }
}