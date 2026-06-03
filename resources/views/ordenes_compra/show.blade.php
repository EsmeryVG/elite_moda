@extends('layouts.app')

@section('page_title', $orden->codigo)
@section('page_subtitle', 'Detalle de la orden de compra')

@section('content')
<div class="row g-4">

    {{-- Info general --}}
    <div class="col-lg-4">

        <div class="card page-card mb-4">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h6 class="fw-semibold mb-0">Información general</h6>
                    <span class="badge-{{ $orden->estado }}">
                        {{ ucfirst($orden->estado) }}
                    </span>
                </div>

                <div class="mb-3">
                    <span class="field-label">Código</span>
                    <div class="field-readonly" style="font-family:monospace;">
                        {{ $orden->codigo }}
                    </div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Proveedor</span>
                    <div class="field-readonly">{{ $orden->proveedor?->nombre }}</div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Almacén destino</span>
                    <div class="field-readonly">
                        {{ $orden->almacen?->nombre }}
                        @if($orden->almacen?->sucursal)
                            — {{ $orden->almacen->sucursal->nombre }}
                        @endif
                    </div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Fecha</span>
                    <div class="field-readonly">
                        {{ $orden->fecha->format('d/m/Y') }}
                    </div>
                </div>
                @if($orden->fecha_esperada)
                    <div class="mb-3">
                        <span class="field-label">Fecha esperada</span>
                        <div class="field-readonly">
                            {{ $orden->fecha_esperada->format('d/m/Y') }}
                        </div>
                    </div>
                @endif
                <div class="mb-3">
                    <span class="field-label">Creada por</span>
                    <div class="field-readonly">
                        {{ $orden->usuario?->name ?? '—' }}
                    </div>
                </div>
                @if($orden->observaciones)
                    <div class="mb-3">
                        <span class="field-label">Observaciones</span>
                        <div class="field-readonly">{{ $orden->observaciones }}</div>
                    </div>
                @endif

            </div>
        </div>

        {{-- Totales --}}
        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <p class="prod-section-title">Resumen</p>
                <div class="totales-box">
                    <div class="totales-row">
                        <span>Subtotal</span>
                        <span>RD$ {{ number_format($orden->subtotal, 2) }}</span>
                    </div>
                    <div class="totales-row">
                        <span>ITBIS (18%)</span>
                        <span>RD$ {{ number_format($orden->impuesto, 2) }}</span>
                    </div>
                    <div class="totales-row">
                        <span>Total</span>
                        <span>RD$ {{ number_format($orden->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="card page-card">
            <div class="card-body p-4">
                <div class="d-grid gap-2">

                    @if($orden->estado === 'borrador')
                        <a href="{{ route('ordenes_compra.edit', $orden) }}"
                           class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i> Editar
                        </a>
                        <form action="{{ route('ordenes_compra.enviar', $orden) }}"
                              method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-outline-primary w-100"
                                    onclick="return confirm('¿Enviar esta orden? Ya no se podrá editar.')">
                                <i class="bi bi-send me-1"></i> Enviar orden
                            </button>
                        </form>
                    @endif

                    @if(in_array($orden->estado, ['enviada', 'parcial']))
                        <a href="{{ route('recepciones.create', ['orden' => $orden->id]) }}"
                           class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-down me-1"></i> Registrar recepción
                        </a>
                    @endif

                    @if($orden->esCancelable())
                        <form action="{{ route('ordenes_compra.cancelar', $orden) }}"
                              method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger w-100"
                                    onclick="return confirm('¿Cancelar esta orden?')">
                                <i class="bi bi-x-circle me-1"></i> Cancelar orden
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('ordenes_compra.index') }}"
                       class="btn btn-secondary">
                        Volver
                    </a>

                </div>
            </div>
        </div>

    </div>

    {{-- Líneas y recepciones --}}
    <div class="col-lg-8">

        {{-- Líneas de la orden --}}
        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-4">Líneas de la orden</h6>

                <div class="tabla-lineas">
                    <table>
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th style="text-align:center;">Solicitado</th>
                                <th style="text-align:center;">Recibido</th>
                                <th style="text-align:right;">Precio unit.</th>
                                <th style="text-align:right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orden->detalles as $detalle)
                                <tr>
                                    <td>
                                        @if($detalle->esPorCaracteristicas())
                                            <span class="badge-tipo-caracteristicas">
                                                Características
                                            </span>
                                        @else
                                            <span class="badge-tipo-variante">
                                                Variante
                                            </span>
                                        @endif
                                    </td>
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

        {{-- Recepciones --}}
        <div class="card page-card">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-4">Recepciones registradas</h6>

                @forelse($orden->recepciones as $recepcion)
                    <div class="mb-3 p-3"
                         style="border:1px solid var(--border);
                                border-radius:var(--radius-md);
                                background:var(--bg-elevated);">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span style="font-family:monospace; font-size:12px;
                                             color:var(--text-muted);">
                                    {{ $recepcion->codigo }}
                                </span>
                                <span class="ms-2" style="font-size:12px; color:var(--text-muted);">
                                    {{ $recepcion->fecha->format('d/m/Y') }}
                                </span>
                            </div>
                            <span class="badge rounded-pill"
                                  style="background:var(--bg-hover); color:var(--text-secondary);
                                         font-size:11px; padding:3px 10px;">
                                {{ ucfirst($recepcion->tipo) }}
                            </span>
                        </div>
                        @if($recepcion->observaciones)
                            <p style="font-size:12px; color:var(--text-muted); margin:0;">
                                {{ $recepcion->observaciones }}
                            </p>
                        @endif
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