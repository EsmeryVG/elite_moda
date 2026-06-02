<?php

namespace App\Http\Controllers;

use App\Http\Requests\SucursalRequest;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index(Request $request)
    {
        $query = Sucursal::orderBy('es_principal', 'desc')
                         ->orderBy('nombre', 'asc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado === 'activas');
        }

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        $sucursales = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('sucursales._tabla', compact('sucursales'))->render();
        }

        return view('sucursales.index', compact('sucursales'));
    }

    public function create()
    {
        return view('sucursales.create');
    }

    public function store(SucursalRequest $request)
    {
        $data = $request->validated();
        $data['nombre'] = ucfirst(strtolower(trim($data['nombre'])));

        // Si se marca como principal, desmarcar las demás
        if (!empty($data['es_principal'])) {
            Sucursal::where('es_principal', true)->update(['es_principal' => false]);
        }

        $sucursal = Sucursal::create($data);
        $sucursal->codigo = 'SUC-' . str_pad($sucursal->id, 3, '0', STR_PAD_LEFT);
        $sucursal->save();

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal creada correctamente.');
    }

    public function edit(Sucursal $sucursal)
    {
        return view('sucursales.edit', compact('sucursal'));
    }

    public function update(SucursalRequest $request, Sucursal $sucursal)
    {
        $data = $request->validated();
        $data['nombre'] = ucfirst(strtolower(trim($data['nombre'])));

        if (!empty($data['es_principal'])) {
            Sucursal::where('es_principal', true)
                    ->where('id', '!=', $sucursal->id)
                    ->update(['es_principal' => false]);
        }

        $sucursal->update($data);

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal actualizada correctamente.');
    }

    public function destroy(Sucursal $sucursal)
    {
        if ($sucursal->almacenes()->exists()) {
            return redirect()->route('sucursales.index')
                ->with('error', 'No puedes desactivar esta sucursal porque tiene almacenes asociados.');
        }

        $sucursal->update(['estado' => false]);

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal desactivada correctamente.');
    }

    public function reactivar(Sucursal $sucursal)
    {
        $sucursal->update(['estado' => true]);

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal reactivada correctamente.');
    }
}