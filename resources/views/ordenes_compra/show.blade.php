@extends('layouts.app')
@section('page_title', $ordenes_compra->codigo)
@section('page_subtitle', 'Detalle de la orden de compra')
@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <h6 class="fw-semibold mb-0">Información general</h6>
                        @php
                            $badgeEstado = match ($ordenes_compra->estado) {
                                'borrador' => 'badge-borrador',
                                'confirmada' => 'badge-enviada',
                                'parcial' => 'badge-parcial',
                                'completada' => 'badge-completada',
                                'cancelada' => 'badge-cancelada',
                                default => 'badge-borrador',
                            };
                        @endphp
                        <div class="d-flex gap-2 align-items-center">
                            <span class="{{ $badgeEstado }}">
                                {{ ucfirst($ordenes_compra->estado) }}
                            </span>
                            @if ($ordenes_compra->esta_retrasada)
                                <span class="badge-retrasada">
                                    <i class="bi bi-clock-history me-1"></i>Retrasada {{ $ordenes_compra->dias_retraso }}d
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Código</span>
                        <div class="field-readonly" style="font-family:monospace;">
                            {{ $ordenes_compra->codigo }}
                        </div>
                    </div>
                    @if ($ordenes_compra->numero_factura)
                        <div class="mb-3">
                            <span class="field-label">Cotización/factura</span>
                            <div class="field-readonly" style="font-family:monospace;">
                                {{ $ordenes_compra->numero_factura }}
                            </div>
                        </div>
                    @endif
                    <div class="mb-3">
                        <span class="field-label">Proveedor</span>
                        <div class="field-readonly">{{ $ordenes_compra->proveedor?->nombre }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Almacén destino</span>
                        <div class="field-readonly">
                            {{ $ordenes_compra->almacen?->nombre }}
                            @if ($ordenes_compra->almacen?->sucursal)
                                — {{ $ordenes_compra->almacen->sucursal->nombre }}
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Fecha</span>
                        <div class="field-readonly">
                            {{ $ordenes_compra->fecha->format('d/m/Y') }}
                        </div>
                    </div>
                    @if ($ordenes_compra->fecha_esperada)
                        <div class="mb-3">
                            <span class="field-label">Fecha esperada</span>
                            <div class="field-readonly">
                                {{ $ordenes_compra->fecha_esperada->format('d/m/Y') }}
                            </div>
                        </div>
                    @endif
                    <div class="mb-3">
                        <span class="field-label">Creada por</span>
                        <div class="field-readonly">
                            {{ $ordenes_compra->usuario?->name ?? '—' }}
                        </div>
                    </div>
                    @if ($ordenes_compra->observaciones)
                        <div class="mb-3">
                            <span class="field-label">Observaciones</span>
                            <div class="field-readonly">{{ $ordenes_compra->observaciones }}</div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <p class="prod-section-title">Resumen</p>
                    <div class="totales-box">
                        <div class="totales-row">
                            <span>Subtotal</span>
                            <span>RD$ {{ number_format($ordenes_compra->subtotal, 2) }}</span>
                        </div>
                        <div class="totales-row">
                            <span>ITBIS</span>
                            <span>RD$ {{ number_format($ordenes_compra->impuesto, 2) }}</span>
                        </div>
                        <div class="totales-row">
                            <span>Total</span>
                            <span>RD$ {{ number_format($ordenes_compra->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card page-card">
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        @permiso('compras.gestionar')
                            @if ($ordenes_compra->estado === 'borrador')
                                <a href="{{ route('ordenes_compra.edit', $ordenes_compra) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil-square me-1"></i> Editar
                                </a>
                                <form action="{{ route('ordenes_compra.confirmar', $ordenes_compra) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline-primary w-100"
                                        onclick="return confirm('¿Confirmar esta orden? Ya no se podrá editar.')">
                                        <i class="bi bi-check2-circle me-1"></i> Confirmar orden
                                    </button>
                                </form>
                            @endif
                            @if (in_array($ordenes_compra->estado, ['confirmada', 'parcial']))
                                <a href="{{ route('recepciones.create', ['orden' => $ordenes_compra->id]) }}"
                                    class="btn btn-primary">
                                    <i class="bi bi-box-arrow-in-down me-1"></i> Registrar recepción
                                </a>
                            @endif
                            @if ($ordenes_compra->esCancelable())
                                <form action="{{ route('ordenes_compra.cancelar', $ordenes_compra) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline-danger w-100"
                                        onclick="return confirm('¿Cancelar esta orden?')">
                                        <i class="bi bi-x-circle me-1"></i> Cancelar orden
                                    </button>
                                </form>
                            @endif
                        @endpermiso
                        <a href="{{ route('ordenes_compra.index') }}" class="btn btn-secondary">
                            Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-4">Líneas de la orden</h6>
                    <div class="tabla-lineas">
                        <table>
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th style="text-align:center; width:80px;">Solicit.</th>
                                    <th style="text-align:center; width:80px;">Recib.</th>
                                    <th style="text-align:right; width:110px;">Precio</th>
                                    <th style="width:60px;">ITBIS</th>
                                    <th style="text-align:right; width:110px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ordenes_compra->detalles as $detalle)
                                    <tr>
                                        <td style="font-size:13px;">
                                            {{ $detalle->descripcion }}
                                        </td>
                                        <td class="text-center" style="font-size:13px;">
                                            {{ $detalle->cantidad_solicitada }}
                                        </td>
                                        <td class="text-center" style="font-size:13px;">
                                            {{ $detalle->cantidad_recibida }}
                                        </td>
                                        <td class="text-end" style="font-size:13px;">
                                            RD$ {{ number_format($detalle->precio_unitario, 2) }}
                                        </td>
                                        <td>
                                            @if ($detalle->itbis_incluido)
                                                <span style="color:#2e7d32; font-size:12px;">● Sí</span>
                                            @else
                                                <span style="color:var(--text-muted); font-size:12px;">● No</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-semibold" style="font-size:13px;">
                                            RD$ {{ number_format($detalle->subtotal, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card page-card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-4">Recepciones registradas</h6>
                    @forelse($ordenes_compra->recepciones as $recepcion)
                        <div class="mb-3 p-3"
                            style="border:1px solid var(--border); border-radius:var(--radius-md);
                                background:var(--bg-elevated);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span style="font-family:monospace; font-size:12px; color:var(--text-muted);">
                                        {{ $recepcion->codigo }}
                                    </span>
                                    <span class="ms-2" style="font-size:12px; color:var(--text-muted);">
                                        {{ $recepcion->fecha->format('d/m/Y') }}
                                    </span>
                                </div>
                                <a href="{{ route('recepciones.show', $recepcion) }}" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-center py-3" style="color:var(--text-muted); font-size:13px;">
                            No hay recepciones registradas aún.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    @vite(['resources/css/ordenes_compra.css'])
@endpush
