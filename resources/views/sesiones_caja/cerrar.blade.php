@extends('layouts.app')

@section('page_title', 'Cerrar Sesión de Caja')
@section('page_subtitle', $sesionCaja->caja?->nombre . ' — Abierta ' . $sesionCaja->fecha_apertura->format('d/m/Y H:i'))

@section('content')
    <div class="row g-4 justify-content-center">
        <div class="col-lg-7">

            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <p class="prod-section-title mb-3">Resumen del turno</p>
                    <div class="cierre-resumen-box">
                        <div class="totales-row">
                            <span>Fondo de apertura</span>
                            <span>RD$ {{ number_format($sesionCaja->monto_apertura, 2) }}</span>
                        </div>
                        <div class="totales-row">
                            <span>Ventas del turno</span>
                            <span>{{ $totalVentas }} ({{ 'RD$ ' . number_format($totalVentasMonto, 2) }})</span>
                        </div>
                        <div class="totales-row">
                            <span>Efectivo esperado en caja</span>
                            <span class="fw-semibold">RD$ {{ number_format($montoEsperado, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('sesiones_caja.cerrar.store', $sesionCaja) }}" method="POST">
                @csrf

                <div class="card page-card mb-4">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label">
                                Efectivo contado <span style="color:var(--accent);">*</span>
                            </label>
                            <input type="number" id="montoCierreReal" name="monto_cierre_real" step="0.01"
                                min="0" data-esperado="{{ $montoEsperado }}"
                                class="form-control @error('monto_cierre_real') is-invalid @enderror"
                                value="{{ old('monto_cierre_real') }}">
                            @error('monto_cierre_real')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <span class="field-label">Diferencia</span>
                            <div class="field-readonly">
                                <span id="diferenciaPreview" class="cierre-diferencia-neutra">RD$ 0.00</span>
                            </div>
                        </div>

                        <div class="mb-3" id="grupoObservacion" style="display:none;">
                            <label class="form-label">
                                Explica la diferencia <span style="color:var(--accent);">*</span>
                            </label>
                            <textarea name="observacion_diferencia" class="form-control @error('observacion_diferencia') is-invalid @enderror"
                                rows="3">{{ old('observacion_diferencia') }}</textarea>
                            @error('observacion_diferencia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card page-card">
                    <div class="card-body p-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-lock me-1"></i> Cerrar sesión de caja
                            </button>
                            <a href="{{ route('sesiones_caja.show', $sesionCaja) }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/caja.css'])
@endpush

@push('scripts')
    @vite(['resources/js/caja.js'])
@endpush
