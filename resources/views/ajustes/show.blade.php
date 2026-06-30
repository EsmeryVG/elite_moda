@extends('layouts.app')

@section('page_title', 'Ajuste de Inventario')
@section('page_subtitle', $ajuste->almacen?->nombre . ' — ' . $ajuste->fecha->format('d/m/Y'))

@section('content')
<div class="row g-4">

    <div class="col-lg-4">
        <div class="card page-card mb-4">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h6 class="fw-semibold mb-0">Información general</h6>
                    @php
                        $badge = match($ajuste->estado) {
                            'pendiente'  => 'badge-ajuste-pendiente',
                            'aprobado'   => 'badge-ajuste-aprobado',
                            'rechazado'  => 'badge-ajuste-rechazado',
                            default      => 'badge-ajuste-pendiente',
                        };
                    @endphp
                    <span class="{{ $badge }}">
                        {{ ucfirst($ajuste->estado) }}
                    </span>
                </div>

                <div class="mb-3">
                    <span class="field-label">Almacén</span>
                    <div class="field-readonly">
                        {{ $ajuste->almacen?->nombre }}
                        @if($ajuste->almacen?->sucursal)
                            <br><small style="color:var(--text-muted);">
                                {{ $ajuste->almacen->sucursal->nombre }}
                            </small>
                        @endif
                    </div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Tipo</span>
                    <div class="field-readonly">
                        {{ ucfirst(str_replace('_', ' ', $ajuste->tipo)) }}
                    </div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Fecha</span>
                    <div class="field-readonly">
                        {{ $ajuste->fecha->format('d/m/Y') }}
                    </div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Registrado por</span>
                    <div class="field-readonly">
                        {{ $ajuste->usuario?->name ?? '—' }}
                    </div>
                </div>
                @if($ajuste->motivo)
                    <div class="mb-3">
                        <span class="field-label">Motivo</span>
                        <div class="field-readonly">{{ $ajuste->motivo }}</div>
                    </div>
                @endif

            </div>
        </div>

        <div class="card page-card">
            <div class="card-body p-4">
                <div class="d-grid gap-2">

                    @if($ajuste->esPendiente())
                        <form action="{{ route('ajustes.aprobar', $ajuste) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-primary w-100"
                                    onclick="return confirm('¿Aprobar este ajuste? El stock se actualizará inmediatamente.')">
                                <i class="bi bi-check2-circle me-1"></i> Aprobar ajuste
                            </button>
                        </form>
                        <form action="{{ route('ajustes.rechazar', $ajuste) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger w-100"
                                    onclick="return confirm('¿Rechazar este ajuste?')">
                                <i class="bi bi-x-circle me-1"></i> Rechazar ajuste
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('ajustes.index') }}" class="btn btn-secondary">
                        Volver
                    </a>

                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-4">Líneas del ajuste</h6>

                <div class="tabla-lineas">
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th style="text-align:center; width:90px;">Sistema</th>
                                <th style="text-align:center; width:90px;">Real</th>
                                <th style="text-align:center; width:90px;">Diferencia</th>
                                <th>Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ajuste->detalles as $detalle)
                                <tr>
                                    <td>
                                        <div style="font-size:13px; font-weight:500;">
                                            {{ $detalle->variante?->producto?->nombre }}
                                        </div>
                                        <div style="font-size:12px; color:var(--text-muted);">
                                            @foreach($detalle->variante?->valores ?? [] as $valor)
                                                {{ $valor->atributo?->nombre }}: {{ $valor->valor }}
                                                @if(!$loop->last) · @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-center" style="font-size:13px;">
                                        {{ $detalle->cantidad_sistema }}
                                    </td>
                                    <td class="text-center" style="font-size:13px;">
                                        {{ $detalle->cantidad_real }}
                                    </td>
                                    <td class="text-center">
                                        <span style="font-weight:700; font-size:14px;
                                                     color:{{ $detalle->diferencia > 0 ? '#2e7d32' : ($detalle->diferencia < 0 ? 'var(--accent)' : 'var(--text-muted)') }};">
                                            {{ $detalle->diferencia > 0 ? '+' : '' }}{{ $detalle->diferencia }}
                                        </span>
                                    </td>
                                    <td style="font-size:12px; color:var(--text-muted);">
                                        {{ $detalle->observacion ?? '—' }}
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
    @vite(['resources/css/ajustes.css'])
@endpush