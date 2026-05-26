<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Services\ProductoVarianteService;
use Illuminate\Http\Request;

class VarianteProductoController extends Controller
{
    public function store(Request $request, ProductoVarianteService $service)
    {
        $request->validate([
            'producto_id'              => 'required|exists:productos,id',
            'atributo_valor_ids'       => 'required|array|min:1',
            'atributo_valor_ids.*'     => 'required|exists:atributo_valores,id',
            'precio_venta'             => 'required|numeric|min:0',
            'costo'                    => 'nullable|numeric|min:0',
            'codigo_barras'            => 'nullable|string|max:100',
        ], [
            'atributo_valor_ids.required' => 'Debes seleccionar al menos un valor.',
            'precio_venta.required'       => 'El precio de venta es obligatorio.',
        ]);

        $producto = Producto::findOrFail($request->producto_id);

        $service->crearVarianteDinamica($producto, $request->all());

        return redirect()->route('productos.show', $producto)
            ->with('success', 'Variante agregada correctamente.');
    }

    public function update(Request $request, VarianteProducto $variante, ProductoVarianteService $service)
    {
        $request->validate([
            'atributo_valor_ids'       => 'nullable|array',
            'atributo_valor_ids.*'     => 'exists:atributo_valores,id',
            'precio_venta'             => 'required|numeric|min:0',
            'costo'                    => 'nullable|numeric|min:0',
            'codigo_barras'            => 'nullable|string|max:100',
            'descuento_maximo'         => 'nullable|numeric|min:0|max:100',
        ], [
            'precio_venta.required' => 'El precio de venta es obligatorio.',
        ]);

        $service->actualizarVarianteDinamica($variante, $request->all());

        return redirect()->route('productos.show', $variante->producto_id)
            ->with('success', 'Variante actualizada correctamente.');
    }

    public function desactivar(VarianteProducto $variante)
    {
        $variante->update(['estado' => false]);

        return redirect()->route('productos.show', $variante->producto_id)
            ->with('success', 'Variante desactivada correctamente.');
    }

    public function reactivar(VarianteProducto $variante)
    {
        $variante->update(['estado' => true]);

        return redirect()->route('productos.show', $variante->producto_id)
            ->with('success', 'Variante reactivada correctamente.');
    }

    public function destroy(VarianteProducto $variante)
    {
        $productoId = $variante->producto_id;
        $variante->delete();

        return redirect()->route('productos.show', $productoId)
            ->with('success', 'Variante eliminada correctamente.');
    }

    public function editarForm(VarianteProducto $variante)
{
    $variante->load('valores.atributo', 'producto');

    $atributos = Atributo::activos()
        ->with(['valores' => fn ($q) =>
            $q->where('estado', true)->orderBy('orden')->orderBy('valor')
        ])
        ->orderBy('nombre')
        ->get();

    $valoresSeleccionados = $variante->valores->pluck('id')->toArray();

    return view('variantes.edit', compact('variante', 'atributos', 'valoresSeleccionados'));
}
}