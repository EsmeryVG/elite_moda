@extends('layouts.app')

@section('page_title', $devolucion->codigo)
@section('page_subtitle', 'Detalle de la devolución')

@section('content')
    <div class="row g-4">

        <div class="col-lg-4">
            <div class="card page-card mb-4">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <h6 class="fw-semibold mb-0">Información general</h6>
                        @php
                            $badge = match ($devolucion->estado) {
                                'pendiente' => 'badge-devolucion-pendiente',
                                'aprobada' => 'badge-devolucion-aprobada',
                                default => 'badge-devolucion-pendiente',
                            };
                        @endphp
                        <span class="{{ $badge }}">{{ ucfirst($devolucion->estado) }}</span>
                    </div>

                    <div class="mb-3">
                        <span class="field-label">Código</span>
                        <div class="field-readonly" style="font-family:monospace;">
                            {{ $devolucion->codigo }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Venta original</span>
                        <div class="field-readonly">
                            <a href="{{ route('ventas.show', $devolucion->venta) }}">
                                {{ $devolucion->venta?->codigo }}
                            </a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Cliente</span>
                        <div class="field-readonly">
                            {{ $devolucion->cliente?->nombre }} {{ $devolucion->cliente?->apellido }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Registrado por</span>
                        <div class="field-readonly">
                            {{ $devolucion->usuario?->name ?? '—' }}
                        </div>
                    </div>
                    @if ($devolucion->empleado)
                        <div class="mb-3">
                            <span class="field-label">Empleado</span>
                            <div class="field-readonly">
                                {{ $devolucion->empleado->nombre_completo }}
                            </div>
                        </div>
                    @endif
                    <div class="mb-3">
                        <span class="field-label">Fecha</span>
                        <div class="field-readonly">
                            {{ $devolucion->fecha->format('d/m/Y H:i') }}
                            <span style="color:var(--text-muted); font-size:12px;">
                                ({{ $devolucion->dias_desde_venta }} días desde la venta)
                            </span>
                        </div>
                    </div>

                    @if ($devolucion->requiere_autorizacion)
                        <div class="mb-3">
                            <span class="field-label">Autorizado por</span>
                            <div class="field-readonly">
                                {{ $devolucion->autorizador?->name ?? '—' }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card page-card">
                <div class="card-body p-4">
                    <p class="prod-section-title">Nota de crédito</p>
                    <div class="nc-box">
                        <div class="mb-2" style="font-size:13px;">
                            <strong>{{ $devolucion->notaCredito?->codigo }}</strong>
                            <span style="color:var(--text-muted);">
                                · NCF {{ $devolucion->notaCredito?->ncf }}
                            </span>
                        </div>
                        <div class="totales-row">
                            <span>Monto original</span>
                            <span>RD$ {{ number_format($devolucion->notaCredito?->monto_original ?? 0, 2) }}</span>
                        </div>
                        <div class="totales-row">
                            <span>Disponible</span>
                            <span>RD$ {{ number_format($devolucion->notaCredito?->monto_disponible ?? 0, 2) }}</span>
                        </div>
                        @if (!$devolucion->incluye_itbis)
                            <div class="mt-2" style="font-size:12px; color:#b71c1c;">
                                <i class="bi bi-info-circle me-1"></i>
                                No incluye ITBIS (devolución fuera del plazo de {{ $devolucion->dias_desde_venta }} días).
                            </div>
                        @endif
                    </div>

                    <a href="{{ route('devoluciones.index') }}" class="btn btn-secondary w-100 mt-3">
                        Volver
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card page-card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-4">Líneas devueltas</h6>

                    <div class="tabla-lineas">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:220px;">Producto</th>
                                    <th style="text-align:center; width:60px;">Cant.</th>
                                    <th style="text-align:right; width:110px;">Subtotal</th>
                                    <th style="width:160px; padding-left:20px;">Motivo</th>
                                    <th style="width:130px;">Condición</th>
                                    <th style="width:100px;" class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($devolucion->detalles as $detalle)
                                    <tr>
                                        <td>
                                            <div style="font-size:13px; font-weight:500;">
                                                {{ $detalle->variante?->producto?->nombre }}
                                            </div>
                                        </td>
                                        <td class="text-center" style="font-size:13px;">
                                            {{ $detalle->cantidad }}
                                        </td>
                                        <td class="text-end" style="font-size:13px;">
                                            RD$ {{ number_format($detalle->subtotal, 2) }}
                                        </td>
                                        <td style="font-size:12px; color:var(--text-muted); padding-left:20px;">
                                            {{ ucfirst(str_replace('_', ' ', $detalle->motivo)) }}
                                        </td>
                                        <td>
                                            @php
                                                $badgeC = match ($detalle->condicion_inspeccion) {
                                                    'pendiente' => 'badge-condicion-pendiente',
                                                    'conforme' => 'badge-condicion-conforme',
                                                    'no_conforme' => 'badge-condicion-no_conforme',
                                                };
                                            @endphp
                                            <span class="{{ $badgeC }}">
                                                {{ ucfirst(str_replace('_', ' ', $detalle->condicion_inspeccion)) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            @if ($detalle->condicion_inspeccion === 'pendiente')
                                                <button type="button"
                                                    class="btn-accion btn-accion-success btn-accion-inline"
                                                    data-detalle-id="{{ $detalle->id }}" data-condicion="conforme"
                                                    data-url="{{ route('devoluciones.inspeccionar', $detalle) }}">
                                                    <i class="bi bi-check2"></i> Conforme
                                                </button>
                                                <button type="button"
                                                    class="btn-accion btn-accion-danger btn-accion-inline btn-inspeccionar"
                                                    data-detalle-id="{{ $detalle->id }}" data-condicion="no_conforme"
                                                    data-url="{{ route('devoluciones.inspeccionar', $detalle) }}">
                                                    <i class="bi bi-x"></i> No conforme
                                                </button>
                                            @else
                                                <span style="font-size:12px; color:var(--text-muted);">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    @vite(['resources/css/devoluciones.css'])
@endpush

@push('scripts')
    @vite(['resources/js/devoluciones.js'])
@endpush
