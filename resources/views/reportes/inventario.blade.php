@extends('layouts.app')
@section('page_title', 'Reporte de Inventario')
@section('page_subtitle', 'Valorización y estado del stock actual')
@section('content')

    <div class="card page-card mb-4">
        <div class="card-body p-4">
            <form method="GET" class="d-flex gap-2 align-items-end flex-wrap">
                <div>
                    <label class="form-label" style="font-size:12px;">Almacén</label>
                    <select name="almacen_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($almacenes as $almacen)
                            <option value="{{ $almacen->id }}" {{ $almacenId == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" style="font-size:12px;">Sin movimiento (días)</label>
                    <input type="number" name="dias_sin_movimiento" class="form-control" style="width:110px;"
                        value="{{ $diasSinMovimiento }}" min="1">
                </div>

                <button type="submit" class="btn btn-secondary">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('reportes.inventario.csv', request()->query()) }}" class="btn btn-outline-primary">
                        <i class="bi bi-filetype-csv me-1"></i> Exportar CSV
                    </a>
                    <a href="{{ route('reportes.inventario.pdf', request()->query()) }}" target="_blank"
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
                <div class="dash-kpi-label">Valor (costo)</div>
                <div class="dash-kpi-valor">RD$ {{ number_format($valorTotalCosto, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Valor (precio de venta)</div>
                <div class="dash-kpi-valor">RD$ {{ number_format($valorTotalVenta, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Utilidad potencial</div>
                <div class="dash-kpi-valor" style="color:#2e7d32;">RD$ {{ number_format($utilidadPotencial, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">SKUs activos</div>
                <div class="dash-kpi-valor">{{ $cantidadSkusActivos }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">En stock crítico</div>
                <div class="dash-kpi-valor" style="color:#e65100;">{{ $stockCritico }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Agotados</div>
                <div class="dash-kpi-valor" style="color:var(--accent);">{{ $stockAgotado }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Valorización por almacén</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Almacén</th>
                                    <th class="text-center">Unid.</th>
                                    <th class="text-end">Costo</th>
                                    <th class="text-end">Venta</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($valorizacionPorAlmacen as $fila)
                                    <tr>
                                        <td style="font-size:13px;">{{ $fila->almacen }}</td>
                                        <td class="text-center" style="font-size:13px;">{{ $fila->unidades }}</td>
                                        <td class="text-end" style="font-size:12px;">RD$
                                            {{ number_format($fila->valor_costo, 2) }}</td>
                                        <td class="text-end fw-semibold" style="font-size:12px;">RD$
                                            {{ number_format($fila->valor_venta, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Sin datos.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Valorización por categoría</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Categoría</th>
                                    <th class="text-center">Unid.</th>
                                    <th class="text-end">Costo</th>
                                    <th class="text-end">Venta</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($valorizacionPorCategoria as $fila)
                                    <tr>
                                        <td style="font-size:13px;">{{ $fila->categoria }}</td>
                                        <td class="text-center" style="font-size:13px;">{{ $fila->unidades }}</td>
                                        <td class="text-end" style="font-size:12px;">RD$
                                            {{ number_format($fila->valor_costo, 2) }}</td>
                                        <td class="text-end fw-semibold" style="font-size:12px;">RD$
                                            {{ number_format($fila->valor_venta, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Sin datos.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card page-card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Sin movimiento en {{ $diasSinMovimiento }}+ días</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Código</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sinMovimiento as $fila)
                                    <tr>
                                        <td style="font-size:13px;">{{ $fila->producto?->nombre ?? '—' }}</td>
                                        <td style="font-size:12px; color:var(--text-muted); font-family:monospace;">
                                            {{ $fila->codigo }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-3">Todo el catálogo tiene
                                            movimiento reciente.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($sinMovimiento->hasPages())
                        <div class="d-flex justify-content-between align-items-center pt-3 mt-2"
                            style="border-top:1px solid var(--border);">
                            <span style="font-size:12px; color:var(--text-muted);">
                                {{ $sinMovimiento->firstItem() }}–{{ $sinMovimiento->lastItem() }} de
                                {{ $sinMovimiento->total() }}
                            </span>
                            <nav>
                                <ul class="pagination pagination-sm mb-0 em-pagination">
                                    <li class="page-item {{ $sinMovimiento->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $sinMovimiento->previousPageUrl() }}">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                    @foreach ($sinMovimiento->getUrlRange(1, $sinMovimiento->lastPage()) as $page => $url)
                                        <li
                                            class="page-item {{ $page == $sinMovimiento->currentPage() ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endforeach
                                    <li class="page-item {{ !$sinMovimiento->hasMorePages() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $sinMovimiento->nextPageUrl() }}">
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

        <div class="col-lg-6">
            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Stock crítico y agotado</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Almacén</th>
                                    <th class="text-center">Disp.</th>
                                    <th class="text-center">Mín.</th>
                                    <th>Nivel</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($listadoCritico as $fila)
                                    <tr>
                                        <td style="font-size:13px;">{{ $fila->variante?->producto?->nombre ?? '—' }}</td>
                                        <td style="font-size:12px; color:var(--text-muted);">
                                            {{ $fila->almacen?->nombre ?? '—' }}</td>
                                        <td class="text-center" style="font-size:13px;">{{ $fila->cantidad_disponible }}
                                        </td>
                                        <td class="text-center" style="font-size:12px; color:var(--text-muted);">
                                            {{ $fila->stock_minimo }}</td>
                                        <td>
                                            @if ($fila->nivel === 'agotado')
                                                <span style="color:var(--accent); font-size:12px;"><i
                                                        class="bi bi-x-circle me-1"></i>Agotado</span>
                                            @else
                                                <span style="color:#e65100; font-size:12px;"><i
                                                        class="bi bi-exclamation-triangle me-1"></i>Crítico</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">No hay productos en stock
                                            crítico o agotado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($listadoCritico->hasPages())
                        <div class="d-flex justify-content-between align-items-center pt-3 mt-2"
                            style="border-top:1px solid var(--border);">
                            <span style="font-size:12px; color:var(--text-muted);">
                                {{ $listadoCritico->firstItem() }}–{{ $listadoCritico->lastItem() }} de
                                {{ $listadoCritico->total() }}
                            </span>
                            <nav>
                                <ul class="pagination pagination-sm mb-0 em-pagination">
                                    <li class="page-item {{ $listadoCritico->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $listadoCritico->previousPageUrl() }}">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                    @foreach ($listadoCritico->getUrlRange(1, $listadoCritico->lastPage()) as $page => $url)
                                        <li
                                            class="page-item {{ $page == $listadoCritico->currentPage() ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endforeach
                                    <li class="page-item {{ !$listadoCritico->hasMorePages() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $listadoCritico->nextPageUrl() }}">
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
                    <h6 class="fw-semibold mb-3">Top 10 productos por valor en inventario</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Unidades</th>
                                    <th class="text-end">Valor (costo)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topValorInventario as $fila)
                                    <tr>
                                        <td style="font-size:13px;">{{ $fila->variante?->producto?->nombre ?? '—' }}</td>
                                        <td class="text-center" style="font-size:13px;">{{ $fila->unidades }}</td>
                                        <td class="text-end fw-semibold" style="font-size:13px;">RD$
                                            {{ number_format($fila->valor_costo, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Sin datos.</td>
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
