<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('rol')->orderBy('estado', 'desc')->orderBy('name');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado === 'activos');
        }

        if ($request->filled('rol')) {
            $query->where('rol_id', $request->rol);
        }

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->buscar . '%')
                  ->orWhere('email', 'like', '%' . $request->buscar . '%');
            });
        }

        $usuarios = $query->paginate(10)->withQueryString();
        $roles    = Rol::orderBy('nombre')->get();

        if ($request->ajax()) {
            return view('usuarios._tabla', compact('usuarios'))->render();
        }

        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    public function create()
    {
        $roles = Rol::orderBy('nombre')->get();
        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'rol_id'   => 'nullable|exists:roles,id',
        ], [
            'name.required'      => 'El nombre es obligatorio.',
            'email.required'     => 'El email es obligatorio.',
            'email.unique'       => 'Este email ya está registrado.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        User::create([
            'name'     => ucfirst(trim($request->name)),
            'email'    => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'rol_id'   => $request->rol_id,
            'estado'   => true,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        $roles = Rol::orderBy('nombre')->get();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'   => 'required|string|max:150',
            'email'  => [
                'required', 'email',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            'rol_id' => 'nullable|exists:roles,id',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required'  => 'El nombre es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.unique'   => 'Este email ya está registrado.',
            'password.min'   => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $data = [
            'name'   => ucfirst(trim($request->name)),
            'email'  => strtolower(trim($request->email)),
            'rol_id' => $request->rol_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        $usuario->update(['estado' => false]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario desactivado correctamente.');
    }

    public function reactivar(User $usuario)
    {
        $usuario->update(['estado' => true]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario reactivado correctamente.');
    }
}