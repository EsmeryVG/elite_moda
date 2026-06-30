@extends('layouts.app')

@section('page_title', $recepcion->codigo)
@section('page_subtitle', 'Detalle de la recepción')

@section('content')
<div class="row g-4">

    <div class="col-lg-4">
        <div class="card page-card mb-4">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h6 class="fw-semibold mb-0">Información general</h6>
                    @php
                        $badgeTipo = match($recepcion->tipo) {
                            'completa'    => ['bg' => 'rgba(76,175,80,0.12)',  'color' => '#2e7d32', 'texto' => 'Completa'],
                            'parcial'     => ['bg' => 'rgba(255,152,0,0.12)',  'color' => '#e65100', 'texto' => 'Parcial'],
                            'no_conforme' => ['bg' => 'rgba(211,47,47,0.10)',  'color' => '#c62828', 'texto' => 'No conforme'],
                            default       => ['bg' => 'var(--bg-hover)',       'color' => 'var(--text-muted)', 'texto' => ucfirst($recepcion->tipo)],
                        };
                    @endphp
                    <span class="badge rounded-pill"
                          style="background:{{ $badgeTipo['bg'] }}; color:{{ $badgeTipo['color'] }};
                                 font-size:11px; padding:4px 10px;">
                        {{ $badgeTipo['texto'] }}
                    </span>
                </div>

                <div class="mb-3">
                    <span class="field-label">Código</span>
                    <div class="field-readonly" style="font-family:monospace;">
                        {{ $recepcion->codigo }}
                    </div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Orden de compra</span>
                    <div class="field-readonly">
                        <a href="{{ route('ordenes_compra.show', $recepcion->orden) }}"
                           style="color:var(--text-primary); text-decoration:none; font-family:monospace;">
                            {{ $recepcion->orden?->codigo }}
                            <i class="bi bi-arrow-up-right-square ms-1"
                               style="font-size:11px; color:var(--text-muted);"></i>
                        </a>
                    </div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Proveedor</span>
                    <div class="field-readonly">
                        {{ $recepcion->orden?->proveedor?->nombre ?? '—' }}
                    </div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Almacén</span>
                    <div class="field-readonly">
                        {{ $recepcion->orden?->almacen?->nombre ?? '—' }}
                        @if($recepcion->orden?->almacen?->sucursal)
                            <br>
                            <small style="color:var(--text-muted);">
                                {{ $recepcion->orden->almacen->sucursal->nombre }}
                            </small>
                        @endif
                    </div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Fecha</span>
                    <div class="field-readonly">
                        {{ $recepcion->fecha->format('d/m/Y') }}
                    </div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Registrada por</span>
                    <div class="field-readonly">
                        {{ $recepcion->usuario?->name ?? '—' }}
                    </div>
                </div>
                @if($recepcion->motivo_rechazo)
                    <div class="mb-3">
                        <span class="field-label">Motivo de rechazo</span>
                        <div class="field-readonly" style="color:var(--accent);">
                            {{ $recepcion->motivo_rechazo }}
                        </div>
                    </div>
                @endif
                @if($recepcion->observaciones)
                    <div class="mb-3">
                        <span class="field-label">Observaciones</span>
                        <div class="field-readonly">{{ $recepcion->observaciones }}</div>
                    </div>
                @endif

            </div>
        </div>

        <div class="card page-card">
            <div class="card-body p-4">
                <a href="{{ route('recepciones.index') }}" class="btn btn-secondary w-100">
                    Volver
                </a>
            </div>
        </div>
    </div>

    {{-- Líneas recibidas --}}
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-4">Líneas recibidas</h6>

                <div class="tabla-lineas">
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th style="text-align:center; width:90px;">Recibido</th>
                                <th style="text-align:center; width:90px;">Aceptado</th>
                                <th style="width:110px;">Calidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recepcion->detalles as $detalle)
                                <tr>
                                    <td>
                                        <div style="font-size:13px; font-weight:500;">
                                            {{ $detalle->detalleOrden?->variante?->producto?->nombre }}
                                        </div>
                                        <div style="font-size:12px; color:var(--text-muted);">
                                            @foreach($detalle->detalleOrden?->variante?->valores ?? [] as $valor)
                                                {{ $valor->atributo?->nombre }}: {{ $valor->valor }}
                                                @if(!$loop->last) · @endif
                                            @endforeach
                                        </div>
                                        @if($detalle->observacion)
                                            <div style="font-size:11px; color:var(--text-muted);
                                                        font-style:italic; margin-top:3px;">
                                                {{ $detalle->observacion }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center" style="font-size:13px;">
                                        {{ $detalle->cantidad_recibida }}
                                    </td>
                                    <td class="text-center" style="font-size:13px;">
                                        {{ $detalle->cantidad_aceptada }}
                                    </td>
                                    <td>
                                        @if($detalle->estado_calidad === 'conforme')
                                            <span style="color:#2e7d32; font-size:12px;">● Conforme</span>
                                        @else
                                            <span style="color:var(--accent); font-size:12px;">● No conforme</span>
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
    @vite(['resources/css/ordenes_compra.css'])
@endpush