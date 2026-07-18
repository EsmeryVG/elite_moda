@extends('layouts.app')
@section('page_title', 'Reporte de Compras')
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
                    <a href="{{ route('reportes.compras.csv', request()->query()) }}" class="btn btn-outline-primary">
                        <i class="bi bi-filetype-csv me-1"></i> Exportar CSV
                    </a>
                    <a href="{{ route('reportes.compras.pdf', request()->query()) }}" target="_blank"
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
                <div class="dash-kpi-label">Total comprado</div>
                <div class="dash-kpi-valor">RD$ {{ number_format($totalComprado, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Cantidad de órdenes</div>
                <div class="dash-kpi-valor">{{ $cantidadOrdenes }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Orden promedio</div>
                <div class="dash-kpi-valor">RD$ {{ number_format($ordenPromedio, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Retrasadas (hoy)</div>
                <div class="dash-kpi-valor" style="color:{{ $ordenesRetrasadas > 0 ? 'var(--accent)' : 'inherit' }};">
                    {{ $ordenesRetrasadas }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Compras por proveedor</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Proveedor</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($porProveedor as $fila)
                                    <tr>
                                        <td style="font-size:13px;">{{ $fila->proveedor }}</td>
                                        <td class="text-center" style="font-size:13px;">{{ $fila->cantidad }}</td>
                                        <td class="text-end fw-semibold" style="font-size:13px;">RD$
                                            {{ number_format($fila->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Sin compras en el período.
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
                    <h6 class="fw-semibold mb-3">Órdenes por estado</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Estado</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($porEstado as $fila)
                                    <tr>
                                        <td style="font-size:13px;">{{ ucfirst($fila->estado) }}</td>
                                        <td class="text-center" style="font-size:13px;">{{ $fila->cantidad }}</td>
                                        <td class="text-end fw-semibold" style="font-size:13px;">RD$
                                            {{ number_format($fila->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Sin órdenes en el período.
                                        </td>
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
                    <h6 class="fw-semibold mb-3">Top 10 productos comprados</h6>
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
                                @forelse($topProductosComprados as $fila)
                                    <tr>
                                        <td style="font-size:13px;">{{ $fila->variante?->producto?->nombre ?? '—' }}</td>
                                        <td class="text-center" style="font-size:13px;">{{ $fila->cantidad_solicitada }}
                                        </td>
                                        <td class="text-end fw-semibold" style="font-size:13px;">RD$
                                            {{ number_format($fila->total_gastado, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Sin datos en el período.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card page-card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Órdenes retrasadas</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Proveedor</th>
                                    <th class="text-center">Días</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($listadoRetrasadas as $fila)
                                    <tr>
                                        <td style="font-size:13px; font-family:monospace;">{{ $fila->codigo }}</td>
                                        <td style="font-size:12px; color:var(--text-muted);">
                                            {{ $fila->proveedor?->nombre ?? '—' }}</td>
                                        <td class="text-center fw-semibold" style="font-size:13px; color:var(--accent);">
                                            {{ $fila->dias_retraso }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">No hay órdenes retrasadas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($listadoRetrasadas->hasPages())
                        <div class="d-flex justify-content-between align-items-center pt-3 mt-2"
                            style="border-top:1px solid var(--border);">
                            <span style="font-size:12px; color:var(--text-muted);">
                                {{ $listadoRetrasadas->firstItem() }}–{{ $listadoRetrasadas->lastItem() }} de
                                {{ $listadoRetrasadas->total() }}
                            </span>
                            <nav>
                                <ul class="pagination pagination-sm mb-0 em-pagination">
                                    <li class="page-item {{ $listadoRetrasadas->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $listadoRetrasadas->previousPageUrl() }}">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                    @foreach ($listadoRetrasadas->getUrlRange(1, $listadoRetrasadas->lastPage()) as $page => $url)
                                        <li
                                            class="page-item {{ $page == $listadoRetrasadas->currentPage() ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endforeach
                                    <li class="page-item {{ !$listadoRetrasadas->hasMorePages() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $listadoRetrasadas->nextPageUrl() }}">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
