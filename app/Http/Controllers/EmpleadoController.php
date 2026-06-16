<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $query = Empleado::with('usuario')
                         ->orderBy('estado', 'desc')
                         ->orderBy('nombre', 'asc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado === 'activos');
        }

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre',   'like', '%' . $request->buscar . '%')
                  ->orWhere('apellido', 'like', '%' . $request->buscar . '%')
                  ->orWhere('cedula',   'like', '%' . $request->buscar . '%')
                  ->orWhere('codigo',   'like', '%' . $request->buscar . '%')
                  ->orWhere('cargo',    'like', '%' . $request->buscar . '%');
            });
        }

        $empleados = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('empleados._tabla', compact('empleados'))->render();
        }

        return view('empleados.index', compact('empleados'));
    }

    public function create()
    {
        $usuarios = User::activos()
                        ->whereDoesntHave('empleado')
                        ->orderBy('name')
                        ->get();

        return view('empleados.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cedula'               => 'required|string|regex:/^[0-9]{11}$/|unique:empleados,cedula',
            'nombre'               => 'required|string|max:150',
            'apellido'             => 'required|string|max:150',
            'telefono'             => ['nullable', 'string', 'regex:/^[0-9]{10}$/'],
            'direccion'            => 'nullable|string|max:255',
            'cargo'                => 'nullable|string|max:100',
            'salario_base'         => 'nullable|numeric|min:0',
            'comision_porcentaje'  => 'nullable|numeric|min:0|max:100',
            'fecha_ingreso'        => 'nullable|date',
            'user_id'              => 'nullable|exists:users,id',
        ], [
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.unique'   => 'Esta cédula ya está registrada.',
            'cedula.regex'    => 'La cédula debe tener exactamente 11 dígitos.',
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'telefono.regex'  => 'El teléfono debe tener exactamente 10 dígitos.',
        ]);

        $empleado = Empleado::create([
            'user_id'              => $request->user_id,
            'cedula'               => $request->cedula,
            'nombre'               => ucfirst(strtolower(trim($request->nombre))),
            'apellido'             => ucfirst(strtolower(trim($request->apellido))),
            'telefono'             => $request->telefono,
            'direccion'            => $request->direccion,
            'cargo'                => $request->cargo,
            'salario_base'         => $request->salario_base ?? 0,
            'comision_porcentaje'  => $request->comision_porcentaje ?? 0,
            'fecha_ingreso'        => $request->fecha_ingreso,
            'estado'               => true,
        ]);

        $empleado->codigo = 'EMP-' . str_pad($empleado->id, 3, '0', STR_PAD_LEFT);
        $empleado->save();

        return redirect()->route('empleados.index')
            ->with('success', 'Empleado creado correctamente.');
    }

    public function edit(Empleado $empleado)
    {
        $usuarios = User::activos()
                        ->where(function ($q) use ($empleado) {
                            $q->whereDoesntHave('empleado')
                              ->orWhereHas('empleado', fn($q2) =>
                                  $q2->where('id', $empleado->id)
                              );
                        })
                        ->orderBy('name')
                        ->get();

        return view('empleados.edit', compact('empleado', 'usuarios'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'cedula'   => [
                'required', 'string', 'regex:/^[0-9]{11}$/',
                Rule::unique('empleados', 'cedula')->ignore($empleado->id),
            ],
            'nombre'               => 'required|string|max:150',
            'apellido'             => 'required|string|max:150',
            'telefono'             => ['nullable', 'string', 'regex:/^[0-9]{10}$/'],
            'direccion'            => 'nullable|string|max:255',
            'cargo'                => 'nullable|string|max:100',
            'salario_base'         => 'nullable|numeric|min:0',
            'comision_porcentaje'  => 'nullable|numeric|min:0|max:100',
            'fecha_ingreso'        => 'nullable|date',
            'user_id'              => 'nullable|exists:users,id',
        ], [
            'cedula.required'   => 'La cédula es obligatoria.',
            'cedula.unique'     => 'Esta cédula ya está registrada.',
            'cedula.regex'      => 'La cédula debe tener exactamente 11 dígitos.',
            'nombre.required'   => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'telefono.regex'    => 'El teléfono debe tener exactamente 10 dígitos.',
        ]);

        $empleado->update([
            'user_id'              => $request->user_id,
            'cedula'               => $request->cedula,
            'nombre'               => ucfirst(strtolower(trim($request->nombre))),
            'apellido'             => ucfirst(strtolower(trim($request->apellido))),
            'telefono'             => $request->telefono,
            'direccion'            => $request->direccion,
            'cargo'                => $request->cargo,
            'salario_base'         => $request->salario_base ?? 0,
            'comision_porcentaje'  => $request->comision_porcentaje ?? 0,
            'fecha_ingreso'        => $request->fecha_ingreso,
        ]);

        return redirect()->route('empleados.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Empleado $empleado)
    {
        $empleado->update(['estado' => false]);

        return redirect()->route('empleados.index')
            ->with('success', 'Empleado desactivado correctamente.');
    }

    public function reactivar(Empleado $empleado)
    {
        $empleado->update(['estado' => true]);

        return redirect()->route('empleados.index')
            ->with('success', 'Empleado reactivado correctamente.');
    }
}