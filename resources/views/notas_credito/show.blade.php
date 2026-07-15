@extends('layouts.app')

@section('page_title', $notaCredito->codigo)
@section('page_subtitle', 'Nota de Crédito')

@section('content')
    <div class="row g-4">

        <div class="col-lg-4">
            <div class="card page-card mb-4">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <h6 class="fw-semibold mb-0">Información general</h6>
                        @php
                            $badge = match ($notaCredito->estado) {
                                'activa' => 'badge-devolucion-aprobada',
                                'agotada' => 'badge-devolucion-pendiente',
                                'vencida' => 'badge-devolucion-vencida',
                                default => 'badge-devolucion-pendiente',
                            };
                        @endphp
                        <span class="{{ $badge }}">{{ ucfirst($notaCredito->estado) }}</span>
                    </div>

                    <div class="mb-3">
                        <span class="field-label">Código</span>
                        <div class="field-readonly" style="font-family:monospace;">{{ $notaCredito->codigo }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">NCF</span>
                        <div class="field-readonly" style="font-family:monospace;">{{ $notaCredito->ncf }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Cliente</span>
                        <div class="field-readonly">
                            {{ $notaCredito->cliente?->nombre }} {{ $notaCredito->cliente?->apellido }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Devolución de origen</span>
                        <div class="field-readonly">
                            @if ($notaCredito->devolucion)
                                <a href="{{ route('devoluciones.show', $notaCredito->devolucion) }}">
                                    {{ $notaCredito->devolucion->codigo }}
                                </a>
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Empleado que atendió</span>
                        <div class="field-readonly">
                            {{ $notaCredito->devolucion?->empleado?->nombre_completo ?? '—' }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Venta original</span>
                        <div class="field-readonly">
                            @if ($notaCredito->devolucion?->venta)
                                <a href="{{ route('ventas.show', $notaCredito->devolucion->venta) }}">
                                    {{ $notaCredito->devolucion->venta->codigo }}
                                </a>
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Fecha de emisión</span>
                        <div class="field-readonly">{{ $notaCredito->fecha->format('d/m/Y') }}</div>
                    </div>
                    @if ($notaCredito->fecha_vencimiento)
                        <div class="mb-0">
                            <span class="field-label">Vencimiento</span>
                            <div class="field-readonly">{{ $notaCredito->fecha_vencimiento->format('d/m/Y') }}</div>
                        </div>
                    @endif

                </div>
            </div>

            <div class="card page-card">
                <div class="card-body p-4">
                    <p class="prod-section-title">Saldo</p>
                    <div class="totales-box">
                        <div class="totales-row">
                            <span>Monto original</span>
                            <span>RD$ {{ number_format($notaCredito->monto_original, 2) }}</span>
                        </div>
                        <div class="totales-row">
                            <span>Usado</span>
                            <span>RD$
                                {{ number_format($notaCredito->monto_original - $notaCredito->monto_disponible, 2) }}</span>
                        </div>
                        <div class="totales-row">
                            <span>Disponible</span>
                            <span class="fw-semibold">RD$ {{ number_format($notaCredito->monto_disponible, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-4">Productos devueltos (origen de esta NC)</h6>

                    @if ($notaCredito->devolucion)
                        <div class="tabla-lineas">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th style="text-align:center; width:70px;">Cant.</th>
                                        <th style="text-align:right; width:100px;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notaCredito->devolucion->detalles as $detalle)
                                        <tr>
                                            <td style="font-size:13px;">{{ $detalle->variante?->producto?->nombre }}</td>
                                            <td class="text-center" style="font-size:13px;">{{ $detalle->cantidad }}</td>
                                            <td class="text-end" style="font-size:13px;">RD$
                                                {{ number_format($detalle->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-4" style="font-size:13px;">Sin información de origen.</p>
                    @endif
                </div>
            </div>

            <div class="card page-card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-4">Usos de esta nota de crédito</h6>

                    @forelse($pagosConNC as $pago)
                        <div class="cc-movimiento-item">
                            <div>
                                <div style="font-weight:500;">
                                    @if ($pago->venta)
                                        <a href="{{ route('ventas.show', $pago->venta) }}">{{ $pago->venta->codigo }}</a>
                                    @else
                                        Venta #{{ $pago->venta_id }}
                                    @endif
                                </div>
                                <div style="font-size:11.5px; color:var(--text-muted);">
                                    {{ $pago->fecha->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="cc-movimiento-monto egreso">
                                -RD$ {{ number_format($pago->monto, 2) }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4" style="font-size:13px;">
                            Esta nota de crédito aún no ha sido utilizada en ninguna venta.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    @vite(['resources/css/devoluciones.css'])
@endpush
