<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    public function index(Request $request)
    {
        $query = Caja::with('sucursal', 'almacen')->orderBy('nombre');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $cajas = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return view('cajas._tabla', compact('cajas'))->render();
        }

        return view('cajas.index', compact('cajas'));
    }

    public function create()
    {
        $sucursales = Sucursal::where('estado', true)->orderBy('nombre')->get();

        return view('cajas.create', compact('sucursales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ], [
            'nombre.required' => 'El nombre de la caja es obligatorio.',
        ]);

        Caja::create([
            'nombre' => $request->nombre,
            'sucursal_id' => $request->sucursal_id,
            'estado' => 'activa',
        ]);

        return redirect()->route('cajas.index')
            ->with('success', 'Caja creada correctamente.');
    }

    public function edit(Caja $caja)
    {
        $sucursales = Sucursal::where('estado', true)->orderBy('nombre')->get();

        return view('cajas.edit', compact('caja', 'sucursales'));
    }

    public function update(Request $request, Caja $caja)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ]);

        // Si cambia de sucursal, el almacén asignado ya no aplica —
        // se recalculará automáticamente en la próxima apertura de sesión.
        $cambioSucursal = $caja->sucursal_id != $request->sucursal_id;

        $caja->update([
            'nombre' => $request->nombre,
            'sucursal_id' => $request->sucursal_id,
            'almacen_id' => $cambioSucursal ? null : $caja->almacen_id,
        ]);

        return redirect()->route('cajas.index')
            ->with('success', 'Caja actualizada correctamente.');
    }

    public function destroy(Caja $caja)
    {
        if ($caja->sesiones()->where('estado', 'abierta')->exists()) {
            return redirect()->route('cajas.index')
                ->with('error', 'No puedes desactivar una caja con una sesión abierta.');
        }

        $caja->update(['estado' => 'inactiva']);

        return redirect()->route('cajas.index')
            ->with('success', 'Caja desactivada correctamente.');
    }

    public function reactivar(Caja $caja)
    {
        $caja->update(['estado' => 'activa']);

        return redirect()->route('cajas.index')
            ->with('success', 'Caja reactivada correctamente.');
    }
}