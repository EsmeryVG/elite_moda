<?php

namespace App\Http\Controllers;

use App\Models\CategoriaGasto;
use Illuminate\Http\Request;

class CategoriaGastoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100|unique:categorias_gasto,nombre']);

        $categoria = CategoriaGasto::create(['nombre' => $request->nombre, 'estado' => true]);

        if ($request->wantsJson()) {
            return response()->json(['id' => $categoria->id, 'nombre' => $categoria->nombre]);
        }

        return back()->with('success', 'Categoría creada correctamente.');
    }
}