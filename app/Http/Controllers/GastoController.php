<?php

namespace App\Http\Controllers;

use App\Models\CategoriaGasto;
use App\Models\Gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GastoFijo;

class GastoController extends Controller
{
    public function index(Request $request)
    {
        $gastos = $this->construirQuery($request)->paginate(15);
        $categorias = CategoriaGasto::activas()->orderBy('nombre')->get();

        $gastosFijos = GastoFijo::activos()->with('categoria')->get()->map(function ($gf) {
        $pagado = $gf->gastoDelMesActual();
        return [
            'id' => $gf->id,
            'nombre' => $gf->nombre,
            'categoria' => $gf->categoria?->nombre,
            'monto_sugerido' => $gf->monto_sugerido,
            'pagado' => $pagado !== null,
            'fecha_pago' => $pagado?->fecha->format('d/m/Y'),
        ];
    });

        return view('gastos.index', compact('gastos', 'categorias', 'gastosFijos'));
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

    public function registrarGastoFijo(Request $request, \App\Models\GastoFijo $gastoFijo)
{
    $request->validate(['monto' => 'required|numeric|min:0.01']);

    $periodo = now()->format('Y-m');
    if ($gastoFijo->gastos()->where('periodo', $periodo)->exists()) {
        return back()->withErrors(['monto' => 'Este gasto fijo ya fue registrado este mes.']);
    }

    Gasto::create([
        'categoria_gasto_id' => $gastoFijo->categoria_gasto_id,
        'gasto_fijo_id' => $gastoFijo->id,
        'origen' => 'directo',
        'nombre' => $gastoFijo->nombre,
        'monto' => $request->monto,
        'usuario_id' => Auth::id(),
        'fecha' => now(),
        'periodo' => $periodo,
    ]);

    return back()->with('success', 'Gasto fijo registrado.');
}
}