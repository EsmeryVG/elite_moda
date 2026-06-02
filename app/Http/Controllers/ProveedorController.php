<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProveedorRequest;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedor::orderBy('estado', 'desc')
                          ->orderBy('nombre', 'asc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado === 'activos');
        }

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('rnc', 'like', '%' . $request->buscar . '%')
                  ->orWhere('contacto_nombre', 'like', '%' . $request->buscar . '%');
            });
        }

        $proveedores = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('proveedores._tabla', compact('proveedores'))->render();
        }

        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(ProveedorRequest $request)
    {
        $data = $request->validated();
        $data['nombre'] = ucfirst(strtolower(trim($data['nombre'])));

        $proveedor = Proveedor::create($data);
        $proveedor->codigo = 'PROV-' . str_pad($proveedor->id, 3, '0', STR_PAD_LEFT);
        $proveedor->save();

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    public function show(Proveedor $proveedor)
    {
        $proveedor->load(['ordenesCompra' => fn($q) =>
            $q->orderBy('created_at', 'desc')->limit(10)
        ]);

        return view('proveedores.show', compact('proveedor'));
    }

    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(ProveedorRequest $request, Proveedor $proveedor)
    {
        $data = $request->validated();
        $data['nombre'] = ucfirst(strtolower(trim($data['nombre'])));

        $proveedor->update($data);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        if ($proveedor->ordenesCompra()->exists()) {
            return redirect()->route('proveedores.index')
                ->with('error', 'No puedes desactivar este proveedor porque tiene órdenes de compra asociadas.');
        }

        $proveedor->update(['estado' => false]);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor desactivado correctamente.');
    }

    public function reactivar(Proveedor $proveedor)
    {
        $proveedor->update(['estado' => true]);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor reactivado correctamente.');
    }
}