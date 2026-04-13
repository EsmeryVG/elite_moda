<?php

namespace App\Http\Controllers;

use App\Http\Requests\VarianteProductoRequest;
use App\Models\Producto;
use App\Models\VarianteProducto;

class VarianteProductoController extends Controller
{
    public function create(Producto $producto)
    {
        return view('variantes.create', compact('producto'));
    }

    public function store(VarianteProductoRequest $request)
    {
        VarianteProducto::create($request->validated());

        return redirect()->route('productos.show', $request->producto_id)
            ->with('success', 'Variante creada correctamente.');
    }

    public function edit(VarianteProducto $variante)
    {
        return view('variantes.edit', compact('variante'));
    }

    public function update(VarianteProductoRequest $request, VarianteProducto $variante)
    {
        $variante->update($request->validated());

        return redirect()->route('productos.show', $variante->producto_id)
            ->with('success', 'Variante actualizada correctamente.');
    }

    public function destroy(VarianteProducto $variante)
    {
        $productoId = $variante->producto_id;

        $variante->delete();

        return redirect()->route('productos.show', $productoId)
            ->with('success', 'Variante eliminada correctamente.');
    }
}
