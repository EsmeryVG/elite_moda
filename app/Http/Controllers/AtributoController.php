<?php

namespace App\Http\Controllers;

use App\Models\Atributo;
use App\Models\AtributoValor;
use Illuminate\Http\Request;

class AtributoController extends Controller
{
    public function index(Request $request)
    {
        $query = Atributo::withCount(['valores' => function ($q) {
            $q->where('estado', true);
        }])->orderBy('estado', 'desc')
           ->orderBy('nombre', 'asc');

        if ($request->filled('estado')) {
            if ($request->estado === 'activos') {
                $query->where('estado', true);
            } elseif ($request->estado === 'inactivos') {
                $query->where('estado', false);
            }
        }

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        $atributos = $query->paginate(8)->withQueryString();

        if ($request->ajax()) {
            return view('atributos._tabla', compact('atributos'))->render();
        }

        return view('atributos.index', compact('atributos'));
    }

    public function show(Atributo $atributo)
    {
        $atributo->load(['valores' => function ($q) {
            $q->orderBy('orden')->orderBy('valor');
        }]);

        return view('atributos.show', compact('atributo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => [
                'required', 'string', 'max:100',
                \Illuminate\Validation\Rule::unique('atributos', 'nombre'),
            ],
        ], [
            'nombre.required' => 'El nombre del atributo es obligatorio.',
            'nombre.unique'   => 'Ya existe un atributo con ese nombre.',
        ]);

        $atributo = Atributo::create([
            'nombre' => ucfirst(strtolower(trim($request->nombre))),
            'estado' => true,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id'     => $atributo->id,
                'nombre' => $atributo->nombre,
            ]);
        }

        return redirect()->route('atributos.index')
            ->with('success', 'Atributo creado correctamente.');
    }

    public function update(Request $request, Atributo $atributo)
    {
        $request->validate([
            'nombre' => [
                'required', 'string', 'max:100',
                \Illuminate\Validation\Rule::unique('atributos', 'nombre')->ignore($atributo->id),
            ],
        ], [
            'nombre.required' => 'El nombre del atributo es obligatorio.',
            'nombre.unique'   => 'Ya existe un atributo con ese nombre.',
        ]);

        $atributo->update([
            'nombre' => ucfirst(strtolower(trim($request->nombre))),
        ]);

        return redirect()->route('atributos.show', $atributo)
            ->with('success', 'Atributo actualizado correctamente.');
    }

    public function destroy(Atributo $atributo)
    {
        $atributo->update(['estado' => false]);

        return redirect()->route('atributos.index')
            ->with('success', 'Atributo desactivado correctamente.');
    }

    public function reactivar(Atributo $atributo)
    {
        $atributo->update(['estado' => true]);

        return redirect()->route('atributos.index')
            ->with('success', 'Atributo reactivado correctamente.');
    }

    // ── Valores ─────────────────────────────────────
    public function storeValor(Request $request, Atributo $atributo)
    {
        $request->validate([
            'valor' => [
                'required', 'string', 'max:100',
                \Illuminate\Validation\Rule::unique('atributo_valores', 'valor')
                    ->where('atributo_id', $atributo->id),
            ],
            'orden' => 'nullable|integer|min:0',
        ], [
            'valor.required' => 'El valor es obligatorio.',
            'valor.unique'   => 'Ya existe ese valor para este atributo.',
        ]);

        $valor = $atributo->valores()->create([
            'valor'  => ucfirst(strtolower(trim($request->valor))),
            'orden'  => $request->orden ?? 0,
            'estado' => true,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id'    => $valor->id,
                'valor' => $valor->valor,
            ]);
        }

        return redirect()->route('atributos.show', $atributo)
            ->with('success', 'Valor agregado correctamente.');
    }

    public function updateValor(Request $request, Atributo $atributo, AtributoValor $valor)
    {
        $request->validate([
            'valor' => [
                'required', 'string', 'max:100',
                \Illuminate\Validation\Rule::unique('atributo_valores', 'valor')
                    ->where('atributo_id', $atributo->id)
                    ->ignore($valor->id),
            ],
            'orden' => 'nullable|integer|min:0',
        ], [
            'valor.required' => 'El valor es obligatorio.',
            'valor.unique'   => 'Ya existe ese valor para este atributo.',
        ]);

        $valor->update([
            'valor' => ucfirst(strtolower(trim($request->valor))),
            'orden' => $request->orden ?? $valor->orden,
        ]);

        return redirect()->route('atributos.show', $atributo)
            ->with('success', 'Valor actualizado correctamente.');
    }

    public function destroyValor(Atributo $atributo, AtributoValor $valor)
    {
        $valor->update(['estado' => false]);

        return redirect()->route('atributos.show', $atributo)
            ->with('success', 'Valor desactivado correctamente.');
    }

    public function reactivarValor(Atributo $atributo, AtributoValor $valor)
    {
        $valor->update(['estado' => true]);

        return redirect()->route('atributos.show', $atributo)
            ->with('success', 'Valor reactivado correctamente.');
    }
}