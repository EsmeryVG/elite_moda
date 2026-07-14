@extends('layouts.app')

@section('page_title', $cuentaPorCobrar->codigo)
@section('page_subtitle', 'Cuenta por cobrar')

@section('content')
    <div class="row g-4">

        <div class="col-lg-4">
            <div class="card page-card mb-4">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <h6 class="fw-semibold mb-0">Información general</h6>
                        @php
                            $badge = match ($cuentaPorCobrar->estado) {
                                'pendiente' => 'badge-cpc-pendiente',
                                'parcial' => 'badge-cpc-parcial',
                                'pagada' => 'badge-cpc-pagada',
                                'vencida' => 'badge-cpc-vencida',
                                default => 'badge-cpc-pendiente',
                            };
                        @endphp
                        <span class="{{ $badge }}">{{ ucfirst($cuentaPorCobrar->estado) }}</span>
                    </div>

                    <div class="mb-3">
                        <span class="field-label">Código</span>
                        <div class="field-readonly" style="font-family:monospace;">{{ $cuentaPorCobrar->codigo }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Cliente</span>
                        <div class="field-readonly">
                            {{ $cuentaPorCobrar->cliente?->nombre }} {{ $cuentaPorCobrar->cliente?->apellido }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Venta de origen</span>
                        <div class="field-readonly">
                            @if ($cuentaPorCobrar->venta)
                                <a
                                    href="{{ route('ventas.show', $cuentaPorCobrar->venta) }}">{{ $cuentaPorCobrar->venta->codigo }}</a>
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Fecha de emisión</span>
                        <div class="field-readonly">{{ $cuentaPorCobrar->fecha_emision->format('d/m/Y') }}</div>
                    </div>
                    <div class="mb-0">
                        <span class="field-label">Fecha de vencimiento</span>
                        <div class="field-readonly">{{ $cuentaPorCobrar->fecha_vencimiento->format('d/m/Y') }}</div>
                    </div>

                </div>
            </div>

            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <p class="prod-section-title">Saldo</p>
                    <div class="totales-box">
                        <div class="totales-row">
                            <span>Monto total</span>
                            <span>RD$ {{ number_format($cuentaPorCobrar->monto_total, 2) }}</span>
                        </div>
                        <div class="totales-row">
                            <span>Pagado</span>
                            <span style="color:#2e7d32;">RD$ {{ number_format($cuentaPorCobrar->monto_pagado, 2) }}</span>
                        </div>
                        <div class="totales-row">
                            <span>Pendiente</span>
                            <span class="fw-semibold">RD$ {{ number_format($cuentaPorCobrar->monto_pendiente, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if (!in_array($cuentaPorCobrar->estado, ['pagada', 'anulada']))
                <div class="card page-card">
                    <div class="card-body p-4">
                        <p class="prod-section-title mb-3">Registrar abono</p>

                        <form action="{{ route('cuentas_por_cobrar.abono', $cuentaPorCobrar) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">RD$</span>
                                    <input type="number" name="monto" step="0.01" min="0.01"
                                        max="{{ $cuentaPorCobrar->monto_pendiente }}"
                                        class="form-control @error('monto') is-invalid @enderror"
                                        value="{{ old('monto') }}" placeholder="0.00">
                                </div>
                                @error('monto')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <select name="tipo_pago_id"
                                    class="form-select form-select-sm @error('tipo_pago_id') is-invalid @enderror">
                                    <option value="">Método de pago...</option>
                                    @foreach ($tiposPago as $tipo)
                                        @if (!in_array($tipo->nombre, ['Crédito', 'Nota de Crédito']))
                                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('tipo_pago_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <input type="text" name="referencia" class="form-control form-control-sm"
                                    placeholder="Referencia (opcional)">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-sm">
                                <i class="bi bi-check-circle me-1"></i> Registrar abono
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-8">
            <div class="card page-card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-4">Historial de abonos</h6>

                    @forelse($cuentaPorCobrar->pagos as $pago)
                        <div class="cpc-abono-item">
                            <div>
                                <div style="font-weight:500;">{{ $pago->tipoPago?->nombre }}</div>
                                <div style="font-size:11.5px; color:var(--text-muted);">
                                    {{ $pago->usuario?->name ?? '—' }} · {{ $pago->fecha->format('d/m/Y') }}
                                    @if ($pago->referencia)
                                        · Ref: {{ $pago->referencia }}
                                    @endif
                                </div>
                            </div>
                            <div style="font-weight:600; color:#2e7d32;">
                                RD$ {{ number_format($pago->monto, 2) }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4" style="font-size:13px;">
                            Aún no se han registrado abonos.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    @vite(['resources/css/cuentas_por_cobrar.css'])
@endpush
