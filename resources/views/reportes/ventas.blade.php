@extends('layouts.app')
@section('page_title', 'Reporte de Ventas')
@section('page_subtitle', $desde->format('d/m/Y') . ' — ' . $hasta->format('d/m/Y'))
@section('content')
    <div class="card page-card mb-4">
        <div class="card-body p-4">
            <form method="GET" class="d-flex gap-2 align-items-end flex-wrap">
                <div>
                    <label class="form-label" style="font-size:12px;">Desde</label>
                    <input type="date" name="desde" class="form-control" value="{{ $desde->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="form-label" style="font-size:12px;">Hasta</label>
                    <input type="date" name="hasta" class="form-control" value="{{ $hasta->format('Y-m-d') }}">
                </div>
                <button type="submit" class="btn btn-secondary">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('reportes.ventas.csv', request()->query()) }}" class="btn btn-outline-primary">
                        <i class="bi bi-filetype-csv me-1"></i> Exportar CSV
                    </a>
                    <a href="{{ route('reportes.ventas.pdf', request()->query()) }}" target="_blank"
                        class="btn btn-outline-danger">
                        <i class="bi bi-filetype-pdf me-1"></i> Exportar PDF
                    </a>
                </div>
            </form>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Total vendido</div>
                <div class="dash-kpi-valor">RD$ {{ number_format($totalVendido, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Cantidad de ventas</div>
                <div class="dash-kpi-valor">{{ $cantidadVentas }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Venta promedio</div>
                <div class="dash-kpi-valor">RD$ {{ number_format($ticketPromedio, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">ITBIS cobrado</div>
                <div class="dash-kpi-valor">RD$ {{ number_format($totalItbis, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Ventas por día</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ventasPorDia as $fila)
                                    <tr>
                                        <td style="font-size:13px;">{{ \Carbon\Carbon::parse($fila->dia)->format('d/m/Y') }}
                                        </td>
                                        <td class="text-center" style="font-size:13px;">{{ $fila->cantidad }}</td>
                                        <td class="text-end fw-semibold" style="font-size:13px;">RD$
                                            {{ number_format($fila->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Sin ventas en el período.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($ventasPorDia->hasPages())
                        <div class="d-flex justify-content-between align-items-center pt-3 mt-2"
                            style="border-top:1px solid var(--border);">
                            <span style="font-size:12px; color:var(--text-muted);">
                                {{ $ventasPorDia->firstItem() }}–{{ $ventasPorDia->lastItem() }} de
                                {{ $ventasPorDia->total() }} días
                            </span>
                            <nav>
                                <ul class="pagination pagination-sm mb-0 em-pagination">
                                    <li class="page-item {{ $ventasPorDia->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $ventasPorDia->previousPageUrl() }}">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                    @foreach ($ventasPorDia->getUrlRange(1, $ventasPorDia->lastPage()) as $page => $url)
                                        <li class="page-item {{ $page == $ventasPorDia->currentPage() ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endforeach
                                    <li class="page-item {{ !$ventasPorDia->hasMorePages() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $ventasPorDia->nextPageUrl() }}">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card page-card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Por método de pago</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Método</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($porMetodoPago as $fila)
                                    <tr>
                                        <td style="font-size:13px;">{{ $fila->metodo }}</td>
                                        <td class="text-center" style="font-size:13px;">{{ $fila->cantidad }}</td>
                                        <td class="text-end fw-semibold" style="font-size:13px;">RD$
                                            {{ number_format($fila->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Sin pagos en el período.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Top 10 productos vendidos</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProductos as $fila)
                                    <tr>
                                        <td style="font-size:13px;">{{ $fila->variante?->producto?->nombre ?? '—' }}</td>
                                        <td class="text-center" style="font-size:13px;">{{ $fila->cantidad_vendida }}</td>
                                        <td class="text-end fw-semibold" style="font-size:13px;">RD$
                                            {{ number_format($fila->total_vendido, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Sin datos en el período.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card page-card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Por vendedor</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Vendedor</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($porVendedor as $fila)
                                    <tr>
                                        <td style="font-size:13px;">{{ $fila->empleado?->nombre_completo ?? '—' }}</td>
                                        <td class="text-center" style="font-size:13px;">{{ $fila->cantidad }}</td>
                                        <td class="text-end fw-semibold" style="font-size:13px;">RD$
                                            {{ number_format($fila->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Sin ventas asignadas a
                                            vendedor.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
