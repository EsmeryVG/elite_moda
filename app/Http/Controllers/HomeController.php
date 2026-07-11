<?php

namespace App\Http\Controllers;

use App\Models\OrdenCompra;
use App\Models\Stock;
use App\Models\Venta;
use App\Models\DetalleVenta;
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

        // ── Últimas ventas ───────────────────────────
        $ultimasVentas = Venta::with(['cliente', 'usuario'])
            ->where('estado', 'completada')
            ->orderBy('fecha', 'desc')
            ->limit(5)
            ->get();

        // ── Ventas últimos 7 días (gráfico) ──────────
        $ventasUltimos7 = collect(range(6, 0))->map(function ($diasAtras) {
            $fecha = now()->subDays($diasAtras);
            $total = Venta::where('estado', 'completada')
                ->whereDate('fecha', $fecha)
                ->sum('total');
            return [
                'fecha'  => $fecha->format('d/m'),
                'total'  => round((float) $total, 2),
            ];
        });

        // ── Top 5 productos del mes (gráfico) ────────
        $topProductos = DetalleVenta::select('variante_producto_id', DB::raw('SUM(cantidad) as total_vendido'))
            ->whereHas('venta', fn($q) =>
                $q->where('estado', 'completada')
                  ->whereMonth('fecha', now()->month)
                  ->whereYear('fecha', now()->year)
            )
            ->with('variante.producto')
            ->groupBy('variante_producto_id')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get()
            ->map(fn($d) => [
                'nombre' => $d->variante?->producto?->nombre ?? '—',
                'total'  => (int) $d->total_vendido,
            ]);

        return view('home', compact(
            'ventasHoy', 'ingresosHoy', 'productosVendidosHoy',
            'ventasSemana', 'ventasMes', 'ticketPromedio',
            'stockAgotado', 'stockCritico', 'ordenesRetrasadas',
            'ultimasVentas', 'ventasUltimos7', 'topProductos'
        ));
    }
}