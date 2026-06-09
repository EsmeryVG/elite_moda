@extends('layouts.app')

@section('page_title', 'Nueva Recepción')
@section('page_subtitle', 'Orden ' . $orden->codigo . ' — ' . $orden->proveedor?->nombre)

@section('content')
<form action="{{ route('recepciones.store') }}" method="POST" id="formRecepcion">
@csrf

<input type="hidden" name="orden_compra_id" value="{{ $orden->id }}">

<div class="row g-4">

    {{-- Columna izquierda --}}
    <div class="col-lg-4">

        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <p class="prod-section-title">Información de la recepción</p>

                <div class="mb-3">
                    <span class="field-label">Orden de compra</span>
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
                    <label class="form-label">
                        Fecha de recepción <span style="color:var(--accent);">*</span>
                    </label>
                    <input type="date" name="fecha"
                           class="form-control @error('fecha') is-invalid @enderror"
                           value="{{ old('fecha', now()->format('Y-m-d')) }}">
                    @error('fecha')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Tipo de recepción <span style="color:var(--accent);">*</span>
                    </label>
                    <select name="tipo"
                            class="form-select @error('tipo') is-invalid @enderror"
                            id="tipoRecepcion">
                        <option value="completa"
                                {{ old('tipo', 'completa') === 'completa' ? 'selected' : '' }}>
                            Completa
                        </option>
                        <option value="parcial"
                                {{ old('tipo') === 'parcial' ? 'selected' : '' }}>
                            Parcial
                        </option>
                        <option value="no_conforme"
                                {{ old('tipo') === 'no_conforme' ? 'selected' : '' }}>
                            No conforme
                        </option>
                    </select>
                    @error('tipo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3" id="motivoRechazoWrapper" style="display:none;">
                    <label class="form-label">
                        Motivo de rechazo <span style="color:var(--accent);">*</span>
                    </label>
                    <textarea name="motivo_rechazo"
                              class="form-control @error('motivo_rechazo') is-invalid @enderror"
                              rows="2"
                              placeholder="Describe el motivo del rechazo...">{{ old('motivo_rechazo') }}</textarea>
                    @error('motivo_rechazo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="3"
                              placeholder="Notas opcionales...">{{ old('observaciones') }}</textarea>
                </div>

            </div>
        </div>

        <div class="card page-card">
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Registrar recepción
                    </button>
                    <a href="{{ route('ordenes_compra.show', $orden) }}"
                       class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- Columna derecha: líneas --}}
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-4">Líneas de la orden</h6>

                @foreach($orden->detalles as $i => $detalle)
                    <div class="linea-card mb-3">

                        <input type="hidden"
                               name="lineas[{{ $i }}][detalle_orden_id]"
                               value="{{ $detalle->id }}">

                        {{-- Header de la línea --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                @if($detalle->esPorCaracteristicas())
                                    <span class="badge-tipo-caracteristicas mb-1"
                                          style="display:inline-block;">
                                        Características
                                    </span>
                                    <div class="fw-semibold" style="font-size:14px;">
                                        {{ $detalle->caracteristicas_solicitadas['descripcion'] ?? '—' }}
                                    </div>
                                    @if(!empty($detalle->caracteristicas_solicitadas['notas']))
                                        <div style="font-size:12px; color:var(--text-muted);">
                                            {{ $detalle->caracteristicas_solicitadas['notas'] }}
                                        </div>
                                    @endif
                                @else
                                    <span class="badge-tipo-variante mb-1"
                                          style="display:inline-block;">
                                        Variante
                                    </span>
                                    <div class="fw-semibold" style="font-size:14px;">
                                        {{ $detalle->variante?->producto?->nombre }}
                                    </div>
                                    <div style="font-size:12px; color:var(--text-muted);">
                                        @foreach($detalle->variante?->valores ?? [] as $valor)
                                            {{ $valor->atributo?->nombre }}: {{ $valor->valor }}
                                            @if(!$loop->last) · @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="text-end">
                                <div style="font-size:11px; color:var(--text-muted);">
                                    Solicitado
                                </div>
                                <div style="font-size:18px; font-weight:700;
                                            color:var(--text-primary);">
                                    {{ $detalle->cantidad_solicitada }}
                                </div>
                                <div style="font-size:11px; color:var(--text-muted);">
                                    Recibido: {{ $detalle->cantidad_recibida }}
                                </div>
                            </div>
                        </div>

                        {{-- Si es por características: selector de variante --}}
                        @if($detalle->esPorCaracteristicas())
                            <div class="mb-3 p-3"
                                 style="border:1px dashed var(--border);
                                        border-radius:var(--radius-sm);
                                        background:var(--bg-surface);">
                                <label style="font-size:11px; font-weight:600;
                                              text-transform:uppercase; letter-spacing:0.06em;
                                              color:var(--text-muted); margin-bottom:8px;
                                              display:block;">
                                    Asociar a variante del sistema
                                </label>
                                <select id="varianteCaract-{{ $i }}"
                                        name="lineas[{{ $i }}][variante_id]"
                                        placeholder="Buscar variante o dejar sin asociar...">
                                </select>
                                <p style="font-size:11px; color:var(--text-muted);
                                          margin-top:6px; margin-bottom:0;">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Si no asocias una variante, el stock quedará
                                    <strong>pendiente de asociar</strong>.
                                </p>
                            </div>
                        @else
                            <input type="hidden"
                                   name="lineas[{{ $i }}][variante_id]"
                                   value="{{ $detalle->variante_producto_id }}">
                        @endif

                        {{-- Cantidades y calidad --}}
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label style="font-size:11px; font-weight:600;
                                              text-transform:uppercase; letter-spacing:0.06em;
                                              color:var(--text-muted); margin-bottom:6px;
                                              display:block;">
                                    Cant. recibida *
                                </label>
                                <input type="number"
                                       name="lineas[{{ $i }}][cantidad_recibida]"
                                       class="form-control @error('lineas.'.$i.'.cantidad_recibida') is-invalid @enderror"
                                       value="{{ old('lineas.'.$i.'.cantidad_recibida', $detalle->cantidad_solicitada - $detalle->cantidad_recibida) }}"
                                       min="0" step="1">
                                @error('lineas.'.$i.'.cantidad_recibida')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label style="font-size:11px; font-weight:600;
                                              text-transform:uppercase; letter-spacing:0.06em;
                                              color:var(--text-muted); margin-bottom:6px;
                                              display:block;">
                                    Cant. aceptada *
                                </label>
                                <input type="number"
                                       name="lineas[{{ $i }}][cantidad_aceptada]"
                                       class="form-control @error('lineas.'.$i.'.cantidad_aceptada') is-invalid @enderror"
                                       value="{{ old('lineas.'.$i.'.cantidad_aceptada', $detalle->cantidad_solicitada - $detalle->cantidad_recibida) }}"
                                       min="0" step="1">
                                @error('lineas.'.$i.'.cantidad_aceptada')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label style="font-size:11px; font-weight:600;
                                              text-transform:uppercase; letter-spacing:0.06em;
                                              color:var(--text-muted); margin-bottom:6px;
                                              display:block;">
                                    Calidad *
                                </label>
                                <select name="lineas[{{ $i }}][estado_calidad]"
                                        class="form-select">
                                    <option value="conforme"
                                            {{ old('lineas.'.$i.'.estado_calidad', 'conforme') === 'conforme' ? 'selected' : '' }}>
                                        Conforme
                                    </option>
                                    <option value="no_conforme"
                                            {{ old('lineas.'.$i.'.estado_calidad') === 'no_conforme' ? 'selected' : '' }}>
                                        No conforme
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label style="font-size:11px; font-weight:600;
                                              text-transform:uppercase; letter-spacing:0.06em;
                                              color:var(--text-muted); margin-bottom:6px;
                                              display:block;">
                                    Observación
                                </label>
                                <input type="text"
                                       name="lineas[{{ $i }}][observacion]"
                                       class="form-control"
                                       value="{{ old('lineas.'.$i.'.observacion') }}"
                                       placeholder="Opcional">
                            </div>
                        </div>

                    </div>
                @endforeach

            </div>
        </div>
    </div>

</div>
</form>
@endsection

@push('styles')
    @vite(['resources/css/ordenes_compra.css'])
@endpush

@push('scripts')
    @vite(['resources/js/recepciones.js'])
@endpush