@extends('layouts.app')

@section('page_title', 'Cuadre Diario')
@section('page_subtitle', 'Resumen de todas las cajas — ' . $fecha->format('d/m/Y'))

@section('content')
    <div class="card page-card w-100 mb-4">
        <div class="card-body p-4">
            <form method="GET" class="d-flex gap-2 align-items-end">
                <div>
                    <label class="form-label" style="font-size:12px;">Fecha</label>
                    <input type="date" name="fecha" class="form-control" value="{{ $fecha->format('Y-m-d') }}">
                </div>
                <button type="submit" class="btn btn-secondary">
                    <i class="bi bi-search me-1"></i> Ver
                </button>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a href="{{ route('sesiones_caja.index', ['estado' => 'cerrada']) }}" class="dash-kpi-card"
                style="text-decoration:none; display:block;">
                <div class="dash-kpi-label">Sesiones cerradas</div>
                <div class="dash-kpi-valor">{{ $sesiones->count() }}</div>
                <div class="dash-kpi-sub">ver todas <i class="bi bi-arrow-right"></i></div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Total esperado</div>
                <div class="dash-kpi-valor">RD$ {{ number_format($totalEsperado, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Total real</div>
                <div class="dash-kpi-valor">RD$ {{ number_format($totalReal, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-kpi-card">
                <div class="dash-kpi-label">Diferencia total</div>
                <div class="dash-kpi-valor"
                    style="color:{{ $totalDiferencia > 0 ? '#2e7d32' : ($totalDiferencia < 0 ? '#c62828' : 'inherit') }};">
                    {{ $totalDiferencia > 0 ? '+' : '' }}RD$ {{ number_format($totalDiferencia, 2) }}
                </div>
            </div>
        </div>
    </div>

    @if ($sesionesConDiferencia > 0)
        <div class="alert alert-warning rounded-3 mb-4" style="font-size:13px;">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ $sesionesConDiferencia }} {{ $sesionesConDiferencia === 1 ? 'sesión tuvo' : 'sesiones tuvieron' }} descuadre
            este día.
            <a href="{{ route('sesiones_caja.pendientes_revision') }}" class="ms-1">Ver pendientes de revisión</a>
        </div>
    @endif

    <div class="card page-card w-100">
        <div class="card-body p-4">
            <h6 class="fw-semibold mb-4">Detalle por sesión</h6>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Caja</th>
                            <th>Responsable</th>
                            <th style="width:90px;">Apertura</th>
                            <th style="width:90px;">Cierre</th>
                            <th class="text-end" style="width:110px;">Esperado</th>
                            <th class="text-end" style="width:110px;">Real</th>
                            <th class="text-end" style="width:110px;">Diferencia</th>
                            <th style="width:60px;" class="text-end">Ver</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sesiones as $sesion)
                            <tr>
                                <td style="font-size:13px; font-weight:500;">{{ $sesion->caja?->nombre }}</td>
                                <td style="font-size:13px;">
                                    {{ $sesion->usuarioCierre?->name ?? ($sesion->usuarioApertura?->name ?? '—') }}</td>
                                <td style="font-size:12px; color:var(--text-muted);">
                                    {{ $sesion->fecha_apertura->format('H:i') }}</td>
                                <td style="font-size:12px; color:var(--text-muted);">
                                    {{ $sesion->fecha_cierre?->format('H:i') ?? '—' }}</td>
                                <td class="text-end" style="font-size:13px;">RD$
                                    {{ number_format($sesion->monto_cierre_esperado, 2) }}</td>
                                <td class="text-end" style="font-size:13px;">RD$
                                    {{ number_format($sesion->monto_cierre_real, 2) }}</td>
                                <td class="text-end" style="font-size:13px;">
                                    <span
                                        style="color:{{ $sesion->diferencia > 0 ? '#2e7d32' : ($sesion->diferencia < 0 ? '#c62828' : 'var(--text-muted)') }}; font-weight:600;">
                                        {{ $sesion->diferencia > 0 ? '+' : '' }}RD$
                                        {{ number_format($sesion->diferencia, 2) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('sesiones_caja.show', $sesion) }}"
                                        class="btn btn-outline-info btn-sm" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5" style="color:var(--text-muted);">
                                    <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                                    No hay sesiones cerradas en esta fecha.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/dashboard.css'])
@endpush
