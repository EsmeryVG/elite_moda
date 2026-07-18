<?php

namespace App\Http\Controllers;

use App\Models\CajaChica;
use App\Models\Configuracion;
use App\Models\Gasto;
use App\Models\MovimientoCajaChica;
use App\Models\Sucursal;
use App\Models\CategoriaGasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CajaChicaController extends Controller
    {
        public function show()
        {
            $cajaChica = CajaChica::activas()->first();

            if (! $cajaChica) {
                $sucursalPrincipal = Sucursal::where('es_principal', true)->first();

                $cajaChica = CajaChica::create([
                    'sucursal_id' => $sucursalPrincipal?->id,
                    'nombre' => 'Caja Chica',
                    'monto_base' => (float) Configuracion::get('caja_chica_monto_base', 2000),
                    'monto_disponible' => 0,
                    'estado' => 'activa',
                ]);
            }

            $movimientos = $cajaChica->movimientos()->orderByDesc('fecha')->limit(20)->get();
            $categorias = CategoriaGasto::activas()->orderBy('nombre')->get();

            return view('caja_chica.show', compact('cajaChica', 'movimientos', 'categorias'));
        }

    public function registrarGasto(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'monto' => 'required|numeric|min:0.01',
            'categoria_gasto_id' => 'required|exists:categorias_gasto,id',
        ]);

        $cajaChica = CajaChica::activas()->firstOrFail();

        if ($request->monto > $cajaChica->monto_disponible) {
            return back()->withErrors([
                'monto' => 'El monto excede el saldo disponible en caja chica (RD$ ' . number_format($cajaChica->monto_disponible, 2) . '). Solicita una reposición.',
            ])->withInput();
        }

        DB::transaction(function () use ($request, $cajaChica) {
            MovimientoCajaChica::create([
                'caja_chica_id' => $cajaChica->id,
                'usuario_id' => Auth::id(),
                'tipo' => 'egreso',
                'monto' => $request->monto,
                'concepto' => $request->nombre,
                'fecha' => now(),
            ]);

            $cajaChica->decrement('monto_disponible', $request->monto);
        });

        return back()->with('success', 'Gasto registrado correctamente.');
    }

    public function reponer(Request $request)
{
    $cajaChica = CajaChica::activas()->firstOrFail();

    abort_unless($cajaChica->puedeReponerNormal(), 422, 'Aún no corresponde una reposición normal.');

    $categoriaGeneral = CategoriaGasto::where('nombre', 'General')->firstOrFail();

    $diferencia = round($cajaChica->monto_base - $cajaChica->monto_disponible, 2);

    DB::transaction(function () use ($cajaChica, $diferencia, $categoriaGeneral) {
        Gasto::create([
            'categoria_gasto_id' => $categoriaGeneral->id,
            'origen' => 'caja_chica',
            'nombre' => 'Reposición de caja chica',
            'monto' => $diferencia,
            'usuario_id' => Auth::id(),
            'fecha' => now(),
            'es_reposicion_extraordinaria' => false,
        ]);

        MovimientoCajaChica::create([
            'caja_chica_id' => $cajaChica->id,
            'usuario_id' => Auth::id(),
            'tipo' => 'ingreso',
            'monto' => $diferencia,
            'concepto' => 'Reposición de caja chica',
            'fecha' => now(),
        ]);

        $cajaChica->update([
            'monto_disponible' => $cajaChica->monto_base,
            'fecha_ultima_reposicion' => now()->toDateString(),
        ]);
    });

    return back()->with('success', 'Caja chica repuesta correctamente.');
}

    public function reponerExtraordinaria(Request $request)
{
    abort_unless(Auth::user()->tienePermiso('caja_chica.gestionar'), 403, 'No tienes permiso para hacer una reposición extraordinaria.');

    $request->validate([
        'monto' => 'required|numeric|min:0.01',
        'observaciones' => 'required|string|max:255',
    ], [
        'observaciones.required' => 'Debes indicar el motivo de la reposición extraordinaria.',
    ]);

    $cajaChica = CajaChica::activas()->firstOrFail();
    $categoriaGeneral = CategoriaGasto::where('nombre', 'General')->firstOrFail();

    DB::transaction(function () use ($request, $cajaChica, $categoriaGeneral) {
        Gasto::create([
            'categoria_gasto_id' => $categoriaGeneral->id,
            'origen' => 'caja_chica',
            'nombre' => 'Reposición extraordinaria de caja chica',
            'monto' => $request->monto,
            'usuario_id' => Auth::id(),
            'fecha' => now(),
            'es_reposicion_extraordinaria' => true,
            'observaciones' => $request->observaciones,
        ]);

        MovimientoCajaChica::create([
            'caja_chica_id' => $cajaChica->id,
            'usuario_id' => Auth::id(),
            'tipo' => 'ingreso',
            'monto' => $request->monto,
            'concepto' => 'Reposición extraordinaria — ' . $request->observaciones,
            'fecha' => now(),
        ]);

        $cajaChica->increment('monto_disponible', $request->monto);
    });

    return back()->with('success', 'Reposición extraordinaria registrada correctamente.');
}
}