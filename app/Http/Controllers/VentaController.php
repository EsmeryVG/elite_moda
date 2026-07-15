<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Comision;
use App\Models\ComprobanteFiscal;
use App\Models\Configuracion;
use App\Models\CuentaPorCobrar;
use App\Models\DetalleVenta;
use App\Models\Empleado;
use App\Models\MovimientoInventario;
use App\Models\Pago;
use App\Models\Stock;
use App\Models\TipoPago;
use App\Models\SesionCaja;
use App\Models\Venta;
use App\Models\VarianteProducto;

use App\Services\DescuentoService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::with(['cliente', 'empleado', 'usuario'])
                      ->orderBy('fecha', 'desc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('codigo', 'like', '%' . $request->buscar . '%')
                  ->orWhere('ncf', 'like', '%' . $request->buscar . '%');
            });
        }

        $ventas = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return view('ventas._tabla', compact('ventas'))->render();
        }

        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        $clienteDefault = Cliente::where('es_default', true)->first();
        $tiposPago      = TipoPago::activos()->orderBy('nombre')->get();
        $horarioCierre  = Configuracion::get('horario_cierre', '19:00');

        return view('ventas.create', compact('clienteDefault', 'tiposPago', 'horarioCierre'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'                => 'required|exists:clientes,id',
            'empleado_id'                => 'nullable|exists:empleados,id',
            'itbis_global'                => 'nullable|boolean',
            'observaciones'              => 'nullable|string',
            'lineas'                     => 'required|array|min:1',
            'lineas.*.variante_id'       => 'required|exists:variante_productos,id',
            'lineas.*.cantidad'          => 'required|integer|min:1',
            'pagos'                      => 'required|array|min:1',
            'pagos.*.tipo_pago_id'       => 'required|exists:tipos_pago,id',
            'pagos.*.monto'              => 'required|numeric|min:0.01',
            'pagos.*.referencia'         => 'nullable|string',
            'pagos.*.banco'              => 'nullable|string',
            'notas_credito'                => 'nullable|array',
            'notas_credito.*.id'           => 'required_with:notas_credito|exists:notas_credito,id',
            'notas_credito.*.monto'        => 'required_with:notas_credito|numeric|min:0.01',
        ], [
            'cliente_id.required'  => 'El cliente es obligatorio.',
            'lineas.required'      => 'Debes agregar al menos un producto.',
            'pagos.required'       => 'Debes registrar al menos un método de pago.',
        ]);

        $itbisPorcentaje = (float) Configuracion::get('itbis_porcentaje', 18);
        $cliente         = Cliente::findOrFail($request->cliente_id);

        $sesionCaja = SesionCaja::abiertas()->orderBy('fecha_apertura')->first();

        if (!$sesionCaja) {
            throw ValidationException::withMessages([
                'caja' => 'No hay ninguna sesión de caja abierta.',
            ]);
        }

        $almacenId = $sesionCaja->caja->almacen_id;

        if (!$almacenId) {
            throw ValidationException::withMessages([
                'almacen' => 'No hay un almacén disponible para procesar la venta.',
            ]);
        }

        // Validar disponibilidad de stock antes de iniciar la transacción
        foreach ($request->lineas as $linea) {
            $stock = Stock::where('variante_producto_id', $linea['variante_id'])
                          ->where('almacen_id', $almacenId)
                          ->first();

            $disponible = $stock?->cantidad_disponible ?? 0;

            if ($disponible < $linea['cantidad']) {
                $variante = VarianteProducto::find($linea['variante_id']);
                throw ValidationException::withMessages([
                    'lineas' => "Stock insuficiente para {$variante?->producto?->nombre}. Disponible: {$disponible}.",
                ]);
            }
        }

        $venta = DB::transaction(function () use ($request, $itbisPorcentaje, $cliente, $almacenId, $sesionCaja) {

            $descuentoService = new DescuentoService();
            $itbisGlobal      = $request->boolean('itbis_global', true);

            $subtotalVenta  = 0;
            $impuestoVenta  = 0;
            $descuentoVenta = 0;

            // Determinar comprobante fiscal según si el cliente tiene RNC
            $tipoComprobante = $cliente->rnc ? 'Crédito Fiscal' : 'Consumidor Final';
            $comprobante = ComprobanteFiscal::activos()
                ->where('tipo_comprobante', $tipoComprobante)
                ->whereColumn('numero_actual', '<=', 'rango_fin')
                ->where('fecha_vencimiento', '>=', now())
                ->lockForUpdate()
                ->first();

            if (!$comprobante) {
                throw ValidationException::withMessages([
                    'ncf' => "No hay comprobantes fiscales disponibles del tipo {$tipoComprobante}.",
                ]);
            }

            $ncf = $comprobante->siguienteNumero();
            $comprobante->increment('numero_actual');

            $venta = Venta::create([
                'cliente_id'             => $cliente->id,
                'empleado_id'            => $request->empleado_id,
                'almacen_id'             => $almacenId,
                 'sesion_caja_id'         => $sesionCaja->id,
                'usuario_id'             => Auth::id() ?? 1,
                'fecha'                  => now(),
                'estado'                 => 'pendiente',
                'ncf'                    => $ncf,
                'comprobante_fiscal_id'  => $comprobante->id,
                'observaciones'          => $request->observaciones,
            ]);

            $venta->codigo = 'VTA-' . str_pad($venta->id, 5, '0', STR_PAD_LEFT);
            $venta->save();

            foreach ($request->lineas as $linea) {
                $variante       = VarianteProducto::findOrFail($linea['variante_id']);
                $cantidad       = (int) $linea['cantidad'];
                $precioUnitario = (float) $variante->precio_venta;

                $totalLineaSinDescuento = $cantidad * $precioUnitario;

                // Buscar el mejor descuento aplicable
                $resultado          = $descuentoService->mejorDescuento($variante, $cliente, $precioUnitario);
                $descuentoUnitario  = $resultado['monto'];
                $descuentoLinea     = $descuentoUnitario * $cantidad;

                $precioConDescuento = $totalLineaSinDescuento - $descuentoLinea;

                // Calcular ITBIS sobre el precio ya con descuento (toggle global)
                $itbisLinea = 0;
                $baseLinea  = $precioConDescuento;

                if ($itbisGlobal) {
                    $baseLinea  = $precioConDescuento / (1 + $itbisPorcentaje / 100);
                    $itbisLinea = $precioConDescuento - $baseLinea;
                }

                $subtotalVenta  += $baseLinea;
                $impuestoVenta  += $itbisLinea;
                $descuentoVenta += $descuentoLinea;

                DetalleVenta::create([
                    'venta_id'             => $venta->id,
                    'variante_producto_id' => $variante->id,
                    'cantidad'             => $cantidad,
                    'precio_unitario'      => $precioUnitario,
                    'descuento_aplicado'   => round($descuentoLinea, 2),
                    'subtotal'             => round($precioConDescuento, 2),
                    'itbis_aplicado'       => $itbisGlobal,
                ]);

                // Descontar stock
                Stock::decrementar($variante->id, $almacenId, $cantidad);

                // Registrar movimiento
                MovimientoInventario::registrar(
                    varianteId:     $variante->id,
                    almacenId:      $almacenId,
                    tipo:           'salida_venta',
                    cantidad:       $cantidad,
                    referenciaTipo: 'venta',
                    referenciaId:   $venta->id,
                    motivo:         'Venta — ' . $venta->codigo,
                    usuarioId:      Auth::id() ?? 1
                );
            }

            $totalVenta = $subtotalVenta + $impuestoVenta;

            $venta->update([
                'subtotal'        => round($subtotalVenta, 2),
                'descuento_total' => round($descuentoVenta, 2),
                'impuesto'        => round($impuestoVenta, 2),
                'total'           => round($totalVenta, 2),
                'estado'          => 'completada',
            ]);

           // Registrar pagos — separar crédito de los demás
            $tipoPagoCredito = TipoPago::where('nombre', 'Crédito')->first();
            $montoCredito    = 0;

            foreach ($request->pagos as $pago) {
                $monto = (float) $pago['monto'];
                if ($monto <= 0) continue;

                // Si es pago con crédito, lo manejamos aparte
                if ($tipoPagoCredito && $pago['tipo_pago_id'] == $tipoPagoCredito->id) {
                    $montoCredito += $monto;
                    continue;
                }

                Pago::create([
                    'venta_id'     => $venta->id,
                    'tipo_pago_id' => $pago['tipo_pago_id'],
                    'monto'        => $monto,
                    'referencia'   => $pago['referencia'] ?? null,
                    'banco'        => $pago['banco'] ?? null,
                    'fecha'        => now(),
                    'estado'       => 'confirmado',
                ]);
            }

            // Registrar pagos con Nota de Crédito
            if ($request->filled('notas_credito')) {
                $tipoPagoNC = TipoPago::where('nombre', 'Nota de Crédito')->firstOrFail();

                foreach ($request->notas_credito as $ncUsada) {
                    $nota = \App\Models\NotaCredito::where('id', $ncUsada['id'])
                        ->where('cliente_id', $cliente->id)
                        ->where('estado', 'activa')
                        ->lockForUpdate()
                        ->first();

                    if (! $nota) {
                        throw ValidationException::withMessages([
                            'notas_credito' => 'Una de las notas de crédito seleccionadas ya no está disponible.',
                        ]);
                    }

                    $monto = (float) $ncUsada['monto'];

                    if ($monto > $nota->monto_disponible) {
                        throw ValidationException::withMessages([
                            'notas_credito' => "El monto usado de la nota {$nota->codigo} excede su saldo disponible.",
                        ]);
                    }

                    Pago::create([
                        'venta_id'     => $venta->id,
                        'tipo_pago_id' => $tipoPagoNC->id,
                        'monto'        => $monto,
                        'referencia'   => $nota->codigo,
                        'fecha'        => now(),
                        'estado'       => 'confirmado',
                    ]);

                    $nota->aplicarMonto($monto);
                }
            }

            // Si hubo pago con crédito, generar cuenta por cobrar
            if ($montoCredito > 0) {
                $clienteActualizado = Cliente::find($cliente->id);

                if ($clienteActualizado->balance_credito + $montoCredito > $clienteActualizado->limite_credito) {
                    throw ValidationException::withMessages([
                        'credito' => 'El monto a crédito supera el límite disponible del cliente.',
                    ]);
                }

                Pago::create([
                    'venta_id'     => $venta->id,
                    'tipo_pago_id' => $tipoPagoCredito->id,
                    'monto'        => $montoCredito,
                    'fecha'        => now(),
                    'estado'       => 'confirmado',
                ]);

                $cuentaPorCobrar = CuentaPorCobrar::create([
                    'venta_id'          => $venta->id,
                    'cliente_id'        => $cliente->id,
                    'monto_total'       => $montoCredito,
                    'monto_pagado'      => 0,
                    'monto_pendiente'   => $montoCredito,
                    'fecha_emision'     => now(),
                    'fecha_vencimiento' => now()->addDays((int) Configuracion::get('credito_dias_vencimiento', 30)),
                    'estado'            => 'pendiente',
                ]);

                $cuentaPorCobrar->codigo = 'CPC-' . str_pad($cuentaPorCobrar->id, 5, '0', STR_PAD_LEFT);
                $cuentaPorCobrar->save();

                $clienteActualizado->increment('balance_credito', $montoCredito);
            }
            

            // Calcular comisión del vendedor (no del cajero)
            if ($request->empleado_id) {
                $empleado = Empleado::find($request->empleado_id);

                if ($empleado && $empleado->comision_porcentaje > 0) {
                    $montoComision = round($totalVenta * ($empleado->comision_porcentaje / 100), 2);

                    Comision::create([
                        'empleado_id'         => $empleado->id,
                        'venta_id'            => $venta->id,
                        'monto_venta'         => round($totalVenta, 2),
                        'porcentaje_comision' => $empleado->comision_porcentaje,
                        'monto_comision'      => $montoComision,
                        'estado'              => 'pendiente',
                        'fecha'               => now(),
                    ]);
                }
            }

            return $venta;
        });

        return redirect()->route('ventas.show', $venta)
        ->with('success', 'Venta registrada correctamente.')
        ->with('abrir_factura', route('ventas.factura', $venta));
    }

    public function show(Venta $venta)
    {
        $venta->load([
            'cliente',
            'empleado',
            'usuario',
            'comprobanteFiscal',
            'detalles.variante.producto',
            'detalles.variante.valores.atributo',
            'pagos.tipoPago',
        ]);

        return view('ventas.show', compact('venta'));
    }

    public function anular(Venta $venta)
    {
        if (!$venta->esAnulable()) {
            return redirect()->route('ventas.show', $venta)
                ->with('error', 'Esta venta no puede ser anulada.');
        }

        DB::transaction(function () use ($venta) {
            $venta->load('detalles');

            foreach ($venta->detalles as $detalle) {
                Stock::incrementar($detalle->variante_producto_id, $venta->almacen_id, $detalle->cantidad);

                MovimientoInventario::registrar(
                    varianteId:     $detalle->variante_producto_id,
                    almacenId:      $venta->almacen_id,
                    tipo:           'entrada_devolucion',
                    cantidad:       $detalle->cantidad,
                    referenciaTipo: 'venta_anulada',
                    referenciaId:   $venta->id,
                    motivo:         'Anulación de venta — ' . $venta->codigo,
                    usuarioId:      Auth::id() ?? 1
                );
            }

            $venta->update(['estado' => 'anulada']);

            Comision::where('venta_id', $venta->id)->update(['estado' => 'anulada']);
        });

        return redirect()->route('ventas.show', $venta)
            ->with('success', 'Venta anulada. El stock ha sido restaurado.');
    }

    public function buscarProductos(Request $request)
    {
        $q = $request->get('q', '');

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $almacenId = $this->obtenerAlmacenVenta();

        $variantes = VarianteProducto::with(['producto', 'valores.atributo'])
            ->whereHas('producto', fn($query) => $query->where('estado', true))
            ->where('estado', true)
            ->where(function ($query) use ($q) {
                $query->where('codigo', 'like', "%{$q}%")
                      ->orWhere('codigo_barras', $q)
                      ->orWhereHas('producto', fn($pq) =>
                          $pq->where('nombre', 'like', "%{$q}%")
                      );
            })
            ->limit(15)
            ->get()
            ->map(function ($v) use ($almacenId) {
                $stock = Stock::where('variante_producto_id', $v->id)
                              ->where('almacen_id', $almacenId)
                              ->first();

                return [
                    'id'         => $v->id,
                    'texto'      => $v->producto?->nombre . ' — ' . $v->valores->map(fn($val) =>
                        $val->atributo?->nombre . ': ' . $val->valor
                    )->join(', '),
                    'codigo'     => $v->codigo,
                    'precio'     => $v->precio_venta,
                    'disponible' => $stock?->cantidad_disponible ?? 0,
                ];
            });

        return response()->json($variantes);
    }

    public function buscarEmpleados(Request $request)
    {
        $q = $request->get('q', '');

        $empleados = Empleado::where('estado', true)
            ->where(function ($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%")
                      ->orWhere('apellido', 'like', "%{$q}%")
                      ->orWhere('codigo', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get()
            ->map(fn($e) => [
                'id'    => $e->id,
                'texto' => $e->nombre_completo . ' (' . $e->codigo . ')',
            ]);

        return response()->json($empleados);
    }

    public function buscarClientes(Request $request)
    {
        $q = $request->get('q', '');

        $clientes = Cliente::where('estado', true)
            ->where('es_default', false)
            ->where(function ($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%")
                      ->orWhere('apellido', 'like', "%{$q}%")
                      ->orWhere('cedula', 'like', "%{$q}%")
                      ->orWhere('rnc', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get()
            ->map(fn($c) => [
                'id'    => $c->id,
                'texto' => trim($c->nombre . ' ' . $c->apellido) . ' — ' . ($c->cedula ?? $c->rnc ?? 'sin doc'),
            ]);

        return response()->json($clientes);
    }

    /**
     * Determina el almacén desde el cual se descuenta el stock en una venta,
     * a partir de la sesión de caja abierta más antigua (cualquier empleado
     * puede vender mientras exista una sesión abierta).
     */
    private function obtenerAlmacenVenta(): ?int
    {
        $sesionCaja = SesionCaja::abiertas()->orderBy('fecha_apertura')->first();

        return $sesionCaja?->caja?->almacen_id;
    }

    public function categorias()
    {
        $categorias = \App\Models\Categoria::activas()
            ->orderBy('nombre')
            ->get()
            ->map(fn($c) => [
                'id'     => $c->id,
                'nombre' => $c->nombre,
            ]);

        return response()->json($categorias);
    }

    public function productosPorCategoria(Request $request)
    {
        $categoriaId = $request->get('categoria_id');
        $almacenId   = $this->obtenerAlmacenVenta();

        $variantes = VarianteProducto::with(['producto.categoria', 'valores.atributo'])
            ->whereHas('producto', fn($q) =>
                $q->where('estado', true)->where('categoria_id', $categoriaId)
            )
            ->where('estado', true)
            ->get()
            ->map(function ($v) use ($almacenId) {
                $stock = Stock::where('variante_producto_id', $v->id)
                            ->where('almacen_id', $almacenId)
                            ->first();

                $atributos = $v->valores->map(fn($val) =>
                    $val->atributo?->nombre . ': ' . $val->valor
                )->join(', ');

                return [
                    'id'         => $v->id,
                    'texto'      => $v->producto?->nombre . ($atributos ? ' — ' . $atributos : ''),
                    'codigo'     => $v->codigo,
                    'precio'     => $v->precio_venta,
                    'disponible' => $stock?->cantidad_disponible ?? 0,
                ];
            });

        return response()->json($variantes);
    }

    public function verificarCredito(Request $request)
    {
        $cliente = Cliente::find($request->get('cliente_id'));

        if (!$cliente || !$cliente->credito_activo || $cliente->es_default) {
            return response()->json(['tiene_credito' => false]);
        }

        return response()->json([
            'tiene_credito'      => true,
            'limite_credito'     => $cliente->limite_credito,
            'balance_credito'    => $cliente->balance_credito,
            'credito_disponible' => $cliente->credito_disponible,
        ]);
    }

    public function notasCreditoCliente(Request $request)
    {
        $clienteId = $request->get('cliente_id');

        $notas = \App\Models\NotaCredito::where('cliente_id', $clienteId)
            ->where('estado', 'activa')
            ->where('monto_disponible', '>', 0)
            ->where(function ($q) {
                $q->whereNull('fecha_vencimiento')->orWhere('fecha_vencimiento', '>=', now());
            })
            ->orderBy('fecha')
            ->get()
            ->map(fn ($nc) => [
                'id' => $nc->id,
                'codigo' => $nc->codigo,
                'monto_disponible' => (float) $nc->monto_disponible,
            ]);

        return response()->json($notas);
    }

    public function factura(Venta $venta)
    {
        $venta->load([
            'cliente',
            'empleado',
            'usuario',
            'comprobanteFiscal',
            'detalles.variante.producto',
            'detalles.variante.valores.atributo',
            'pagos.tipoPago',
            'almacen.sucursal',
        ]);

        $config = \App\Models\Configuracion::all()->keyBy('clave');

        return view('ventas.factura', compact('venta', 'config'));
    }
}