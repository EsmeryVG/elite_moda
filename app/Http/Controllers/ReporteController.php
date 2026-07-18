<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Pago;
use App\Models\DetalleVenta;
use App\Models\Stock;
use App\Models\VarianteProducto;
use App\Models\MovimientoInventario;
use App\Models\Almacen;
use App\Models\OrdenCompra;
use App\Models\DetalleOrdenCompra;
use App\Models\Proveedor;
use App\Models\CuentaPorCobrar;
use App\Models\PagoCredito;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function index()
    {
        return redirect()->route('reportes.ventas');
    }

    /* ============ VENTAS ============ */

    private function rangoFechas(Request $request): array
    {
        $desde = $request->filled('desde')
            ? Carbon::parse($request->desde)->startOfDay()
            : now()->startOfMonth();

        $hasta = $request->filled('hasta')
            ? Carbon::parse($request->hasta)->endOfDay()
            : now()->endOfDay();

        return [$desde, $hasta];
    }

    private function datosReporteVentas(Request $request, bool $paginar = true): array
    {
        [$desde, $hasta] = $this->rangoFechas($request);

        $ventasQuery = Venta::where('ventas.estado', 'completada')
            ->whereBetween('fecha', [$desde, $hasta]);

        $totalVendido = (clone $ventasQuery)->sum('total');
        $cantidadVentas = (clone $ventasQuery)->count();
        $totalItbis = (clone $ventasQuery)->sum('impuesto');
        $totalDescuentos = (clone $ventasQuery)->sum('descuento_total');
        $ticketPromedio = $cantidadVentas > 0 ? $totalVendido / $cantidadVentas : 0;

        // Serie completa para el gráfico (sin paginar, siempre completa)
        $ventasPorDiaCompleto = (clone $ventasQuery)
            ->selectRaw('DATE(fecha) as dia, COUNT(*) as cantidad, SUM(total) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        // Versión paginada para la tabla (10 días por página)
        if ($paginar) {
            $porPagina = 10;
            $pagina = LengthAwarePaginator::resolveCurrentPage('pagina');
            $items = $ventasPorDiaCompleto->slice(($pagina - 1) * $porPagina, $porPagina)->values();
            $ventasPorDia = new LengthAwarePaginator(
                $items,
                $ventasPorDiaCompleto->count(),
                $porPagina,
                $pagina,
                ['path' => request()->url(), 'query' => request()->query(), 'pageName' => 'pagina']
            );
        } else {
            $ventasPorDia = $ventasPorDiaCompleto;
        }

        $porMetodoPago = Pago::where('pagos.estado', 'confirmado')
            ->whereHas('venta', function ($q) use ($desde, $hasta) {
                $q->where('ventas.estado', 'completada')->whereBetween('fecha', [$desde, $hasta]);
            })
            ->join('tipos_pago', 'pagos.tipo_pago_id', '=', 'tipos_pago.id')
            ->selectRaw('tipos_pago.nombre as metodo, SUM(pagos.monto) as total, COUNT(*) as cantidad')
            ->groupBy('tipos_pago.nombre')
            ->orderByDesc('total')
            ->get();

        $topProductos = DetalleVenta::whereHas('venta', function ($q) use ($desde, $hasta) {
                $q->where('ventas.estado', 'completada')->whereBetween('fecha', [$desde, $hasta]);
            })
            ->selectRaw('variante_producto_id, SUM(cantidad) as cantidad_vendida, SUM(subtotal) as total_vendido')
            ->groupBy('variante_producto_id')
            ->orderByDesc('cantidad_vendida')
            ->with('variante.producto')
            ->limit(10)
            ->get();

        $porVendedor = Venta::where('ventas.estado', 'completada')
            ->whereBetween('fecha', [$desde, $hasta])
            ->whereNotNull('empleado_id')
            ->with('empleado')
            ->selectRaw('empleado_id, COUNT(*) as cantidad, SUM(total) as total')
            ->groupBy('empleado_id')
            ->orderByDesc('total')
            ->get();

        return compact(
            'desde', 'hasta', 'totalVendido', 'cantidadVentas', 'totalItbis',
            'totalDescuentos', 'ticketPromedio', 'ventasPorDia', 'ventasPorDiaCompleto',
            'porMetodoPago', 'topProductos', 'porVendedor'
        );
    }

    public function ventas(Request $request)
    {
        $datos = $this->datosReporteVentas($request);
        return view('reportes.ventas', $datos);
    }

    public function ventasCsv(Request $request)
    {
        $datos = $this->datosReporteVentas($request, paginar: false);

        $filename = 'reporte_ventas_' . $datos['desde']->format('Y-m-d') . '_a_' . $datos['hasta']->format('Y-m-d') . '.csv';

        $callback = function () use ($datos) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['Reporte de Ventas']);
            fputcsv($handle, ['Período', $datos['desde']->format('d/m/Y') . ' - ' . $datos['hasta']->format('d/m/Y')]);
            fputcsv($handle, []);
            fputcsv($handle, ['Total vendido', number_format($datos['totalVendido'], 2)]);
            fputcsv($handle, ['Cantidad de ventas', $datos['cantidadVentas']]);
            fputcsv($handle, ['Ticket promedio', number_format($datos['ticketPromedio'], 2)]);
            fputcsv($handle, ['Total ITBIS', number_format($datos['totalItbis'], 2)]);
            fputcsv($handle, []);

            fputcsv($handle, ['Ventas por día']);
            fputcsv($handle, ['Fecha', 'Cantidad', 'Total']);
            foreach ($datos['ventasPorDiaCompleto'] as $fila) {
                fputcsv($handle, [$fila->dia, $fila->cantidad, number_format($fila->total, 2)]);
            }
            fputcsv($handle, []);

            fputcsv($handle, ['Desglose por método de pago']);
            fputcsv($handle, ['Método', 'Cantidad', 'Total']);
            foreach ($datos['porMetodoPago'] as $fila) {
                fputcsv($handle, [$fila->metodo, $fila->cantidad, number_format($fila->total, 2)]);
            }
            fputcsv($handle, []);

            fputcsv($handle, ['Top productos']);
            fputcsv($handle, ['Producto', 'Cantidad vendida', 'Total']);
            foreach ($datos['topProductos'] as $fila) {
                fputcsv($handle, [
                    $fila->variante?->producto?->nombre ?? '—',
                    $fila->cantidad_vendida,
                    number_format($fila->total_vendido, 2),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function ventasPdf(Request $request)
    {
        $datos = $this->datosReporteVentas($request, paginar: false);
        $datos['ventasPorDia'] = $datos['ventasPorDiaCompleto'];

        $pdf = Pdf::loadView('reportes.ventas_pdf', $datos)->setPaper('letter', 'portrait');

        $filename = 'reporte_ventas_' . $datos['desde']->format('Y-m-d') . '_a_' . $datos['hasta']->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /* ============ INVENTARIO ============ */
    private function datosReporteInventario(Request $request, bool $paginar = true): array
    {
        $diasSinMovimiento = (int) $request->get('dias_sin_movimiento', 30);
        $almacenId = $request->get('almacen_id');
        $stockBase = fn () => Stock::whereHas('variante', fn ($q) => $q->where('estado', true));
        $stockQuery = $stockBase();
        if ($almacenId) {
            $stockQuery->where('almacen_id', $almacenId);
        }
        // Valorización total: a costo y a precio de venta
        $valorTotalCosto = (clone $stockQuery)
            ->join('variante_productos', 'stocks.variante_producto_id', '=', 'variante_productos.id')
            ->selectRaw('SUM(stocks.cantidad_disponible * variante_productos.costo) as valor')
            ->value('valor') ?? 0;
        $valorTotalVenta = (clone $stockQuery)
            ->join('variante_productos', 'stocks.variante_producto_id', '=', 'variante_productos.id')
            ->selectRaw('SUM(stocks.cantidad_disponible * variante_productos.precio_venta) as valor')
            ->value('valor') ?? 0;
        $utilidadPotencial = $valorTotalVenta - $valorTotalCosto;
        $cantidadSkusActivos = VarianteProducto::where('estado', true)->count();
        $stockCritico = (clone $stockQuery)
            ->whereColumn('cantidad_disponible', '<=', 'stock_minimo')
            ->where('cantidad_disponible', '>', 0)
            ->count();
        $stockAgotado = (clone $stockQuery)
            ->where('cantidad_disponible', '<=', 0)
            ->count();
        // Valorización por almacén (costo y venta)
        $valorizacionPorAlmacen = Stock::join('variante_productos', 'stocks.variante_producto_id', '=', 'variante_productos.id')
            ->join('almacenes', 'stocks.almacen_id', '=', 'almacenes.id')
            ->selectRaw('almacenes.nombre as almacen, SUM(stocks.cantidad_disponible) as unidades,
                         SUM(stocks.cantidad_disponible * variante_productos.costo) as valor_costo,
                         SUM(stocks.cantidad_disponible * variante_productos.precio_venta) as valor_venta')
            ->where('variante_productos.estado', true)
            ->groupBy('almacenes.id', 'almacenes.nombre')
            ->orderByDesc('valor_costo')
            ->get();
        // Valorización por categoría
        $valorizacionPorCategoria = Stock::join('variante_productos', 'stocks.variante_producto_id', '=', 'variante_productos.id')
            ->join('productos', 'variante_productos.producto_id', '=', 'productos.id')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->selectRaw('categorias.nombre as categoria, SUM(stocks.cantidad_disponible) as unidades,
                         SUM(stocks.cantidad_disponible * variante_productos.costo) as valor_costo,
                         SUM(stocks.cantidad_disponible * variante_productos.precio_venta) as valor_venta')
            ->where('variante_productos.estado', true)
            ->groupBy('categorias.id', 'categorias.nombre')
            ->orderByDesc('valor_costo')
            ->get();
        // Top productos por valor en inventario (costo total invertido)
        $topValorInventario = Stock::join('variante_productos', 'stocks.variante_producto_id', '=', 'variante_productos.id')
            ->where('variante_productos.estado', true)
            ->selectRaw('stocks.variante_producto_id, SUM(stocks.cantidad_disponible) as unidades,
                         SUM(stocks.cantidad_disponible * variante_productos.costo) as valor_costo')
            ->groupBy('stocks.variante_producto_id')
            ->having('unidades', '>', 0)
            ->orderByDesc('valor_costo')
            ->with('variante.producto')
            ->limit(10)
            ->get();
        // Listado completo de stock crítico/agotado
        $listadoCriticoCompleto = (clone $stockQuery)
            ->with('variante.producto', 'almacen')
            ->whereColumn('cantidad_disponible', '<=', 'stock_minimo')
            ->orderBy('cantidad_disponible')
            ->get();
        $listadoCritico = $paginar
            ? $this->paginarColeccion($listadoCriticoCompleto, 10, 'pagina_criticos')
            : $listadoCriticoCompleto;
        // Productos sin movimiento reciente
        $fechaLimiteSinMov = now()->subDays($diasSinMovimiento);
        $ultimoMovimientoPorVariante = MovimientoInventario::selectRaw('variante_producto_id, MAX(fecha) as ultima_fecha')
            ->groupBy('variante_producto_id')
            ->pluck('ultima_fecha', 'variante_producto_id');
        $sinMovimientoCompleto = VarianteProducto::where('estado', true)
            ->with('producto')
            ->get()
            ->filter(function ($variante) use ($ultimoMovimientoPorVariante, $fechaLimiteSinMov) {
                $ultima = $ultimoMovimientoPorVariante->get($variante->id);
                return !$ultima || Carbon::parse($ultima)->lt($fechaLimiteSinMov);
            })
            ->values();
        $sinMovimiento = $paginar
            ? $this->paginarColeccion($sinMovimientoCompleto, 10, 'pagina_sin_mov')
            : $sinMovimientoCompleto;

        $almacenes = Almacen::where('estado', true)->orderBy('nombre')->get();
        return compact(
            'valorTotalCosto', 'valorTotalVenta', 'utilidadPotencial', 'cantidadSkusActivos',
            'stockCritico', 'stockAgotado', 'valorizacionPorAlmacen', 'valorizacionPorCategoria',
            'topValorInventario', 'listadoCritico', 'listadoCriticoCompleto', 'sinMovimiento',
            'sinMovimientoCompleto', 'diasSinMovimiento', 'almacenId', 'almacenes'
        );
    }
    private function paginarColeccion($coleccion, int $porPagina, string $nombrePagina)
    {
        $pagina = LengthAwarePaginator::resolveCurrentPage($nombrePagina);
        $items = $coleccion->slice(($pagina - 1) * $porPagina, $porPagina)->values();
        return new LengthAwarePaginator(
            $items,
            $coleccion->count(),
            $porPagina,
            $pagina,
            ['path' => request()->url(), 'query' => request()->query(), 'pageName' => $nombrePagina]
        );
    }
    public function inventario(Request $request)
    {
        $datos = $this->datosReporteInventario($request);
        return view('reportes.inventario', $datos);
    }
    public function inventarioCsv(Request $request)
    {
        $datos = $this->datosReporteInventario($request, paginar: false);
        $filename = 'reporte_inventario_' . now()->format('Y-m-d') . '.csv';
        $callback = function () use ($datos) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['Reporte de Inventario']);
            fputcsv($handle, ['Generado', now()->format('d/m/Y H:i')]);
            fputcsv($handle, []);
            fputcsv($handle, ['Valor de inventario (costo)', number_format($datos['valorTotalCosto'], 2)]);
            fputcsv($handle, ['Valor de inventario (precio de venta)', number_format($datos['valorTotalVenta'], 2)]);
            fputcsv($handle, ['Utilidad potencial', number_format($datos['utilidadPotencial'], 2)]);
            fputcsv($handle, ['SKUs activos', $datos['cantidadSkusActivos']]);
            fputcsv($handle, ['En stock crítico', $datos['stockCritico']]);
            fputcsv($handle, ['Agotados', $datos['stockAgotado']]);
            fputcsv($handle, []);
            fputcsv($handle, ['Valorización por almacén']);
            fputcsv($handle, ['Almacén', 'Unidades', 'Valor (costo)', 'Valor (venta)']);
            foreach ($datos['valorizacionPorAlmacen'] as $fila) {
                fputcsv($handle, [$fila->almacen, $fila->unidades, number_format($fila->valor_costo, 2), number_format($fila->valor_venta, 2)]);
            }
            fputcsv($handle, []);
            fputcsv($handle, ['Valorización por categoría']);
            fputcsv($handle, ['Categoría', 'Unidades', 'Valor (costo)', 'Valor (venta)']);
            foreach ($datos['valorizacionPorCategoria'] as $fila) {
                fputcsv($handle, [$fila->categoria, $fila->unidades, number_format($fila->valor_costo, 2), number_format($fila->valor_venta, 2)]);
            }
            fputcsv($handle, []);
            fputcsv($handle, ['Top productos por valor en inventario']);
            fputcsv($handle, ['Producto', 'Unidades', 'Valor (costo)']);
            foreach ($datos['topValorInventario'] as $fila) {
                fputcsv($handle, [$fila->variante?->producto?->nombre ?? '—', $fila->unidades, number_format($fila->valor_costo, 2)]);
            }
            fputcsv($handle, []);
            fputcsv($handle, ['Stock crítico / agotado']);
            fputcsv($handle, ['Producto', 'Almacén', 'Disponible', 'Mínimo', 'Nivel']);
            foreach ($datos['listadoCriticoCompleto'] as $fila) {
                fputcsv($handle, [
                    $fila->variante?->producto?->nombre ?? '—',
                    $fila->almacen?->nombre ?? '—',
                    $fila->cantidad_disponible,
                    $fila->stock_minimo,
                    ucfirst($fila->nivel),
                ]);
            }
            fputcsv($handle, []);
            fputcsv($handle, ['Productos sin movimiento (' . $datos['diasSinMovimiento'] . '+ días)']);
            fputcsv($handle, ['Producto', 'Código']);
            foreach ($datos['sinMovimientoCompleto'] as $fila) {
                fputcsv($handle, [$fila->producto?->nombre ?? '—', $fila->codigo]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
    public function inventarioPdf(Request $request)
    {
        $datos = $this->datosReporteInventario($request, paginar: false);
        $datos['listadoCritico'] = $datos['listadoCriticoCompleto'];
        $datos['sinMovimiento'] = $datos['sinMovimientoCompleto'];
        $pdf = Pdf::loadView('reportes.inventario_pdf', $datos)->setPaper('letter', 'portrait');
        return $pdf->download('reporte_inventario_' . now()->format('Y-m-d') . '.pdf');
    }

    /* ============ COMPRAS ============ */

    private function datosReporteCompras(Request $request, bool $paginar = true): array
    {
        [$desde, $hasta] = $this->rangoFechas($request);

        $ordenesQuery = OrdenCompra::whereBetween('fecha', [$desde, $hasta]);

        $totalComprado = (clone $ordenesQuery)->where('estado', '!=', 'cancelada')->sum('total');
        $cantidadOrdenes = (clone $ordenesQuery)->count();
        $ordenPromedio = $cantidadOrdenes > 0 ? $totalComprado / $cantidadOrdenes : 0;

        $ordenesRetrasadas = OrdenCompra::whereIn('estado', ['confirmada', 'parcial'])
            ->whereNotNull('fecha_esperada')
            ->where('fecha_esperada', '<', now())
            ->count();

        // Compras por proveedor
        $porProveedor = OrdenCompra::join('proveedores', 'ordenes_compra.proveedor_id', '=', 'proveedores.id')
            ->whereBetween('fecha', [$desde, $hasta])
            ->where('ordenes_compra.estado', '!=', 'cancelada')
            ->selectRaw('proveedores.nombre as proveedor, COUNT(*) as cantidad, SUM(ordenes_compra.total) as total')
            ->groupBy('proveedores.id', 'proveedores.nombre')
            ->orderByDesc('total')
            ->get();

        // Órdenes por estado
        $porEstado = (clone $ordenesQuery)
            ->selectRaw('estado, COUNT(*) as cantidad, SUM(total) as total')
            ->groupBy('estado')
            ->get();

        // Top productos comprados (por cantidad recibida y monto)
        $topProductosComprados = DetalleOrdenCompra::whereHas('orden', function ($q) use ($desde, $hasta) {
                $q->whereBetween('fecha', [$desde, $hasta])->where('estado', '!=', 'cancelada');
            })
            ->selectRaw('variante_producto_id, SUM(cantidad_solicitada) as cantidad_solicitada, SUM(subtotal) as total_gastado')
            ->groupBy('variante_producto_id')
            ->orderByDesc('total_gastado')
            ->with('variante.producto')
            ->limit(10)
            ->get();

        // Listado de órdenes retrasadas (para tabla + export)
        $listadoRetrasadasCompleto = OrdenCompra::with('proveedor')
            ->whereIn('estado', ['confirmada', 'parcial'])
            ->whereNotNull('fecha_esperada')
            ->where('fecha_esperada', '<', now())
            ->orderBy('fecha_esperada')
            ->get();

        $listadoRetrasadas = $paginar
            ? $this->paginarColeccion($listadoRetrasadasCompleto, 10, 'pagina_retrasadas')
            : $listadoRetrasadasCompleto;

        return compact(
            'desde', 'hasta', 'totalComprado', 'cantidadOrdenes', 'ordenPromedio',
            'ordenesRetrasadas', 'porProveedor', 'porEstado', 'topProductosComprados',
            'listadoRetrasadas', 'listadoRetrasadasCompleto'
        );
    }

    public function compras(Request $request)
    {
        $datos = $this->datosReporteCompras($request);
        return view('reportes.compras', $datos);
    }

    public function comprasCsv(Request $request)
    {
        $datos = $this->datosReporteCompras($request, paginar: false);

        $filename = 'reporte_compras_' . $datos['desde']->format('Y-m-d') . '_a_' . $datos['hasta']->format('Y-m-d') . '.csv';

        $callback = function () use ($datos) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['Reporte de Compras']);
            fputcsv($handle, ['Período', $datos['desde']->format('d/m/Y') . ' - ' . $datos['hasta']->format('d/m/Y')]);
            fputcsv($handle, []);
            fputcsv($handle, ['Total comprado', number_format($datos['totalComprado'], 2)]);
            fputcsv($handle, ['Cantidad de órdenes', $datos['cantidadOrdenes']]);
            fputcsv($handle, ['Orden promedio', number_format($datos['ordenPromedio'], 2)]);
            fputcsv($handle, ['Órdenes retrasadas (a la fecha)', $datos['ordenesRetrasadas']]);
            fputcsv($handle, []);

            fputcsv($handle, ['Compras por proveedor']);
            fputcsv($handle, ['Proveedor', 'Cantidad', 'Total']);
            foreach ($datos['porProveedor'] as $fila) {
                fputcsv($handle, [$fila->proveedor, $fila->cantidad, number_format($fila->total, 2)]);
            }
            fputcsv($handle, []);

            fputcsv($handle, ['Órdenes por estado']);
            fputcsv($handle, ['Estado', 'Cantidad', 'Total']);
            foreach ($datos['porEstado'] as $fila) {
                fputcsv($handle, [ucfirst($fila->estado), $fila->cantidad, number_format($fila->total, 2)]);
            }
            fputcsv($handle, []);

            fputcsv($handle, ['Top productos comprados']);
            fputcsv($handle, ['Producto', 'Cantidad', 'Total gastado']);
            foreach ($datos['topProductosComprados'] as $fila) {
                fputcsv($handle, [
                    $fila->variante?->producto?->nombre ?? '—',
                    $fila->cantidad_solicitada,
                    number_format($fila->total_gastado, 2),
                ]);
            }
            fputcsv($handle, []);

            fputcsv($handle, ['Órdenes retrasadas']);
            fputcsv($handle, ['Código', 'Proveedor', 'Fecha esperada', 'Días de retraso']);
            foreach ($datos['listadoRetrasadasCompleto'] as $fila) {
                fputcsv($handle, [
                    $fila->codigo,
                    $fila->proveedor?->nombre ?? '—',
                    $fila->fecha_esperada?->format('d/m/Y'),
                    $fila->dias_retraso,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function comprasPdf(Request $request)
    {
        $datos = $this->datosReporteCompras($request, paginar: false);
        $datos['listadoRetrasadas'] = $datos['listadoRetrasadasCompleto'];

        $pdf = Pdf::loadView('reportes.compras_pdf', $datos)->setPaper('letter', 'portrait');

        $filename = 'reporte_compras_' . $datos['desde']->format('Y-m-d') . '_a_' . $datos['hasta']->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /* ============ CRÉDITO Y COBROS ============ */

    private function datosReporteCredito(Request $request, bool $paginar = true): array
    {
        [$desde, $hasta] = $this->rangoFechas($request);

        // Cartera total pendiente (a la fecha de hoy, independiente del filtro)
        $carteraTotal = CuentaPorCobrar::whereNotIn('estado', ['pagada', 'anulada'])->sum('monto_pendiente');

        $carteraVencida = CuentaPorCobrar::whereNotIn('estado', ['pagada', 'anulada'])
            ->where('fecha_vencimiento', '<', now())
            ->sum('monto_pendiente');

        $cantidadCuentasActivas = CuentaPorCobrar::whereNotIn('estado', ['pagada', 'anulada'])->count();

        // Cobrado en el período (abonos registrados en el rango de fechas)
        $cobradoPeriodo = PagoCredito::whereBetween('fecha', [$desde, $hasta])->sum('monto');
        $cantidadAbonos = PagoCredito::whereBetween('fecha', [$desde, $hasta])->count();

        // Antigüedad de cartera vencida (0-30, 31-60, 61+ días)
        $cuentasVencidas = CuentaPorCobrar::whereNotIn('estado', ['pagada', 'anulada'])
            ->where('fecha_vencimiento', '<', now())
            ->get();

        $antiguedad = [
            '0-30' => ['cantidad' => 0, 'monto' => 0],
            '31-60' => ['cantidad' => 0, 'monto' => 0],
            '61+' => ['cantidad' => 0, 'monto' => 0],
        ];

        foreach ($cuentasVencidas as $cuenta) {
            $diasVencida = (int) $cuenta->fecha_vencimiento->diffInDays(now());
            $tramo = $diasVencida <= 30 ? '0-30' : ($diasVencida <= 60 ? '31-60' : '61+');
            $antiguedad[$tramo]['cantidad']++;
            $antiguedad[$tramo]['monto'] += $cuenta->monto_pendiente;
        }

        // Cartera por cliente (todas las cuentas activas, no solo vencidas)
        $porClienteCompleto = CuentaPorCobrar::with('cliente')
            ->whereNotIn('estado', ['pagada', 'anulada'])
            ->orderByDesc('monto_pendiente')
            ->get();

        $porCliente = $paginar
            ? $this->paginarColeccion($porClienteCompleto, 10, 'pagina_cxc')
            : $porClienteCompleto;

        // Historial de abonos del período
        $historialAbonosCompleto = PagoCredito::with('cuenta.cliente', 'tipoPago', 'usuario')
            ->whereBetween('fecha', [$desde, $hasta])
            ->orderByDesc('fecha')
            ->get();

        $historialAbonos = $paginar
            ? $this->paginarColeccion($historialAbonosCompleto, 10, 'pagina_abonos')
            : $historialAbonosCompleto;

        return compact(
            'desde', 'hasta', 'carteraTotal', 'carteraVencida', 'cantidadCuentasActivas',
            'cobradoPeriodo', 'cantidadAbonos', 'antiguedad', 'porCliente', 'porClienteCompleto',
            'historialAbonos', 'historialAbonosCompleto'
        );
    }

    public function credito(Request $request)
    {
        $datos = $this->datosReporteCredito($request);
        return view('reportes.credito', $datos);
    }

    public function creditoCsv(Request $request)
    {
        $datos = $this->datosReporteCredito($request, paginar: false);

        $filename = 'reporte_credito_' . $datos['desde']->format('Y-m-d') . '_a_' . $datos['hasta']->format('Y-m-d') . '.csv';

        $callback = function () use ($datos) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['Reporte de Crédito y Cobros']);
            fputcsv($handle, ['Período (para cobros)', $datos['desde']->format('d/m/Y') . ' - ' . $datos['hasta']->format('d/m/Y')]);
            fputcsv($handle, []);
            fputcsv($handle, ['Cartera total pendiente (a hoy)', number_format($datos['carteraTotal'], 2)]);
            fputcsv($handle, ['Cartera vencida (a hoy)', number_format($datos['carteraVencida'], 2)]);
            fputcsv($handle, ['Cuentas activas', $datos['cantidadCuentasActivas']]);
            fputcsv($handle, ['Cobrado en el período', number_format($datos['cobradoPeriodo'], 2)]);
            fputcsv($handle, ['Cantidad de abonos', $datos['cantidadAbonos']]);
            fputcsv($handle, []);

            fputcsv($handle, ['Antigüedad de cartera vencida']);
            fputcsv($handle, ['Tramo', 'Cantidad', 'Monto']);
            foreach ($datos['antiguedad'] as $tramo => $info) {
                fputcsv($handle, [$tramo . ' días', $info['cantidad'], number_format($info['monto'], 2)]);
            }
            fputcsv($handle, []);

            fputcsv($handle, ['Cartera por cliente']);
            fputcsv($handle, ['Cliente', 'Código', 'Monto total', 'Pendiente', 'Vence', 'Estado']);
            foreach ($datos['porClienteCompleto'] as $fila) {
                fputcsv($handle, [
                    trim(($fila->cliente?->nombre ?? '') . ' ' . ($fila->cliente?->apellido ?? '')) ?: '—',
                    $fila->codigo,
                    number_format($fila->monto_total, 2),
                    number_format($fila->monto_pendiente, 2),
                    $fila->fecha_vencimiento?->format('d/m/Y'),
                    ucfirst($fila->estado),
                ]);
            }
            fputcsv($handle, []);

            fputcsv($handle, ['Historial de abonos del período']);
            fputcsv($handle, ['Fecha', 'Cliente', 'Cuenta', 'Método', 'Monto', 'Registrado por']);
            foreach ($datos['historialAbonosCompleto'] as $fila) {
                fputcsv($handle, [
                    $fila->fecha->format('d/m/Y'),
                    trim(($fila->cuenta?->cliente?->nombre ?? '') . ' ' . ($fila->cuenta?->cliente?->apellido ?? '')) ?: '—',
                    $fila->cuenta?->codigo ?? '—',
                    $fila->tipoPago?->nombre ?? '—',
                    number_format($fila->monto, 2),
                    $fila->usuario?->name ?? '—',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function creditoPdf(Request $request)
    {
        $datos = $this->datosReporteCredito($request, paginar: false);
        $datos['porCliente'] = $datos['porClienteCompleto'];
        $datos['historialAbonos'] = $datos['historialAbonosCompleto'];

        $pdf = Pdf::loadView('reportes.credito_pdf', $datos)->setPaper('letter', 'portrait');

        $filename = 'reporte_credito_' . $datos['desde']->format('Y-m-d') . '_a_' . $datos['hasta']->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}