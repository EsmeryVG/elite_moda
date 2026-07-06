<?php

namespace App\Http\Controllers;

use App\Models\TipoPago;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TipoPagoController extends Controller
{
    public function index()
    {
        $tiposPago = TipoPago::withCount('pagos')->orderBy('nombre')->get();
        return view('tipos_pago.index', compact('tiposPago'));
    }

    public function create()
    {
        return view('tipos_pago.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:tipos_pago,nombre',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique'   => 'Ya existe un tipo de pago con ese nombre.',
        ]);

        TipoPago::create([
            'nombre' => ucfirst(trim($request->nombre)),
            'estado' => true,
        ]);

        return redirect()->route('tipos_pago.index')
            ->with('success', 'Tipo de pago creado correctamente.');
    }

    public function edit(TipoPago $tipo_pago)
    {
        return view('tipos_pago.edit', compact('tipo_pago'));
    }

    public function update(Request $request, TipoPago $tipo_pago)
    {
        $request->validate([
            'nombre' => [
                'required', 'string', 'max:100',
                Rule::unique('tipos_pago', 'nombre')->ignore($tipo_pago->id),
            ],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique'   => 'Ya existe un tipo de pago con ese nombre.',
        ]);

        $tipo_pago->update([
            'nombre' => ucfirst(trim($request->nombre)),
        ]);

        return redirect()->route('tipos_pago.index')
            ->with('success', 'Tipo de pago actualizado correctamente.');
    }

    public function destroy(TipoPago $tipo_pago)
    {
        if ($tipo_pago->pagos()->exists()) {
            return redirect()->route('tipos_pago.index')
                ->with('error', 'No puedes desactivar este tipo de pago porque tiene pagos asociados.');
        }

        $tipo_pago->update(['estado' => false]);

        return redirect()->route('tipos_pago.index')
            ->with('success', 'Tipo de pago desactivado correctamente.');
    }

    public function reactivar(TipoPago $tipo_pago)
    {
        $tipo_pago->update(['estado' => true]);

        return redirect()->route('tipos_pago.index')
            ->with('success', 'Tipo de pago reactivado correctamente.');
    }
}