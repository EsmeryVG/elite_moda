<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use App\Models\GrupoCliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::with('grupo')
                        ->where('es_default', false)
                        ->orderBy('estado', 'desc')
                        ->orderBy('nombre', 'asc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado === 'activos');
        }

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('apellido', 'like', '%' . $request->buscar . '%')
                  ->orWhere('cedula', 'like', '%' . $request->buscar . '%')
                  ->orWhere('codigo', 'like', '%' . $request->buscar . '%')
                  ->orWhere('email', 'like', '%' . $request->buscar . '%');
            });
        }

        if ($request->filled('grupo')) {
            $query->where('grupo_cliente_id', $request->grupo);
        }

        $clientes = $query->paginate(10)->withQueryString();
        $grupos   = GrupoCliente::orderBy('nombre')->get();

        if ($request->ajax()) {
            return view('clientes._tabla', compact('clientes'))->render();
        }

        return view('clientes.index', compact('clientes', 'grupos'));
    }

    public function create()
    {
        $grupos = GrupoCliente::orderBy('nombre')->get();
        return view('clientes.create', compact('grupos'));
    }

    public function store(ClienteRequest $request)
    {
        $data = $request->validated();
        $data['nombre']   = ucfirst(strtolower(trim($data['nombre'])));
        $data['apellido'] = isset($data['apellido'])
            ? ucfirst(strtolower(trim($data['apellido'])))
            : null;
        $data['credito_activo'] = $request->boolean('credito_activo');

        $cliente = Cliente::create($data);
        $cliente->codigo = 'CLI-' . str_pad($cliente->id, 3, '0', STR_PAD_LEFT);
        $cliente->save();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load('grupo', 'ventas', 'cuentasPorCobrar');
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        $grupos = GrupoCliente::orderBy('nombre')->get();
        return view('clientes.edit', compact('cliente', 'grupos'));
    }

    public function update(ClienteRequest $request, Cliente $cliente)
    {
        $data = $request->validated();
        $data['nombre']   = ucfirst(strtolower(trim($data['nombre'])));
        $data['apellido'] = isset($data['apellido'])
            ? ucfirst(strtolower(trim($data['apellido'])))
            : null;
        $data['credito_activo'] = $request->boolean('credito_activo');

        $cliente->update($data);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        if ($cliente->es_default) {
            return redirect()->route('clientes.index')
                ->with('error', 'El cliente por defecto no puede ser desactivado.');
        }

        if ($cliente->ventas()->exists()) {
            return redirect()->route('clientes.index')
                ->with('error', 'No puedes desactivar este cliente porque tiene ventas registradas.');
        }

        if ($cliente->cuentasPorCobrar()->where('estado', '!=', 'pagada')->exists()) {
            return redirect()->route('clientes.index')
                ->with('error', 'No puedes desactivar este cliente porque tiene crédito pendiente.');
        }

        $cliente->update(['estado' => false]);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente desactivado correctamente.');
    }

    public function reactivar(Cliente $cliente)
    {
        $cliente->update(['estado' => true]);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente reactivado correctamente.');
    }
}