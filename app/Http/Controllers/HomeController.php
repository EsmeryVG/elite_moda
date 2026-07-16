<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenCompra;
use App\Models\SesionCaja;
use App\Models\Stock;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\CajaChica;
use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{
    public function index()
    {
        // ── KPIs del día ────────────────────────────
        $ventasHoy = Venta::where('estado', 'completada')
            ->whereDate('fecha', today())
            ->count();

        $ingresosHoy = Venta::where('estado', 'completada')
            ->whereDate('fecha', today())
            ->sum('total');

        $productosVendidosHoy = DetalleVenta::whereHas('venta', fn($q) =>
            $q->where('estado', 'completada')->whereDate('fecha', today())
        )->sum('cantidad');

        // ── KPIs de la semana y mes ──────────────────
        $ventasSemana = Venta::where('estado', 'completada')
            ->whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total');

        $ventasMes = Venta::where('estado', 'completada')
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->sum('total');

        $ticketPromedio = Venta::where('estado', 'completada')
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->avg('total') ?? 0;

        // ── Alertas ──────────────────────────────────
        $stockAgotado = Stock::where('cantidad_disponible', '<=', 0)->count();

        $stockCritico = Stock::where('cantidad_disponible', '>', 0)
            ->whereColumn('cantidad_disponible', '<=', 'stock_minimo')
            ->count();

        $ordenesRetrasadas = OrdenCompra::whereIn('estado', ['confirmada', 'parcial'])
            ->whereNotNull('fecha_esperada')
            ->where('fecha_esperada', '<', today())
            ->count();

        $diferenciasCajaPendientes = SesionCaja::pendientesRevision()->count();

        $cajaChica = CajaChica::activas()->first();
        $cajaChicaBaja = $cajaChica && $cajaChica->monto_disponible < $cajaChica->monto_base;
        $cajaChicaAgotada = $cajaChica && $cajaChica->monto_disponible <= 0;

        // ── Últimas ventas ───────────────────────────
        $ultimasVentas = Venta::with(['cliente', 'usuario'])
            ->where('estado', 'completada')
            ->orderBy('fecha', 'desc')
            ->limit(5)
            ->get();


        return view('home', compact(
            'ventasHoy', 'ingresosHoy', 'productosVendidosHoy',
            'ventasSemana', 'ventasMes', 'ticketPromedio',
            'stockAgotado', 'stockCritico', 'ordenesRetrasadas', 'diferenciasCajaPendientes',
            'ultimasVentas', 'cajaChicaBaja', 'cajaChicaAgotada',
            'cajaChica'
        ));
    }

    public static function obtenerAlertas(): array
{
    $stockAgotado = \App\Models\Stock::where('cantidad_disponible', '<=', 0)->count();
    $stockCritico = \App\Models\Stock::where('cantidad_disponible', '>', 0)
        ->whereColumn('cantidad_disponible', '<=', 'stock_minimo')->count();
    $ordenesRetrasadas = \App\Models\OrdenCompra::whereIn('estado', ['confirmada', 'parcial'])
        ->whereNotNull('fecha_esperada')->where('fecha_esperada', '<', today())->count();
    $diferenciasCajaPendientes = \App\Models\SesionCaja::pendientesRevision()->count();
    $cajaChica = \App\Models\CajaChica::activas()->first();
    $cajaChicaAgotada = $cajaChica && $cajaChica->monto_disponible <= 0;
    $cajaChicaBaja = $cajaChica && $cajaChica->monto_disponible < $cajaChica->monto_base;

    $alertas = [];

    if ($stockAgotado > 0) {
        $alertas[] = ['icono' => 'x-circle', 'texto' => "{$stockAgotado} " . ($stockAgotado === 1 ? 'variante agotada' : 'variantes agotadas'), 'url' => route('stock.index', ['nivel' => 'agotado']), 'tipo' => 'danger'];
    }
    if ($stockCritico > 0) {
        $alertas[] = ['icono' => 'exclamation-triangle', 'texto' => "{$stockCritico} " . ($stockCritico === 1 ? 'variante en nivel crítico' : 'variantes en nivel crítico'), 'url' => route('stock.index', ['nivel' => 'critico']), 'tipo' => 'warning'];
    }
    if ($ordenesRetrasadas > 0) {
        $alertas[] = ['icono' => 'clock-history', 'texto' => "{$ordenesRetrasadas} " . ($ordenesRetrasadas === 1 ? 'orden retrasada' : 'órdenes retrasadas'), 'url' => route('ordenes_compra.index', ['estado' => 'confirmada']), 'tipo' => 'warning'];
    }
    if ($diferenciasCajaPendientes > 0) {
        $alertas[] = ['icono' => 'calculator', 'texto' => "{$diferenciasCajaPendientes} " . ($diferenciasCajaPendientes === 1 ? 'diferencia de caja pendiente' : 'diferencias de caja pendientes'), 'url' => route('sesiones_caja.pendientes_revision'), 'tipo' => 'danger'];
    }
    if ($cajaChicaAgotada) {
        $alertas[] = ['icono' => 'cash-stack', 'texto' => 'Caja chica agotada', 'url' => route('caja_chica.show'), 'tipo' => 'danger'];
    } elseif ($cajaChicaBaja) {
        $alertas[] = ['icono' => 'cash-stack', 'texto' => 'Caja chica requiere reposición', 'url' => route('caja_chica.show'), 'tipo' => 'warning'];
    }

    return $alertas;
}
public function graficoDatos(Request $request)
    {
        [$desde, $hasta] = $this->resolverRango($request);

        $ventasPorDia = collect();
        $periodo = \Carbon\CarbonPeriod::create($desde->copy()->startOfDay(), $hasta->copy()->startOfDay());

        foreach ($periodo as $fecha) {
            $total = Venta::where('estado', 'completada')
                ->whereDate('fecha', $fecha)
                ->sum('total');

            $ventasPorDia->push([
                'fecha' => $fecha->format('d/m'),
                'total' => round((float) $total, 2),
            ]);
        }

        $topProductos = DetalleVenta::select('variante_producto_id', DB::raw('SUM(cantidad) as total_vendido'))
            ->whereHas('venta', fn ($q) =>
                $q->where('estado', 'completada')->whereBetween('fecha', [$desde, $hasta])
            )
            ->with('variante.producto')
            ->groupBy('variante_producto_id')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get()
            ->map(fn ($d) => [
                'nombre' => $d->variante?->producto?->nombre ?? '—',
                'total'  => (int) $d->total_vendido,
            ]);

        $ventasPorCategoria = DetalleVenta::whereHas('venta', fn ($q) =>
                $q->where('estado', 'completada')->whereBetween('fecha', [$desde, $hasta])
            )
            ->with('variante.producto.categoria')
            ->get()
            ->groupBy(fn ($d) => $d->variante?->producto?->categoria?->nombre ?? 'Sin categoría')
            ->map(fn ($grupo, $nombre) => [
                'nombre' => $nombre,
                'total'  => round((float) $grupo->sum('subtotal'), 2),
            ])
            ->sortByDesc('total')
            ->values()
            ->take(5);

        return response()->json([
            'ventas' => $ventasPorDia,
            'topProductos' => $topProductos,
            'ventasPorCategoria' => $ventasPorCategoria,
        ]);
    }

    private function resolverRango(Request $request): array
    {
        $rango = $request->get('rango', '7dias');

        return match ($rango) {
            'hoy'   => [now()->startOfDay(), now()->endOfDay()],
            'mes'   => [now()->startOfMonth(), now()->endOfDay()],
            'personalizado' => [
                \Carbon\Carbon::parse($request->get('desde', now()->subDays(6)))->startOfDay(),
                \Carbon\Carbon::parse($request->get('hasta', now()))->endOfDay(),
            ],
            default => [now()->subDays(6)->startOfDay(), now()->endOfDay()], // 7dias
        };
    }
}