<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlmacenRequest;
use App\Models\Almacen;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    public function index(Request $request)
    {
        $query = Almacen::with('sucursal')
                        ->orderBy('estado', 'desc')
                        ->orderBy('nombre', 'asc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado === 'activos');
        }

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        if ($request->filled('sucursal')) {
            $query->where('sucursal_id', $request->sucursal);
        }

        $almacenes  = $query->paginate(10)->withQueryString();
        $sucursales = Sucursal::activas()->orderBy('nombre')->get();

        if ($request->ajax()) {
            return view('almacenes._tabla', compact('almacenes'))->render();
        }

        return view('almacenes.index', compact('almacenes', 'sucursales'));
    }

    public function create()
    {
        $sucursales = Sucursal::activas()->orderBy('nombre')->get();
        return view('almacenes.create', compact('sucursales'));
    }

    public function store(AlmacenRequest $request)
    {
        $data = $request->validated();
        $data['nombre'] = ucfirst(strtolower(trim($data['nombre'])));

        Almacen::create($data);

        return redirect()->route('almacenes.index')
            ->with('success', 'Almacén creado correctamente.');
    }

    public function edit(Almacen $almacen)
    {
        $sucursales = Sucursal::activas()->orderBy('nombre')->get();
        return view('almacenes.edit', compact('almacen', 'sucursales'));
    }

    public function update(AlmacenRequest $request, Almacen $almacen)
    {
        $data = $request->validated();
        $data['nombre'] = ucfirst(strtolower(trim($data['nombre'])));

        $almacen->update($data);

        return redirect()->route('almacenes.index')
            ->with('success', 'Almacén actualizado correctamente.');
    }

    public function destroy(Almacen $almacen)
    {
        if ($almacen->stocks()->exists()) {
            return redirect()->route('almacenes.index')
                ->with('error', 'No puedes desactivar este almacén porque tiene stock registrado.');
        }

        $almacen->update(['estado' => false]);

        return redirect()->route('almacenes.index')
            ->with('success', 'Almacén desactivado correctamente.');
    }

    public function reactivar(Almacen $almacen)
    {
        $almacen->update(['estado' => true]);

        return redirect()->route('almacenes.index')
            ->with('success', 'Almacén reactivado correctamente.');
    }
}