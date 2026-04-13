<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categoria')->orderBy('id', 'desc')->get();
        return view('productos.index', compact('productos'));
    }

  public function create()
{
    $categorias = Categoria::where('estado', 1)->orderBy('nombre')->get();

    $marcas = Producto::whereNotNull('marca')
        ->where('marca', '!=', '')
        ->select('marca')
        ->distinct()
        ->orderBy('marca')
        ->pluck('marca');

    return view('productos.create', compact('categorias', 'marcas'));
}

  public function store(ProductoRequest $request)
{
    $data = $request->validated();

    $data['nombre'] = trim($data['nombre']);
    $data['descripcion'] = isset($data['descripcion']) ? trim($data['descripcion']) : null;

    if (($data['marca'] ?? null) === '__otra__') {
        $data['marca'] = !empty($data['nueva_marca'])
            ? trim(ucwords(strtolower($data['nueva_marca'])))
            : null;
    } else {
        $data['marca'] = !empty($data['marca'])
            ? trim(ucwords(strtolower($data['marca'])))
            : null;
    }

    unset($data['nueva_marca']);

    $producto = Producto::create($data);

    $producto->codigo = 'PROD-' . str_pad($producto->id, 3, '0', STR_PAD_LEFT);
    $producto->save();

    return redirect()->route('productos.index')
        ->with('success', 'Producto creado correctamente.');
}

    public function show(Producto $producto)
    {
        $producto->load('categoria');
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
{
    $categorias = Categoria::where('estado', 1)->orderBy('nombre')->get();

    $marcas = Producto::whereNotNull('marca')
        ->where('marca', '!=', '')
        ->select('marca')
        ->distinct()
        ->orderBy('marca')
        ->pluck('marca');

    return view('productos.edit', compact('producto', 'categorias', 'marcas'));
}

    public function update(ProductoRequest $request, Producto $producto)
{
    $data = $request->validated();

    $data['nombre'] = trim($data['nombre']);
    $data['descripcion'] = isset($data['descripcion']) ? trim($data['descripcion']) : null;

    if (($data['marca'] ?? null) === '__otra__') {
        $data['marca'] = !empty($data['nueva_marca'])
            ? trim(ucwords(strtolower($data['nueva_marca'])))
            : null;
    } else {
        $data['marca'] = !empty($data['marca'])
            ? trim(ucwords(strtolower($data['marca'])))
            : null;
    }

    unset($data['nueva_marca']);

    $producto->update($data);

    return redirect()->route('productos.index')
        ->with('success', 'Producto actualizado correctamente.');
}

    public function destroy(Producto $producto)
{
    if ($producto->variantes()->exists()) {
        return redirect()->route('productos.index')
            ->with('error', 'No puedes eliminar este producto porque tiene variantes asociadas.');
    }

    $producto->delete();

    return redirect()->route('productos.index')
        ->with('success', 'Producto eliminado correctamente.');
}
}