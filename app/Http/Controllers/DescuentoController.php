<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Descuento;
use App\Models\GrupoCliente;
use Illuminate\Http\Request;
use App\Services\DescuentoService;

class DescuentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Descuento::with(['cliente', 'variantes.producto', 'gruposCliente'])
                          ->orderBy('estado', 'desc')
                          ->orderBy('created_at', 'desc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado === 'activos');
        }

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        $descuentos = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('descuentos._tabla', compact('descuentos'))->render();
        }

        return view('descuentos.index', compact('descuentos'));
    }

    public function create()
    {
        $clientes = Cliente::where('estado', true)->where('es_default', false)
                            ->orderBy('nombre')->get();
        $grupos   = GrupoCliente::orderBy('nombre')->get();

        return view('descuentos.create', compact('clientes', 'grupos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'                 => 'required|string|max:150',
            'tipo'                   => 'required|in:porcentaje,monto_fijo',
            'valor'                  => 'required|numeric|min:0',
            'aplica_a'               => 'required|in:producto,cliente,grupo_cliente',
            'cliente_id'             => 'required_if:aplica_a,cliente|nullable|exists:clientes,id',
            'variante_ids'           => 'required_if:aplica_a,producto|nullable|array',
            'variante_ids.*'         => 'exists:variante_productos,id',
            'grupo_ids'              => 'required_if:aplica_a,grupo_cliente|nullable|array',
            'grupo_ids.*'            => 'exists:grupo_clientes,id',
            'fecha_inicio'           => 'nullable|date',
            'fecha_fin'              => 'nullable|date|after_or_equal:fecha_inicio',
            'requiere_autorizacion'  => 'nullable|boolean',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'tipo.required'   => 'El tipo de descuento es obligatorio.',
            'valor.required'  => 'El valor del descuento es obligatorio.',
        ]);

        if ($request->tipo === 'porcentaje' && $request->valor > 100) {
            return back()->withErrors(['valor' => 'El porcentaje no puede ser mayor a 100.'])
                         ->withInput();
        }

        $descuento = Descuento::create([
            'nombre'                => $request->nombre,
            'tipo'                  => $request->tipo,
            'valor'                 => $request->valor,
            'cliente_id'            => $request->aplica_a === 'cliente' ? $request->cliente_id : null,
            'fecha_inicio'          => $request->fecha_inicio,
            'fecha_fin'             => $request->fecha_fin,
            'requiere_autorizacion'=> $request->boolean('requiere_autorizacion'),
            'estado'                => true,
        ]);

        if ($request->aplica_a === 'producto') {
            $descuento->variantes()->sync($request->variante_ids);
        }

        if ($request->aplica_a === 'grupo_cliente') {
            $descuento->gruposCliente()->sync($request->grupo_ids);
        }

        return redirect()->route('descuentos.index')
            ->with('success', 'Descuento creado correctamente.');
    }

    public function show(Descuento $descuento)
    {
        $descuento->load(['cliente', 'variantes.producto', 'gruposCliente']);
        return view('descuentos.show', compact('descuento'));
    }

    public function edit(Descuento $descuento)
    {
        $descuento->load(['variantes', 'gruposCliente']);
        $clientes = Cliente::where('estado', true)->where('es_default', false)
                            ->orderBy('nombre')->get();
        $grupos   = GrupoCliente::orderBy('nombre')->get();

        return view('descuentos.edit', compact('descuento', 'clientes', 'grupos'));
    }

    public function update(Request $request, Descuento $descuento)
    {
        $request->validate([
            'nombre'                 => 'required|string|max:150',
            'tipo'                   => 'required|in:porcentaje,monto_fijo',
            'valor'                  => 'required|numeric|min:0',
            'aplica_a'               => 'required|in:producto,cliente,grupo_cliente',
            'cliente_id'             => 'required_if:aplica_a,cliente|nullable|exists:clientes,id',
            'variante_ids'           => 'required_if:aplica_a,producto|nullable|array',
            'variante_ids.*'         => 'exists:variante_productos,id',
            'grupo_ids'              => 'required_if:aplica_a,grupo_cliente|nullable|array',
            'grupo_ids.*'            => 'exists:grupo_clientes,id',
            'fecha_inicio'           => 'nullable|date',
            'fecha_fin'              => 'nullable|date|after_or_equal:fecha_inicio',
            'requiere_autorizacion'  => 'nullable|boolean',
        ]);

        if ($request->tipo === 'porcentaje' && $request->valor > 100) {
            return back()->withErrors(['valor' => 'El porcentaje no puede ser mayor a 100.'])
                         ->withInput();
        }

        $descuento->update([
            'nombre'                => $request->nombre,
            'tipo'                  => $request->tipo,
            'valor'                 => $request->valor,
            'cliente_id'            => $request->aplica_a === 'cliente' ? $request->cliente_id : null,
            'fecha_inicio'          => $request->fecha_inicio,
            'fecha_fin'             => $request->fecha_fin,
            'requiere_autorizacion'=> $request->boolean('requiere_autorizacion'),
        ]);

        $descuento->variantes()->sync($request->aplica_a === 'producto' ? $request->variante_ids : []);
        $descuento->gruposCliente()->sync($request->aplica_a === 'grupo_cliente' ? $request->grupo_ids : []);

        return redirect()->route('descuentos.index')
            ->with('success', 'Descuento actualizado correctamente.');
    }

    public function destroy(Descuento $descuento)
    {
        $descuento->update(['estado' => false]);

        return redirect()->route('descuentos.index')
            ->with('success', 'Descuento desactivado correctamente.');
    }

    public function reactivar(Descuento $descuento)
    {
        $descuento->update(['estado' => true]);

        return redirect()->route('descuentos.index')
            ->with('success', 'Descuento reactivado correctamente.');
    }

    public function calcular(Request $request)
    {
        $varianteId = $request->get('variante_id');
        $clienteId  = $request->get('cliente_id');
        $precio     = (float) $request->get('precio', 0);

        if (!$varianteId || !$clienteId) {
            return response()->json(['monto' => 0]);
        }

        $variante = \App\Models\VarianteProducto::find($varianteId);
        $cliente  = \App\Models\Cliente::find($clienteId);

        if (!$variante || !$cliente) {
            return response()->json(['monto' => 0]);
        }

        $service   = new \App\Services\DescuentoService();
        $resultado = $service->mejorDescuento($variante, $cliente, $precio);

        return response()->json([
            'monto'   => round($resultado['monto'], 2),
            'nombre'  => $resultado['descuento']?->nombre,
        ]);
    }

}