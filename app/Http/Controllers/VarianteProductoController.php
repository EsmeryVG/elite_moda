<?php

namespace App\Http\Controllers;

use App\Http\Requests\VarianteProductoRequest;
use App\Models\Producto;
use App\Models\VarianteProducto;

class VarianteProductoController extends Controller
{
    public function create(Producto $producto)
{
    $colores = VarianteProducto::whereNotNull('color')
        ->where('color', '!=', '')
        ->select('color')
        ->distinct()
        ->orderBy('color')
        ->pluck('color');

    $tallas = VarianteProducto::whereNotNull('talla')
        ->where('talla', '!=', '')
        ->select('talla')
        ->distinct()
        ->orderBy('talla')
        ->pluck('talla');

    $materiales = VarianteProducto::whereNotNull('material')
        ->where('material', '!=', '')
        ->select('material')
        ->distinct()
        ->orderBy('material')
        ->pluck('material');

    return view('variantes.create', compact('producto', 'colores', 'tallas', 'materiales'));
}

    public function store(VarianteProductoRequest $request)
{
    $data = $request->validated();

    $data['descripcion'] = isset($data['descripcion']) ? trim($data['descripcion']) : null;

    if (($data['color'] ?? null) === '__otra__') {
        $data['color'] = !empty($data['nuevo_color'])
            ? trim(ucwords(strtolower($data['nuevo_color'])))
            : null;
    } else {
        $data['color'] = !empty($data['color'])
            ? trim(ucwords(strtolower($data['color'])))
            : null;
    }

    if (($data['talla'] ?? null) === '__otra__') {
        $data['talla'] = !empty($data['nueva_talla'])
            ? trim(strtoupper($data['nueva_talla']))
            : null;
    } else {
        $data['talla'] = !empty($data['talla'])
            ? trim(strtoupper($data['talla']))
            : null;
    }

    if (($data['material'] ?? null) === '__otra__') {
        $data['material'] = !empty($data['nuevo_material'])
            ? trim(ucwords(strtolower($data['nuevo_material'])))
            : null;
    } else {
        $data['material'] = !empty($data['material'])
            ? trim(ucwords(strtolower($data['material'])))
            : null;
    }

    unset($data['nuevo_color'], $data['nueva_talla'], $data['nuevo_material']);

    $variante = VarianteProducto::create($data);

    $variante->codigo = 'VAR-' . str_pad($variante->id, 3, '0', STR_PAD_LEFT);
    $variante->save();

    return redirect()->route('productos.show', $request->producto_id)
        ->with('success', 'Variante creada correctamente.');
}

    public function edit(VarianteProducto $variante)
{
    $colores = VarianteProducto::whereNotNull('color')
        ->where('color', '!=', '')
        ->select('color')
        ->distinct()
        ->orderBy('color')
        ->pluck('color');

    $tallas = VarianteProducto::whereNotNull('talla')
        ->where('talla', '!=', '')
        ->select('talla')
        ->distinct()
        ->orderBy('talla')
        ->pluck('talla');

    $materiales = VarianteProducto::whereNotNull('material')
        ->where('material', '!=', '')
        ->select('material')
        ->distinct()
        ->orderBy('material')
        ->pluck('material');

    return view('variantes.edit', compact('variante', 'colores', 'tallas', 'materiales'));
}

   public function update(VarianteProductoRequest $request, VarianteProducto $variante)
{
    $data = $request->validated();

    $data['descripcion'] = isset($data['descripcion']) ? trim($data['descripcion']) : null;

    if (($data['color'] ?? null) === '__otra__') {
        $data['color'] = !empty($data['nuevo_color'])
            ? trim(ucwords(strtolower($data['nuevo_color'])))
            : null;
    } else {
        $data['color'] = !empty($data['color'])
            ? trim(ucwords(strtolower($data['color'])))
            : null;
    }

    if (($data['talla'] ?? null) === '__otra__') {
        $data['talla'] = !empty($data['nueva_talla'])
            ? trim(strtoupper($data['nueva_talla']))
            : null;
    } else {
        $data['talla'] = !empty($data['talla'])
            ? trim(strtoupper($data['talla']))
            : null;
    }

    if (($data['material'] ?? null) === '__otra__') {
        $data['material'] = !empty($data['nuevo_material'])
            ? trim(ucwords(strtolower($data['nuevo_material'])))
            : null;
    } else {
        $data['material'] = !empty($data['material'])
            ? trim(ucwords(strtolower($data['material'])))
            : null;
    }

    unset($data['nuevo_color'], $data['nueva_talla'], $data['nuevo_material']);

    $variante->update($data);

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
