<?php

namespace App\Http\Controllers;

use App\Models\OrdenCompra;
use App\Models\DetalleOrdenCompra;
use App\Models\Proveedor;
use App\Models\Almacen;
use App\Models\VarianteProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdenCompraController extends Controller
{
    public function index(Request $request)
    {
        $query = OrdenCompra::with(['proveedor', 'almacen'])
                            ->orderBy('created_at', 'desc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('proveedor')) {
            $query->where('proveedor_id', $request->proveedor);
        }

        if ($request->filled('buscar')) {
            $query->where('codigo', 'like', '%' . $request->buscar . '%');
        }

        $ordenes     = $query->paginate(10)->withQueryString();
        $proveedores = Proveedor::activos()->orderBy('nombre')->get();

        if ($request->ajax()) {
            return view('ordenes_compra._tabla', compact('ordenes'))->render();
        }

        return view('ordenes_compra.index', compact('ordenes', 'proveedores'));
    }

    public function create()
    {
        $proveedores = Proveedor::activos()->orderBy('nombre')->get();
        $almacenes   = Almacen::activos()->with('sucursal')->orderBy('nombre')->get();

        return view('ordenes_compra.create', compact('proveedores', 'almacenes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id'   => 'required|exists:proveedores,id',
            'almacen_id'     => 'required|exists:almacenes,id',
            'fecha'          => 'required|date',
            'fecha_esperada' => 'nullable|date|after_or_equal:fecha',
            'observaciones'  => 'nullable|string',
            'lineas'         => 'required|array|min:1',
            'lineas.*.tipo'  => 'required|in:variante,caracteristicas',
            'lineas.*.cantidad_solicitada' => 'required|integer|min:1',
            'lineas.*.precio_unitario'     => 'required|numeric|min:0',
            'lineas.*.variante_id'         => 'required_if:lineas.*.tipo,variante|nullable|exists:variante_productos,id',
            'lineas.*.descripcion'         => 'required_if:lineas.*.tipo,caracteristicas|nullable|string',
        ], [
            'lineas.required'                        => 'Debes agregar al menos una línea.',
            'lineas.*.cantidad_solicitada.required'  => 'La cantidad es obligatoria.',
            'lineas.*.precio_unitario.required'      => 'El precio unitario es obligatorio.',
            'lineas.*.variante_id.required_if'       => 'Debes seleccionar una variante.',
            'lineas.*.descripcion.required_if'       => 'La descripción es obligatoria para líneas por características.',
        ]);

        DB::transaction(function () use ($request) {
            $orden = OrdenCompra::create([
                'proveedor_id'   => $request->proveedor_id,
                'almacen_id'     => $request->almacen_id,
                'usuario_id'     => Auth::id(),
                'fecha'          => $request->fecha,
                'fecha_esperada' => $request->fecha_esperada,
                'observaciones'  => $request->observaciones,
                'estado'         => 'borrador',
                'subtotal'       => 0,
                'impuesto'       => 0,
                'total'          => 0,
            ]);

            $orden->codigo = 'OC-' . str_pad($orden->id, 3, '0', STR_PAD_LEFT);
            $orden->save();

            foreach ($request->lineas as $linea) {
                $subtotal = $linea['cantidad_solicitada'] * $linea['precio_unitario'];

                $orden->detalles()->create([
                    'variante_producto_id'        => $linea['tipo'] === 'variante' ? $linea['variante_id'] : null,
                    'caracteristicas_solicitadas' => $linea['tipo'] === 'caracteristicas'
                        ? ['descripcion' => $linea['descripcion'], 'notas' => $linea['notas'] ?? null]
                        : null,
                    'cantidad_solicitada' => $linea['cantidad_solicitada'],
                    'cantidad_recibida'   => 0,
                    'precio_unitario'     => $linea['precio_unitario'],
                    'subtotal'            => $subtotal,
                ]);
            }

            $orden->recalcularTotales();
        });

        return redirect()->route('ordenes_compra.index')
            ->with('success', 'Orden de compra creada correctamente.');
    }

    public function show(OrdenCompra $ordenes_compra)
    {
        $ordenes_compra->load([
            'proveedor',
            'almacen.sucursal',
            'usuario',
            'detalles.variante.producto',
            'detalles.variante.valores.atributo',
            'recepciones.detalles',
        ]);

        return view('ordenes_compra.show', ['orden' => $ordenes_compra]);
    }

    public function edit(OrdenCompra $ordenes_compra)
    {
        if (!$ordenes_compra->esEditable()) {
            return redirect()->route('ordenes_compra.show', $ordenes_compra)
                ->with('error', 'Esta orden ya no se puede editar.');
        }

        $proveedores = Proveedor::activos()->orderBy('nombre')->get();
        $almacenes   = Almacen::activos()->with('sucursal')->orderBy('nombre')->get();
        $ordenes_compra->load([
            'detalles.variante.producto',
            'detalles.variante.valores.atributo',
        ]);

        return view('ordenes_compra.edit', [
            'orden'       => $ordenes_compra,
            'proveedores' => $proveedores,
            'almacenes'   => $almacenes,
        ]);
    }

    public function update(Request $request, OrdenCompra $ordenes_compra)
    {
        if (!$ordenes_compra->esEditable()) {
            return redirect()->route('ordenes_compra.show', $ordenes_compra)
                ->with('error', 'Esta orden ya no se puede editar.');
        }

        $request->validate([
            'proveedor_id'   => 'required|exists:proveedores,id',
            'almacen_id'     => 'required|exists:almacenes,id',
            'fecha'          => 'required|date',
            'fecha_esperada' => 'nullable|date|after_or_equal:fecha',
            'observaciones'  => 'nullable|string',
            'lineas'         => 'required|array|min:1',
            'lineas.*.tipo'  => 'required|in:variante,caracteristicas',
            'lineas.*.cantidad_solicitada' => 'required|integer|min:1',
            'lineas.*.precio_unitario'     => 'required|numeric|min:0',
            'lineas.*.variante_id'         => 'required_if:lineas.*.tipo,variante|nullable|exists:variante_productos,id',
            'lineas.*.descripcion'         => 'required_if:lineas.*.tipo,caracteristicas|nullable|string',
        ]);

        DB::transaction(function () use ($request, $ordenes_compra) {
            $ordenes_compra->update([
                'proveedor_id'   => $request->proveedor_id,
                'almacen_id'     => $request->almacen_id,
                'fecha'          => $request->fecha,
                'fecha_esperada' => $request->fecha_esperada,
                'observaciones'  => $request->observaciones,
            ]);

            // Eliminar detalles anteriores y recrear
            $ordenes_compra->detalles()->delete();

            foreach ($request->lineas as $linea) {
                $subtotal = $linea['cantidad_solicitada'] * $linea['precio_unitario'];

                $ordenes_compra->detalles()->create([
                    'variante_producto_id'        => $linea['tipo'] === 'variante' ? $linea['variante_id'] : null,
                    'caracteristicas_solicitadas' => $linea['tipo'] === 'caracteristicas'
                        ? ['descripcion' => $linea['descripcion'], 'notas' => $linea['notas'] ?? null]
                        : null,
                    'cantidad_solicitada' => $linea['cantidad_solicitada'],
                    'cantidad_recibida'   => 0,
                    'precio_unitario'     => $linea['precio_unitario'],
                    'subtotal'            => $subtotal,
                ]);
            }

            $ordenes_compra->recalcularTotales();
        });

        return redirect()->route('ordenes_compra.show', $ordenes_compra)
            ->with('success', 'Orden actualizada correctamente.');
    }

    public function enviar(OrdenCompra $ordenes_compra)
    {
        if ($ordenes_compra->estado !== 'borrador') {
            return redirect()->route('ordenes_compra.show', $ordenes_compra)
                ->with('error', 'Solo se pueden enviar órdenes en borrador.');
        }

        $ordenes_compra->update(['estado' => 'enviada']);

        return redirect()->route('ordenes_compra.show', $ordenes_compra)
            ->with('success', 'Orden enviada correctamente.');
    }

    public function cancelar(OrdenCompra $ordenes_compra)
    {
        if (!$ordenes_compra->esCancelable()) {
            return redirect()->route('ordenes_compra.show', $ordenes_compra)
                ->with('error', 'No se puede cancelar esta orden.');
        }

        $ordenes_compra->update(['estado' => 'cancelada']);

        return redirect()->route('ordenes_compra.show', $ordenes_compra)
            ->with('success', 'Orden cancelada.');
    }

    // AJAX — buscar variantes para las líneas
    public function buscarVariantes(Request $request)
    {
        $buscar = $request->get('q', '');

        $variantes = VarianteProducto::with(['producto', 'valores.atributo'])
            ->whereHas('producto', fn($q) =>
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->where('estado', true)
            )
            ->orWhere('codigo', 'like', "%{$buscar}%")
            ->orWhere('codigo_barras', 'like', "%{$buscar}%")
            ->where('estado', true)
            ->limit(10)
            ->get()
            ->map(fn($v) => [
                'id'          => $v->id,
                'texto'       => $v->producto?->nombre . ' — ' .
                    $v->valores->map(fn($val) => $val->atributo?->nombre . ': ' . $val->valor)->join(' / '),
                'codigo'      => $v->codigo,
                'precio'      => $v->precio_venta,
            ]);

        return response()->json($variantes);
    }
}