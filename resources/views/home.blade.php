@extends('layouts.app')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Resumen general — ' . now()->format('d/m/Y'))

@section('content')

    {{-- ── Fila 1: KPIs del día ── --}}
    <div class="dash-section-title">Hoy — {{ now()->format('d/m/Y') }}</div>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Ventas realizadas</div>
                <div class="dash-kpi-valor">{{ $ventasHoy }}</div>
                <div class="dash-kpi-sub">transacciones hoy</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Ingresos del día</div>
                <div class="dash-kpi-valor">RD$ {{ number_format($ingresosHoy, 0, '.', ',') }}</div>
                <div class="dash-kpi-sub">en ventas completadas</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Unidades vendidas</div>
                <div class="dash-kpi-valor">{{ $productosVendidosHoy }}</div>
                <div class="dash-kpi-sub">productos hoy</div>
            </div>
        </div>
    </div>

    {{-- ── Fila 2: KPIs semana/mes ── --}}
    <div class="dash-section-title">Este período</div>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Ventas esta semana</div>
                <div class="dash-kpi-valor">RD$ {{ number_format($ventasSemana, 0, '.', ',') }}</div>
                <div class="dash-kpi-sub">{{ now()->startOfWeek()->format('d/m') }} —
                    {{ now()->endOfWeek()->format('d/m') }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Ventas este mes</div>
                <div class="dash-kpi-valor">RD$ {{ number_format($ventasMes, 0, '.', ',') }}</div>
                <div class="dash-kpi-sub">{{ now()->format('F Y') }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Venta promedio</div>
                <div class="dash-kpi-valor">RD$ {{ number_format($ticketPromedio, 0, '.', ',') }}</div>
                <div class="dash-kpi-sub">por transacción este mes</div>
            </div>
        </div>
    </div>

    {{-- ── Fila 3: Alertas ── --}}
    <div class="dash-section-title">Alertas operacionales</div>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('stock.index', ['nivel' => 'agotado']) }}"
                class="dash-alerta-card {{ $stockAgotado > 0 ? 'agotado' : 'sin-alertas' }}">
                <i class="bi bi-x-circle dash-alerta-icono"></i>
                <div>
                    <div class="dash-alerta-numero">{{ $stockAgotado }}</div>
                    <div class="dash-alerta-texto">
                        {{ $stockAgotado === 1 ? 'variante agotada' : 'variantes agotadas' }}
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('stock.index', ['nivel' => 'critico']) }}"
                class="dash-alerta-card {{ $stockCritico > 0 ? 'critico' : 'sin-alertas' }}">
                <i class="bi bi-exclamation-triangle dash-alerta-icono"></i>
                <div>
                    <div class="dash-alerta-numero">{{ $stockCritico }}</div>
                    <div class="dash-alerta-texto">
                        {{ $stockCritico === 1 ? 'variante en nivel crítico' : 'variantes en nivel crítico' }}
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('ordenes_compra.index', ['estado' => 'confirmada']) }}"
                class="dash-alerta-card {{ $ordenesRetrasadas > 0 ? 'retrasada' : 'sin-alertas' }}">
                <i class="bi bi-clock-history dash-alerta-icono"></i>
                <div>
                    <div class="dash-alerta-numero">{{ $ordenesRetrasadas }}</div>
                    <div class="dash-alerta-texto">
                        {{ $ordenesRetrasadas === 1 ? 'orden retrasada' : 'órdenes retrasadas' }}
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- ── Fila 4: Gráficos ── --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card page-card h-100">
                <div class="card-body p-4">
                    <div class="dash-section-title">Ventas últimos 7 días</div>
                    <canvas id="chartVentas7Dias" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card page-card h-100">
                <div class="card-body p-4">
                    <div class="dash-section-title">Top 5 productos del mes</div>
                    <canvas id="chartTopProductos" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Fila 5: Últimas ventas ── --}}
    <div class="card page-card">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="dash-section-title mb-0">Últimas ventas</div>
                <a href="{{ route('ventas.index') }}" style="font-size:12.5px; color:var(--text-muted);">
                    Ver todas <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            @if ($ultimasVentas->isEmpty())
                <p class="text-center py-4" style="color:var(--text-muted); font-size:13px;">
                    No hay ventas registradas hoy.
                </p>
            @else
                <table class="dash-ventas-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Cliente</th>
                            <th>Cajero</th>
                            <th>Hora</th>
                            <th style="text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ultimasVentas as $venta)
                            <tr style="cursor:pointer;" onclick="window.location='{{ route('ventas.show', $venta) }}'">
                                <td style="font-family:monospace; font-size:12px;">
                                    {{ $venta->codigo }}
                                </td>
                                <td>{{ $venta->cliente?->nombre }} {{ $venta->cliente?->apellido }}</td>
                                <td style="color:var(--text-muted);">{{ $venta->usuario?->name ?? '—' }}</td>
                                <td style="color:var(--text-muted);">{{ $venta->fecha->format('H:i') }}</td>
                                <td style="text-align:right; font-weight:600;">
                                    RD$ {{ number_format($venta->total, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

@endsection

@push('styles')
    @vite(['resources/css/dashboard.css'])
@endpush

@push('scripts')
    @vite(['resources/js/dashboard.js'])
    <script>
        const ventasLabels = @json($ventasUltimos7->pluck('fecha'));
        const ventasTotales = @json($ventasUltimos7->pluck('total'));
        const topProductosLabels = @json($topProductos->pluck('nombre'));
        const topProductosTotales = @json($topProductos->pluck('total'));
    </script>
@endpush
