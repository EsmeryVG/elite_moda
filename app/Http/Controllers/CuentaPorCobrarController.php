<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\CuentaPorCobrar;
use App\Models\PagoCredito;
use App\Models\TipoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CuentaPorCobrarController extends Controller
{
    public function index(Request $request)
    {
        $this->actualizarVencidas();

        $cuentas = $this->construirQuery($request)->paginate(15);
        $tiposPago = TipoPago::activos()->orderBy('nombre')->get();

        return view('cuentas_por_cobrar.index', compact('cuentas', 'tiposPago'));
    }

    public function tabla(Request $request)
    {
        $cuentas = $this->construirQuery($request)->paginate(15);

        return view('cuentas_por_cobrar._tabla', compact('cuentas'));
    }

    private function construirQuery(Request $request)
    {
        $query = CuentaPorCobrar::with('cliente', 'venta');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('busqueda')) {
            $b = $request->busqueda;
            $query->where(function ($q) use ($b) {
                $q->where('codigo', 'like', "%{$b}%")
                  ->orWhereHas('cliente', fn ($c) => $c->where('nombre', 'like', "%{$b}%"));
            });
        }

        return $query->orderByDesc('fecha_emision');
    }

    private function actualizarVencidas(): void
    {
        CuentaPorCobrar::whereIn('estado', ['pendiente', 'parcial'])
            ->where('fecha_vencimiento', '<', now()->startOfDay())
            ->update(['estado' => 'vencida']);
    }

    public function show(CuentaPorCobrar $cuentaPorCobrar)
    {
        $cuentaPorCobrar->load('cliente', 'venta', 'pagos.tipoPago', 'pagos.usuario');
        $tiposPago = TipoPago::activos()->orderBy('nombre')->get();

        return view('cuentas_por_cobrar.show', compact('cuentaPorCobrar', 'tiposPago'));
    }

   public function registrarAbono(Request $request, CuentaPorCobrar $cuentaPorCobrar)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'tipo_pago_id' => 'required|exists:tipos_pago,id',
            'referencia' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string|max:255',
        ]);

        if ($request->monto > $cuentaPorCobrar->monto_pendiente) {
            return back()->withErrors([
                'monto' => 'El abono no puede exceder el saldo pendiente (RD$ ' . number_format($cuentaPorCobrar->monto_pendiente, 2) . ').',
            ])->withInput();
        }

        DB::transaction(function () use ($request, $cuentaPorCobrar) {
            PagoCredito::create([
                'cuenta_por_cobrar_id' => $cuentaPorCobrar->id,
                'tipo_pago_id' => $request->tipo_pago_id,
                'usuario_id' => Auth::id(),
                'monto' => $request->monto,
                'referencia' => $request->referencia,
                'fecha' => now(),
                'observaciones' => $request->observaciones,
            ]);

            $nuevoPagado = $cuentaPorCobrar->monto_pagado + $request->monto;
            $nuevoPendiente = max(0, $cuentaPorCobrar->monto_total - $nuevoPagado);

            $cuentaPorCobrar->update([
                'monto_pagado' => $nuevoPagado,
                'monto_pendiente' => $nuevoPendiente,
                'estado' => $nuevoPendiente <= 0.01 ? 'pagada' : 'parcial',
            ]);

            $cuentaPorCobrar->cliente->decrement('balance_credito', $request->monto);
        });

        return back()->with('success', 'Abono registrado correctamente.');
    }
}