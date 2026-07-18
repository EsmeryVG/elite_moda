@extends('layouts.app')
@section('page_title', 'Reporte de Crédito y Cobros')
@section('page_subtitle', 'Cartera a hoy — Cobros del ' . $desde->format('d/m/Y') . ' al ' . $hasta->format('d/m/Y'))
@section('content')

    <div class="card page-card mb-4">
        <div class="card-body p-4">
            <form method="GET" class="d-flex gap-2 align-items-end flex-wrap">
                <div>
                    <label class="form-label" style="font-size:12px;">Cobros desde</label>
                    <input type="date" name="desde" class="form-control" value="{{ $desde->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="form-label" style="font-size:12px;">Cobros hasta</label>
                    <input type="date" name="hasta" class="form-control" value="{{ $hasta->format('Y-m-d') }}">
                </div>
                <button type="submit" class="btn btn-secondary">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('reportes.credito.csv', request()->query()) }}" class="btn btn-outline-primary">
                        <i class="bi bi-filetype-csv me-1"></i> Exportar CSV
                    </a>
                    <a href="{{ route('reportes.credito.pdf', request()->query()) }}" target="_blank"
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
                <div class="dash-kpi-label">Cartera total (a hoy)</div>
                <div class="dash-kpi-valor">RD$ {{ number_format($carteraTotal, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Cartera vencida (a hoy)</div>
                <div class="dash-kpi-valor" style="color:{{ $carteraVencida > 0 ? 'var(--accent)' : 'inherit' }};">RD$
                    {{ number_format($carteraVencida, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Cobrado en el período</div>
                <div class="dash-kpi-valor" style="color:#2e7d32;">RD$ {{ number_format($cobradoPeriodo, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Cuentas activas</div>
                <div class="dash-kpi-valor">{{ $cantidadCuentasActivas }}</div>
            </div>
        </div>
    </div>

    <div class="card page-card mb-4">
        <div class="card-body p-4">
            <h6 class="fw-semibold mb-3">Antigüedad de cartera vencida</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="dash-kpi-card">
                        <div class="dash-kpi-label">0–30 días</div>
                        <div class="dash-kpi-valor">RD$ {{ number_format($antiguedad['0-30']['monto'], 2) }}</div>
                        <div style="font-size:11px; color:var(--text-muted);">{{ $antiguedad['0-30']['cantidad'] }} cuentas
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dash-kpi-card">
                        <div class="dash-kpi-label" style="color:#e65100;">31–60 días</div>
                        <div class="dash-kpi-valor" style="color:#e65100;">RD$
                            {{ number_format($antiguedad['31-60']['monto'], 2) }}</div>
                        <div style="font-size:11px; color:var(--text-muted);">{{ $antiguedad['31-60']['cantidad'] }}
                            cuentas</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dash-kpi-card">
                        <div class="dash-kpi-label" style="color:var(--accent);">61+ días</div>
                        <div class="dash-kpi-valor" style="color:var(--accent);">RD$
                            {{ number_format($antiguedad['61+']['monto'], 2) }}</div>
                        <div style="font-size:11px; color:var(--text-muted);">{{ $antiguedad['61+']['cantidad'] }} cuentas
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card page-card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Cartera por cliente</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th class="text-end">Pendiente</th>
                                    <th>Vence</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($porCliente as $fila)
                                    <tr>
                                        <td style="font-size:13px;">
                                            {{ trim(($fila->cliente?->nombre ?? '') . ' ' . ($fila->cliente?->apellido ?? '')) ?: '—' }}
                                        </td>
                                        <td class="text-end fw-semibold" style="font-size:13px;">RD$
                                            {{ number_format($fila->monto_pendiente, 2) }}</td>
                                        <td
                                            style="font-size:12px; color:{{ $fila->estaVencida() ? 'var(--accent)' : 'var(--text-muted)' }};">
                                            {{ $fila->fecha_vencimiento?->format('d/m/Y') }}</td>
                                        <td>
                                            @php
                                                $badge = match ($fila->estado) {
                                                    'pendiente' => 'badge-cpc-pendiente',
                                                    'parcial' => 'badge-cpc-parcial',
                                                    'vencida' => 'badge-cpc-vencida',
                                                    default => 'badge-cpc-pendiente',
                                                };
                                            @endphp
                                            <span class="{{ $badge }}">{{ ucfirst($fila->estado) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No hay cuentas activas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($porCliente->hasPages())
                        <div class="d-flex justify-content-between align-items-center pt-3 mt-2"
                            style="border-top:1px solid var(--border);">
                            <span style="font-size:12px; color:var(--text-muted);">
                                {{ $porCliente->firstItem() }}–{{ $porCliente->lastItem() }} de
                                {{ $porCliente->total() }}
                            </span>
                            <nav>
                                <ul class="pagination pagination-sm mb-0 em-pagination">
                                    <li class="page-item {{ $porCliente->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $porCliente->previousPageUrl() }}">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                    @foreach ($porCliente->getUrlRange(1, $porCliente->lastPage()) as $page => $url)
                                        <li class="page-item {{ $page == $porCliente->currentPage() ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endforeach
                                    <li class="page-item {{ !$porCliente->hasMorePages() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $porCliente->nextPageUrl() }}">
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
            <div class="card page-card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Historial de abonos del período</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Método</th>
                                    <th class="text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historialAbonos as $fila)
                                    <tr>
                                        <td style="font-size:12px; color:var(--text-muted);">
                                            {{ $fila->fecha->format('d/m/Y') }}</td>
                                        <td style="font-size:13px;">
                                            {{ trim(($fila->cuenta?->cliente?->nombre ?? '') . ' ' . ($fila->cuenta?->cliente?->apellido ?? '')) ?: '—' }}
                                        </td>
                                        <td style="font-size:12px; color:var(--text-muted);">
                                            {{ $fila->tipoPago?->nombre ?? '—' }}</td>
                                        <td class="text-end fw-semibold" style="font-size:13px; color:#2e7d32;">RD$
                                            {{ number_format($fila->monto, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Sin abonos en el período.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($historialAbonos->hasPages())
                        <div class="d-flex justify-content-between align-items-center pt-3 mt-2"
                            style="border-top:1px solid var(--border);">
                            <span style="font-size:12px; color:var(--text-muted);">
                                {{ $historialAbonos->firstItem() }}–{{ $historialAbonos->lastItem() }} de
                                {{ $historialAbonos->total() }}
                            </span>
                            <nav>
                                <ul class="pagination pagination-sm mb-0 em-pagination">
                                    <li class="page-item {{ $historialAbonos->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $historialAbonos->previousPageUrl() }}">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                    @foreach ($historialAbonos->getUrlRange(1, $historialAbonos->lastPage()) as $page => $url)
                                        <li
                                            class="page-item {{ $page == $historialAbonos->currentPage() ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endforeach
                                    <li class="page-item {{ !$historialAbonos->hasMorePages() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $historialAbonos->nextPageUrl() }}">
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
