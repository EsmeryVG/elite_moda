<?php

namespace App\Http\Controllers;

use App\Http\Requests\VarianteProductoRequest;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Models\Atributo;
use App\Services\ProductoVarianteService;

class VarianteProductoController extends Controller
{
    public function create(Producto $producto)
{
    $atributos = Atributo::with(['valores' => function ($query) {
        $query->where('estado', 1)->orderBy('orden')->orderBy('valor');
    }])
    ->where('estado', 1)
    ->orderBy('nombre')
    ->get();

    return view('variantes.create', compact('producto', 'atributos'));
}

    public function store(VarianteProductoRequest $request, ProductoVarianteService $productoVarianteService)
{
    $producto = Producto::findOrFail($request->producto_id);

    $productoVarianteService->crearVarianteDinamica(
        $producto,
        $request->validated()
    );

    return redirect()->route('productos.show', $producto)
        ->with('success', 'Variante creada correctamente.');
}

public function edit(VarianteProducto $variante)
{
    $variante->load('valores.atributo', 'producto');

    $atributos = Atributo::with(['valores' => function ($query) {
        $query->where('estado', 1)->orderBy('orden')->orderBy('valor');
    }])
    ->where('estado', 1)
    ->orderBy('nombre')
    ->get();

    $valoresSeleccionados = $variante->valores->pluck('id')->toArray();

    return view('variantes.edit', compact('variante', 'atributos', 'valoresSeleccionados'));
}

   public function update(
    VarianteProductoRequest $request,
    VarianteProducto $variante,
    ProductoVarianteService $productoVarianteService
) {
    $productoVarianteService->actualizarVarianteDinamica(
        $variante,
        $request->validated()
    );

    return redirect()->route('productos.show', $variante->producto_id)
        ->with('success', 'Variante actualizada correctamente.');
}

public function index()
{
    $variantes = VarianteProducto::with('producto')
        ->orderBy('id', 'desc')
        ->get();

    return view('variantes.index', compact('variantes'));
}

    public function destroy(VarianteProducto $variante)
    {
        $productoId = $variante->producto_id;

        $variante->delete();

        return redirect()->route('productos.show', $productoId)
            ->with('success', 'Variante eliminada correctamente.');
    }
}
