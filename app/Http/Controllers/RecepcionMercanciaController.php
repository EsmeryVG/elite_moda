<?php

namespace App\Http\Controllers;

use App\Models\DetalleOrdenCompra;
use App\Models\DetalleRecepcion;
use App\Models\MovimientoInventario;
use App\Models\OrdenCompra;
use App\Models\RecepcionMercancia;
use App\Models\Stock;
use App\Models\VarianteProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecepcionMercanciaController extends Controller
{
    public function create(Request $request)
    {
        $orden = OrdenCompra::with([
            'proveedor',
            'almacen',
            'detalles.variante.producto',
            'detalles.variante.valores.atributo',
        ])->findOrFail($request->orden);

        if (!in_array($orden->estado, ['confirmada', 'parcial'])) {
            return redirect()->route('ordenes_compra.show', $orden)
                ->with('error', 'Esta orden no está disponible para recepción.');
        }

        return view('recepciones.create', compact('orden'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'orden_compra_id'             => 'required|exists:ordenes_compra,id',
            'fecha'                       => 'required|date',
            'tipo'                        => 'required|in:completa,parcial,no_conforme',
            'observaciones'               => 'nullable|string',
            'motivo_rechazo'              => 'required_if:tipo,no_conforme|nullable|string',
            'lineas'                      => 'required|array|min:1',
            'lineas.*.detalle_orden_id'   => 'required|exists:detalle_ordenes_compra,id',
            'lineas.*.cantidad_recibida'  => 'required|integer|min:0',
            'lineas.*.cantidad_aceptada'  => 'required|integer|min:0',
            'lineas.*.estado_calidad'     => 'required|in:conforme,no_conforme',
        ], [
            'motivo_rechazo.required_if'          => 'El motivo de rechazo es obligatorio para recepciones no conformes.',
            'lineas.*.cantidad_recibida.required' => 'La cantidad recibida es obligatoria.',
            'lineas.*.cantidad_aceptada.required' => 'La cantidad aceptada es obligatoria.',
        ]);

        DB::transaction(function () use ($request) {
            $orden = OrdenCompra::findOrFail($request->orden_compra_id);

            $recepcion = RecepcionMercancia::create([
                'orden_compra_id' => $orden->id,
                'usuario_id'      => Auth::id() ?? 1,
                'fecha'           => $request->fecha,
                'tipo'            => $request->tipo,
                'observaciones'   => $request->observaciones,
                'motivo_rechazo'  => $request->motivo_rechazo,
            ]);

            $recepcion->codigo = 'REC-' . str_pad($recepcion->id, 3, '0', STR_PAD_LEFT);
            $recepcion->save();

            foreach ($request->lineas as $linea) {
                $detalleOrden = DetalleOrdenCompra::findOrFail($linea['detalle_orden_id']);
                $varianteId   = $detalleOrden->variante_producto_id;

                DetalleRecepcion::create([
                    'recepcion_id'      => $recepcion->id,
                    'detalle_orden_id'  => $detalleOrden->id,
                    'cantidad_recibida' => $linea['cantidad_recibida'],
                    'cantidad_aceptada' => $linea['cantidad_aceptada'],
                    'estado_calidad'    => $linea['estado_calidad'],
                    'observacion'       => $linea['observacion'] ?? null,
                ]);

                $detalleOrden->increment('cantidad_recibida', $linea['cantidad_aceptada']);

                if ($linea['cantidad_aceptada'] > 0) {
                    $stockActual      = Stock::where('variante_producto_id', $varianteId)
                                            ->where('almacen_id', $orden->almacen_id)
                                            ->first();
                    $cantidadAnterior = $stockActual?->cantidad_disponible ?? 0;

                    Stock::incrementar($varianteId, $orden->almacen_id, $linea['cantidad_aceptada']);

                    MovimientoInventario::registrar(
                        varianteId:     $varianteId,
                        almacenId:      $orden->almacen_id,
                        tipo:           'entrada_compra',
                        cantidad:       $linea['cantidad_aceptada'],
                        referenciaTipo: 'recepcion_mercancia',
                        referenciaId:   $recepcion->id,
                        motivo:         'Recepción de mercancía — ' . $recepcion->codigo,
                        usuarioId:      Auth::id() ?? 1
                    );

                    $variante      = VarianteProducto::find($varianteId);
                    $costoAnterior = $variante->costo ?? 0;
                    $cantidadNueva = $linea['cantidad_aceptada'];
                    $costoNuevo    = $detalleOrden->precio_base;

                    if (($cantidadAnterior + $cantidadNueva) > 0) {
                        $costoPromedio = (($cantidadAnterior * $costoAnterior) + ($cantidadNueva * $costoNuevo))
                                         / ($cantidadAnterior + $cantidadNueva);

                        $variante->update(['costo' => round($costoPromedio, 2)]);
                    }
                }
            }

            $this->actualizarEstadoOrden($orden);
        });

        return redirect()->route('ordenes_compra.show', $request->orden_compra_id)
            ->with('success', 'Recepción registrada correctamente.');
    }

    private function actualizarEstadoOrden(OrdenCompra $orden): void
    {
        $orden->load('detalles');

        $totalSolicitado = $orden->detalles->sum('cantidad_solicitada');
        $totalRecibido   = $orden->detalles->sum('cantidad_recibida');

        if ($totalRecibido >= $totalSolicitado) {
            $orden->update(['estado' => 'completada']);
        } elseif ($totalRecibido > 0) {
            $orden->update(['estado' => 'parcial']);
        }
    }

    public function index(Request $request)
    {
        $query = RecepcionMercancia::with(['orden.proveedor', 'orden.almacen', 'usuario'])
                                   ->orderBy('created_at', 'desc');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('codigo', 'like', '%' . $request->buscar . '%')
                  ->orWhereHas('orden', fn($q2) =>
                      $q2->where('codigo', 'like', '%' . $request->buscar . '%')
                  );
            });
        }

        $recepciones = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('recepciones._tabla', compact('recepciones'))->render();
        }

        return view('recepciones.index', compact('recepciones'));
    }

    public function show(RecepcionMercancia $recepcion)
    {
        $recepcion->load([
            'orden.proveedor',
            'orden.almacen.sucursal',
            'usuario',
            'detalles.detalleOrden.variante.producto',
            'detalles.detalleOrden.variante.valores.atributo',
        ]);

        return view('recepciones.show', compact('recepcion'));
    }
}