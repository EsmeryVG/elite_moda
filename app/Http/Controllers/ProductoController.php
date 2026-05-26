<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoRequest;
use App\Models\Atributo;
use App\Models\Categoria;
use App\Models\Producto;
use App\Services\ProductoVarianteService;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'variantes'])
            ->orderBy('estado', 'desc')
            ->orderBy('nombre', 'asc');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('codigo', 'like', "%{$buscar}%")
                  ->orWhere('marca', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado === 'activos');
        }

        $productos   = $query->paginate(10)->withQueryString();
        $categorias  = Categoria::activas()->orderBy('nombre')->get();

        if ($request->ajax()) {
            return view('productos._tabla', compact('productos'))->render();
        }

        return view('productos.index', compact('productos', 'categorias'));
    }

    public function create()
    {
        $categorias = Categoria::activas()->orderBy('nombre')->get();
        $atributos  = Atributo::activos()
            ->with(['valores' => fn ($q) => $q->where('estado', true)->orderBy('orden')->orderBy('valor')])
            ->orderBy('nombre')
            ->get();

        $marcas = Producto::whereNotNull('marca')
            ->where('marca', '!=', '')
            ->distinct()
            ->orderBy('marca')
            ->pluck('marca');

        return view('productos.create', compact('categorias', 'atributos', 'marcas'));
    }

    public function store(ProductoRequest $request, ProductoVarianteService $service)
    {
        $data = $request->validated();

        // Normalizar marca
        if (($data['marca'] ?? null) === '__otra__') {
            $data['marca'] = !empty($data['nueva_marca'])
                ? trim(ucwords(strtolower($data['nueva_marca'])))
                : null;
        } else {
            $data['marca'] = !empty($data['marca'])
                ? trim(ucwords(strtolower($data['marca'])))
                : null;
        }

        $tieneVariantes = !empty($data['variantes']) && count($data['variantes']) > 0;

        $producto = Producto::create([
            'categoria_id'    => $data['categoria_id'],
            'nombre'          => trim($data['nombre']),
            'marca'           => $data['marca'],
            'descripcion'     => !empty($data['descripcion']) ? trim($data['descripcion']) : null,
            'tiene_variantes' => $tieneVariantes,
            'estado'          => true,
        ]);

        $producto->codigo = 'PROD-' . str_pad($producto->id, 3, '0', STR_PAD_LEFT);
        $producto->save();

        if ($tieneVariantes) {
            // Producto con variantes — crear desde la grilla
            $service->crearVariantesMasivo($producto, $data['variantes']);
        } else {
            // Producto simple — crear variante default
            $service->crearVarianteDefault($producto, [
                'costo'        => $data['costo_simple'] ?? null,
                'precio_venta' => $data['precio_simple'],
            ]);
        }

        return redirect()->route('productos.show', $producto)
            ->with('success', 'Producto creado correctamente.');
    }

public function show(Producto $producto)
{
    $producto->load([
        'categoria',
        'variantes' => fn ($q) => $q->orderBy('es_default', 'desc')->orderBy('id'),
        'variantes.valores.atributo',
    ]);

    return view('productos.show', compact('producto'));
}

public function edit(Producto $producto)
{
    $producto->load([
        'categoria',
        'variantes' => fn ($q) => $q->orderBy('es_default', 'desc')->orderBy('id'),
        'variantes.valores.atributo',
    ]);

    $atributos = Atributo::activos()
        ->with(['valores' => fn ($q) =>
            $q->where('estado', true)->orderBy('orden')->orderBy('valor')
        ])
        ->orderBy('nombre')
        ->get();

    $categorias = \App\Models\Categoria::activas()->orderBy('nombre')->get();

    $marcas = \App\Models\Producto::whereNotNull('marca')
        ->where('marca', '!=', '')
        ->distinct()
        ->orderBy('marca')
        ->pluck('marca');

    return view('productos.edit', compact('producto', 'atributos', 'categorias', 'marcas'));
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

    public function desactivar(Producto $producto)
    {
        $producto->update(['estado' => false]);
        $producto->variantes()->update(['estado' => false]);

        return redirect()->route('productos.index')
            ->with('success', 'Producto y sus variantes desactivados correctamente.');
    }

    public function reactivar(Producto $producto)
    {
        $producto->update(['estado' => true]);
        $producto->variantes()->update(['estado' => true]);

        return redirect()->route('productos.index')
            ->with('success', 'Producto y sus variantes reactivados correctamente.');
    }
}