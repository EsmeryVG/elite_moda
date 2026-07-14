@extends('layouts.app')

@section('page_title', $sesionCaja->caja?->nombre)
@section('page_subtitle', 'Sesión de caja')

@section('content')
    <div class="row g-4">

        <div class="col-lg-4">
            <div class="card page-card mb-4">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <h6 class="fw-semibold mb-0">Información general</h6>
                        <span
                            class="{{ $sesionCaja->estado === 'abierta' ? 'badge-sesion-abierta' : 'badge-sesion-cerrada' }}">
                            {{ ucfirst($sesionCaja->estado) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <span class="field-label">Caja</span>
                        <div class="field-readonly">{{ $sesionCaja->caja?->nombre }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Responsable (apertura)</span>
                        <div class="field-readonly">{{ $sesionCaja->usuarioApertura?->name ?? '—' }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Apertura</span>
                        <div class="field-readonly">{{ $sesionCaja->fecha_apertura->format('d/m/Y H:i') }}</div>
                    </div>
                    @if ($sesionCaja->estado === 'cerrada')
                        <div class="mb-3">
                            <span class="field-label">Cierre</span>
                            <div class="field-readonly">
                                {{ $sesionCaja->fecha_cierre?->format('d/m/Y H:i') }}
                                <br><small style="color:var(--text-muted);">
                                    por {{ $sesionCaja->usuarioCierre?->name ?? '—' }}
                                </small>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <p class="prod-section-title">Cuadre</p>
                    <div class="totales-box">
                        <div class="totales-row">
                            <span>Fondo apertura</span>
                            <span>RD$ {{ number_format($sesionCaja->monto_apertura, 2) }}</span>
                        </div>
                        @if ($sesionCaja->estado === 'cerrada')
                            <div class="totales-row">
                                <span>Esperado</span>
                                <span>RD$ {{ number_format($sesionCaja->monto_cierre_esperado, 2) }}</span>
                            </div>
                            <div class="totales-row">
                                <span>Real</span>
                                <span>RD$ {{ number_format($sesionCaja->monto_cierre_real, 2) }}</span>
                            </div>
                            <div class="totales-row">
                                <span>Diferencia</span>
                                <span
                                    style="color:{{ $sesionCaja->diferencia > 0 ? '#2e7d32' : ($sesionCaja->diferencia < 0 ? '#c62828' : 'var(--text-muted)') }}; font-weight:700;">
                                    {{ $sesionCaja->diferencia > 0 ? '+' : '' }}RD$
                                    {{ number_format($sesionCaja->diferencia, 2) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    @if ($sesionCaja->observacion_diferencia)
                        <div class="mt-3" style="font-size:12.5px; color:var(--text-muted);">
                            <strong>Observación:</strong> {{ $sesionCaja->observacion_diferencia }}
                        </div>
                    @endif
                    @if ($sesionCaja->estado === 'cerrada' && $sesionCaja->diferencia != 0)
                        <div class="mt-3">
                            @if ($sesionCaja->revisada_por)
                                <span class="badge-sesion-abierta">
                                    Revisada por {{ $sesionCaja->revisadaPor?->name }} el
                                    {{ $sesionCaja->revisada_en->format('d/m/Y H:i') }}
                                </span>
                            @else
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge-condicion-no_conforme">Pendiente de revisión</span>
                                    @if (Auth::user()->esAdministrador())
                                        <a href="{{ route('sesiones_caja.pendientes_revision') }}"
                                            class="btn btn-outline-success btn-sm" title="Ir a revisar">
                                            <i class="bi bi-arrow-right-circle"></i>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if ($sesionCaja->estado === 'abierta')
                <div class="card page-card">
                    <div class="card-body p-4">
                        <a href="{{ route('sesiones_caja.cerrar', $sesionCaja) }}" class="btn btn-primary w-100">
                            <i class="bi bi-lock me-1"></i> Cerrar sesión
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-8">
            <div class="card page-card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-4">Ventas del turno</h6>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Cliente</th>
                                    <th class="text-end">Total</th>
                                    <th>Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sesionCaja->ventas as $venta)
                                    <tr>
                                        <td style="font-size:13px; font-family:monospace;">
                                            <a href="{{ route('ventas.show', $venta) }}">{{ $venta->codigo }}</a>
                                        </td>
                                        <td style="font-size:13px;">
                                            {{ $venta->cliente?->nombre }} {{ $venta->cliente?->apellido }}
                                        </td>
                                        <td class="text-end fw-semibold" style="font-size:13px;">
                                            RD$ {{ number_format($venta->total, 2) }}
                                        </td>
                                        <td style="font-size:12px; color:var(--text-muted);">
                                            {{ $venta->fecha->format('H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4"
                                            style="color:var(--text-muted); font-size:13px;">
                                            No se han registrado ventas en este turno.
                                        </td>
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

@push('styles')
    @vite(['resources/css/caja.css'])
@endpush
