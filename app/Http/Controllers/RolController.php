<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RolController extends Controller
{
    public function index()
    {
        $roles = Rol::withCount('usuarios')->orderBy('nombre')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100|unique:roles,nombre',
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.unique'   => 'Ya existe un rol con ese nombre.',
        ]);

        $data['nombre'] = ucfirst(trim($data['nombre']));
        Rol::create($data);

        return redirect()->route('roles.index')
            ->with('success', 'Rol creado correctamente.');
    }

    public function edit(Rol $rol)
    {
        $permisosPorModulo = Permiso::orderBy('modulo')->orderBy('nombre')->get()->groupBy('modulo');
        $permisosAsignados = $rol->permisos()->pluck('permisos.id')->toArray();

        return view('roles.edit', compact('rol', 'permisosPorModulo', 'permisosAsignados'));
    }

    public function update(Request $request, Rol $rol)
    {
        $data = $request->validate([
            'nombre' => [
                'required', 'string', 'max:100',
                Rule::unique('roles', 'nombre')->ignore($rol->id),
            ],
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.unique'   => 'Ya existe un rol con ese nombre.',
        ]);

        $data['nombre'] = ucfirst(trim($data['nombre']));
        $rol->update($data);

        $rol->permisos()->sync($request->input('permisos', []));

        return redirect()->route('roles.edit', $rol)
            ->with('success', 'Rol y permisos actualizados correctamente.');
    }

    public function destroy(Rol $rol)
    {
        if ($rol->usuarios()->exists()) {
            return redirect()->route('roles.index')
                ->with('error', 'No puedes eliminar este rol porque tiene usuarios asociados.');
        }

        $rol->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rol eliminado correctamente.');
    }
}