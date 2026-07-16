<?php

namespace App\Http\Controllers;

use App\Models\CategoriaGasto;
use App\Models\GastoFijo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GastoFijoController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->esAdministrador(), 403);

        $gastosFijos = GastoFijo::with('categoria')->orderBy('nombre')->get();
        $categorias = CategoriaGasto::activas()->orderBy('nombre')->get();

        return view('gastos_fijos.index', compact('gastosFijos', 'categorias'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->esAdministrador(), 403);

        $request->validate([
            'nombre' => 'required|string|max:150',
            'categoria_gasto_id' => 'required|exists:categorias_gasto,id',
            'monto_sugerido' => 'required|numeric|min:0',
        ]);

        GastoFijo::create($request->only('nombre', 'categoria_gasto_id', 'monto_sugerido') + ['estado' => true]);

        return back()->with('success', 'Gasto fijo creado.');
    }

    public function destroy(GastoFijo $gastoFijo)
    {
        abort_unless(Auth::user()->esAdministrador(), 403);

        $gastoFijo->update(['estado' => false]);

        return back()->with('success', 'Gasto fijo desactivado.');
    }
}