@extends('layouts.app')

@section('page_title', $detalleNomina->empleado?->nombre_completo)
@section('page_subtitle', 'Detalle de pago — ' . $detalleNomina->nomina->periodo_inicio->format('d/m/Y') . ' — ' .
    $detalleNomina->nomina->periodo_fin->format('d/m/Y'))

@section('content')
    <div class="row g-4">

        <div class="col-lg-4">
            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <p class="prod-section-title">Empleado</p>
                    <div class="mb-3">
                        <span class="field-label">Nombre</span>
                        <div class="field-readonly">{{ $detalleNomina->empleado?->nombre_completo }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Cargo</span>
                        <div class="field-readonly">{{ $detalleNomina->empleado?->cargo ?? '—' }}</div>
                    </div>
                    <div class="mb-0">
                        <span class="field-label">Código</span>
                        <div class="field-readonly" style="font-family:monospace;">{{ $detalleNomina->empleado?->codigo }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card page-card">
                <div class="card-body p-4">
                    <p class="prod-section-title">Resumen de pago</p>
                    <div class="totales-box">
                        <div class="totales-row">
                            <span>Salario base</span>
                            <span>RD$ {{ number_format($detalleNomina->salario_base, 2) }}</span>
                        </div>
                        <div class="totales-row">
                            <span>Comisiones ({{ $detalleNomina->comisiones->count() }} ventas)</span>
                            <span style="color:#2e7d32;">RD$ {{ number_format($detalleNomina->total_comisiones, 2) }}</span>
                        </div>
                        <div class="totales-row"
                            style="font-weight:700; font-size:15px; margin-top:6px; padding-top:6px; border-top:1px solid var(--border);">
                            <span>Total a pagar</span>
                            <span>RD$ {{ number_format($detalleNomina->total_pagar, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card page-card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-4">Ventas y comisiones del período</h6>

                    @forelse($detalleNomina->comisiones as $comision)
                        <div class="nomina-comision-item">
                            <div>
                                <div style="font-weight:500;">
                                    @if ($comision->venta)
                                        <a
                                            href="{{ route('ventas.show', $comision->venta) }}">{{ $comision->venta->codigo }}</a>
                                    @else
                                        Venta #{{ $comision->venta_id }}
                                    @endif
                                </div>
                                <div style="font-size:11.5px; color:var(--text-muted);">
                                    {{ $comision->venta?->cliente?->nombre }} {{ $comision->venta?->cliente?->apellido }}
                                    · {{ $comision->fecha->format('d/m/Y') }}
                                    · Venta: RD$ {{ number_format($comision->monto_venta, 2) }}
                                    ({{ $comision->porcentaje_comision }}%)
                                </div>
                            </div>
                            <div style="font-weight:700; color:#2e7d32;">
                                RD$ {{ number_format($comision->monto_comision, 2) }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4" style="font-size:13px;">
                            No hay comisiones registradas en este período.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    @vite(['resources/css/nomina.css'])
@endpush
