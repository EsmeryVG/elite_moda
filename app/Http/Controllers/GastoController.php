<?php

namespace App\Http\Controllers;

use App\Models\CategoriaGasto;
use App\Models\Gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GastoController extends Controller
{
    public function index(Request $request)
    {
        $gastos = $this->construirQuery($request)->paginate(15);
        $categorias = CategoriaGasto::activas()->orderBy('nombre')->get();

        return view('gastos.index', compact('gastos', 'categorias'));
    }

    public function tabla(Request $request)
    {
        $gastos = $this->construirQuery($request)->paginate(15);

        return view('gastos._tabla', compact('gastos'));
    }

    private function construirQuery(Request $request)
    {
        $query = Gasto::with('categoria', 'usuario');

        if ($request->filled('origen')) {
            $query->where('origen', $request->origen);
        }

        if ($request->filled('categoria_gasto_id')) {
            $query->where('categoria_gasto_id', $request->categoria_gasto_id);
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }

        return $query->orderByDesc('fecha');
    }

    public function create()
    {
        $categorias = CategoriaGasto::activas()->orderBy('nombre')->get();

        return view('gastos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'monto' => 'required|numeric|min:0.01',
            'categoria_gasto_id' => 'required|exists:categorias_gasto,id',
            'metodo_pago' => 'required|string|max:50',
            'referencia' => 'nullable|string|max:100',
        ]);

        Gasto::create([
            'categoria_gasto_id' => $request->categoria_gasto_id,
            'origen' => 'directo',
            'nombre' => $request->nombre,
            'monto' => $request->monto,
            'usuario_id' => Auth::id(),
            'fecha' => now(),
            'metodo_pago' => $request->metodo_pago,
            'referencia' => $request->referencia,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('gastos.index')->with('success', 'Gasto registrado correctamente.');
    }
}