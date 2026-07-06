<?php

namespace App\Http\Controllers;

use App\Models\GrupoCliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GrupoClienteController extends Controller
{
    public function index()
    {
        $grupos = GrupoCliente::withCount('clientes')
                              ->orderBy('nombre')
                              ->get();

        return view('grupo_clientes.index', compact('grupos'));
    }

    public function create()
    {
        return view('grupo_clientes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100|unique:grupo_clientes,nombre',
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.required' => 'El nombre del grupo es obligatorio.',
            'nombre.unique'   => 'Ya existe un grupo con ese nombre.',
        ]);

        $data['nombre'] = ucfirst(strtolower(trim($data['nombre'])));

        GrupoCliente::create($data);

        return redirect()->route('grupo_clientes.index')
            ->with('success', 'Grupo creado correctamente.');
    }

    public function edit(GrupoCliente $grupo_cliente)
    {
        return view('grupo_clientes.edit', compact('grupo_cliente'));
    }

    public function update(Request $request, GrupoCliente $grupo_cliente)
    {
        $data = $request->validate([
            'nombre'      => [
                'required', 'string', 'max:100',
                Rule::unique('grupo_clientes', 'nombre')->ignore($grupo_cliente->id),
            ],
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.required' => 'El nombre del grupo es obligatorio.',
            'nombre.unique'   => 'Ya existe un grupo con ese nombre.',
        ]);

        $data['nombre'] = ucfirst(strtolower(trim($data['nombre'])));

        $grupo_cliente->update($data);

        return redirect()->route('grupo_clientes.index')
            ->with('success', 'Grupo actualizado correctamente.');
    }

    public function destroy(GrupoCliente $grupo_cliente)
    {
        if ($grupo_cliente->clientes()->exists()) {
            return redirect()->route('grupo_clientes.index')
                ->with('error', 'No puedes eliminar este grupo porque tiene clientes asociados.');
        }

        $grupo_cliente->delete();

        return redirect()->route('grupo_clientes.index')
            ->with('success', 'Grupo eliminado correctamente.');
    }
}