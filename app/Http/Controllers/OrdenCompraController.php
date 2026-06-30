<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use App\Models\DetalleOrdenCompra;
use App\Models\OrdenCompra;
use App\Models\Almacen;
use App\Models\Proveedor;
use App\Models\VarianteProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdenCompraController extends Controller
{
    public function index(Request $request)
    {
        $query = OrdenCompra::with(['proveedor', 'almacen', 'usuario'])
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
        $proveedores = Proveedor::where('estado', true)->orderBy('nombre')->get();

        if ($request->ajax()) {
            return view('ordenes_compra._tabla', compact('ordenes'))->render();
        }

        return view('ordenes_compra.index', compact('ordenes', 'proveedores'));
    }

    public function create()
    {
        $proveedores     = Proveedor::where('estado', true)->orderBy('nombre')->get();
        $almacenes       = Almacen::where('estado', true)->with('sucursal')->orderBy('nombre')->get();
        $itbisPorcentaje = (float) Configuracion::get('itbis_porcentaje', 18);

        return view('ordenes_compra.create', compact('proveedores', 'almacenes', 'itbisPorcentaje'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id'                  => 'required|exists:proveedores,id',
            'almacen_id'                    => 'required|exists:almacenes,id',
            'numero_factura'                 => 'nullable|string|max:50',
            'fecha'                          => 'required|date',
            'fecha_esperada'                  => 'nullable|date|after_or_equal:fecha',
            'observaciones'                  => 'nullable|string',
            'lineas'                         => 'required|array|min:1',
            'lineas.*.variante_id'            => 'required|exists:variante_productos,id',
            'lineas.*.cantidad_solicitada'    => 'required|integer|min:1',
            'lineas.*.precio_unitario'        => 'required|numeric|min:0',
            'lineas.*.itbis_incluido'         => 'nullable|boolean',
        ], [
            'proveedor_id.required'                  => 'El proveedor es obligatorio.',
            'almacen_id.required'                    => 'El almacén es obligatorio.',
            'fecha.required'                         => 'La fecha es obligatoria.',
            'lineas.required'                        => 'Debes agregar al menos una línea.',
            'lineas.*.cantidad_solicitada.required'  => 'La cantidad es obligatoria.',
            'lineas.*.cantidad_solicitada.min'       => 'La cantidad debe ser al menos 1.',
            'lineas.*.precio_unitario.required'      => 'El precio unitario es obligatorio.',
        ]);

        $itbisPorcentaje = (float) Configuracion::get('itbis_porcentaje', 18);

        DB::transaction(function () use ($request, $itbisPorcentaje) {
            $subtotalOrden = 0;
            $impuestoOrden = 0;

            $orden = OrdenCompra::create([
                'proveedor_id'   => $request->proveedor_id,
                'almacen_id'     => $request->almacen_id,
                'usuario_id'     => Auth::id() ?? 1,
                'numero_factura' => $request->numero_factura,
                'fecha'          => $request->fecha,
                'fecha_esperada' => $request->fecha_esperada,
                'estado'         => 'borrador',
                'observaciones'  => $request->observaciones,
            ]);

            $orden->codigo = 'OC-' . str_pad($orden->id, 3, '0', STR_PAD_LEFT);
            $orden->save();

            foreach ($request->lineas as $linea) {
                $itbisIncluido  = !empty($linea['itbis_incluido']);
                $precioUnitario = (float) $linea['precio_unitario'];
                $cantidad       = (int) $linea['cantidad_solicitada'];

                $lineaSubtotal = $cantidad * $precioUnitario;

                $itbisLinea = 0;
                if ($itbisIncluido) {
                    $precioBase = $precioUnitario / (1 + $itbisPorcentaje / 100);
                    $itbisLinea = ($precioUnitario - $precioBase) * $cantidad;
                }

                $subtotalOrden += $lineaSubtotal - $itbisLinea;
                $impuestoOrden += $itbisLinea;

                DetalleOrdenCompra::create([
                    'orden_compra_id'      => $orden->id,
                    'variante_producto_id' => $linea['variante_id'],
                    'cantidad_solicitada'  => $cantidad,
                    'cantidad_recibida'    => 0,
                    'precio_unitario'      => $precioUnitario,
                    'itbis_incluido'       => $itbisIncluido,
                    'itbis_porcentaje'     => $itbisPorcentaje,
                    'subtotal'             => round($lineaSubtotal, 2),
                ]);
            }

            $orden->update([
                'subtotal' => round($subtotalOrden, 2),
                'impuesto' => round($impuestoOrden, 2),
                'total'    => round($subtotalOrden + $impuestoOrden, 2),
            ]);
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
            'recepciones.usuario',
        ]);

        return view('ordenes_compra.show', compact('ordenes_compra'));
    }

    public function edit(OrdenCompra $ordenes_compra)
    {
        if ($ordenes_compra->estado !== 'borrador') {
            return redirect()->route('ordenes_compra.show', $ordenes_compra)
                ->with('error', 'Solo se pueden editar órdenes en borrador.');
        }

        $ordenes_compra->load([
            'detalles.variante.producto',
            'detalles.variante.valores.atributo',
        ]);

        $proveedores     = Proveedor::where('estado', true)->orderBy('nombre')->get();
        $almacenes       = Almacen::where('estado', true)->with('sucursal')->orderBy('nombre')->get();
        $itbisPorcentaje = (float) Configuracion::get('itbis_porcentaje', 18);

        return view('ordenes_compra.edit', compact('ordenes_compra', 'proveedores', 'almacenes', 'itbisPorcentaje'));
    }

    public function update(Request $request, OrdenCompra $ordenes_compra)
    {
        if ($ordenes_compra->estado !== 'borrador') {
            return redirect()->route('ordenes_compra.show', $ordenes_compra)
                ->with('error', 'Solo se pueden editar órdenes en borrador.');
        }

        $request->validate([
            'proveedor_id'                  => 'required|exists:proveedores,id',
            'almacen_id'                    => 'required|exists:almacenes,id',
            'numero_factura'                 => 'nullable|string|max:50',
            'fecha'                          => 'required|date',
            'fecha_esperada'                  => 'nullable|date|after_or_equal:fecha',
            'observaciones'                  => 'nullable|string',
            'lineas'                         => 'required|array|min:1',
            'lineas.*.variante_id'            => 'required|exists:variante_productos,id',
            'lineas.*.cantidad_solicitada'    => 'required|integer|min:1',
            'lineas.*.precio_unitario'        => 'required|numeric|min:0',
            'lineas.*.itbis_incluido'         => 'nullable|boolean',
        ]);

        $itbisPorcentaje = (float) Configuracion::get('itbis_porcentaje', 18);

        DB::transaction(function () use ($request, $ordenes_compra, $itbisPorcentaje) {
            $subtotalOrden = 0;
            $impuestoOrden = 0;

            $ordenes_compra->update([
                'proveedor_id'   => $request->proveedor_id,
                'almacen_id'     => $request->almacen_id,
                'numero_factura' => $request->numero_factura,
                'fecha'          => $request->fecha,
                'fecha_esperada' => $request->fecha_esperada,
                'observaciones'  => $request->observaciones,
            ]);

            $ordenes_compra->detalles()->delete();

            foreach ($request->lineas as $linea) {
                $itbisIncluido  = !empty($linea['itbis_incluido']);
                $precioUnitario = (float) $linea['precio_unitario'];
                $cantidad       = (int) $linea['cantidad_solicitada'];

                $lineaSubtotal = $cantidad * $precioUnitario;

                $itbisLinea = 0;
                if ($itbisIncluido) {
                    $precioBase = $precioUnitario / (1 + $itbisPorcentaje / 100);
                    $itbisLinea = ($precioUnitario - $precioBase) * $cantidad;
                }

                $subtotalOrden += $lineaSubtotal - $itbisLinea;
                $impuestoOrden += $itbisLinea;

                DetalleOrdenCompra::create([
                    'orden_compra_id'      => $ordenes_compra->id,
                    'variante_producto_id' => $linea['variante_id'],
                    'cantidad_solicitada'  => $cantidad,
                    'cantidad_recibida'    => 0,
                    'precio_unitario'      => $precioUnitario,
                    'itbis_incluido'       => $itbisIncluido,
                    'itbis_porcentaje'     => $itbisPorcentaje,
                    'subtotal'             => round($lineaSubtotal, 2),
                ]);
            }

            $ordenes_compra->update([
                'subtotal' => round($subtotalOrden, 2),
                'impuesto' => round($impuestoOrden, 2),
                'total'    => round($subtotalOrden + $impuestoOrden, 2),
            ]);
        });

        return redirect()->route('ordenes_compra.show', $ordenes_compra)
            ->with('success', 'Orden actualizada correctamente.');
    }

    public function confirmar(OrdenCompra $ordenes_compra)
    {
        if ($ordenes_compra->estado !== 'borrador') {
            return redirect()->route('ordenes_compra.show', $ordenes_compra)
                ->with('error', 'Solo se pueden confirmar órdenes en borrador.');
        }

        if ($ordenes_compra->detalles()->count() === 0) {
            return redirect()->route('ordenes_compra.show', $ordenes_compra)
                ->with('error', 'La orden no tiene líneas registradas.');
        }

        $ordenes_compra->update(['estado' => 'confirmada']);

        return redirect()->route('ordenes_compra.show', $ordenes_compra)
            ->with('success', 'Orden confirmada correctamente.');
    }

    public function cancelar(OrdenCompra $ordenes_compra)
    {
        if (!$ordenes_compra->esCancelable()) {
            return redirect()->route('ordenes_compra.show', $ordenes_compra)
                ->with('error', 'No puedes cancelar una orden que ya tiene recepciones.');
        }

        $ordenes_compra->update(['estado' => 'cancelada']);

        return redirect()->route('ordenes_compra.show', $ordenes_compra)
            ->with('success', 'Orden cancelada correctamente.');
    }

    public function buscarVariantes(Request $request)
    {
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $variantes = VarianteProducto::with(['producto', 'valores.atributo'])
            ->whereHas('producto', fn($query) => $query->where('estado', true))
            ->where('estado', true)
            ->where(function ($query) use ($q) {
                $query->where('codigo', 'like', "%{$q}%")
                      ->orWhere('codigo_barras', 'like', "%{$q}%")
                      ->orWhereHas('producto', fn($pq) =>
                          $pq->where('nombre', 'like', "%{$q}%")
                      );
            })
            ->limit(15)
            ->get()
            ->map(fn($v) => [
                'id'     => $v->id,
                'texto'  => $v->producto?->nombre . ' — ' . $v->valores->map(fn($val) =>
                    $val->atributo?->nombre . ': ' . $val->valor
                )->join(', '),
                'codigo' => $v->codigo,
                'precio' => $v->costo,
            ]);

        return response()->json($variantes);
    }
}