@extends('layouts.app')
@section('page_title', 'Editar Comprobante Fiscal')
@section('page_subtitle', 'Actualiza el rango de NCF — ' . $comprobante->prefijo_ncf)
@section('content')
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card page-card">
                <div class="card-body p-4">
                    <p class="prod-section-title">Información del comprobante</p>
                    <form action="{{ route('comprobantes.update', $comprobante) }}" method="POST" id="formComprobante">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">
                                Tipo de comprobante <span style="color:var(--accent);">*</span>
                            </label>
                            <select name="tipo_comprobante"
                                class="form-select @error('tipo_comprobante') is-invalid @enderror">
                                <option value="">Selecciona un tipo</option>
                                <option value="Consumidor Final"
                                    {{ old('tipo_comprobante', $comprobante->tipo_comprobante) === 'Consumidor Final' ? 'selected' : '' }}>
                                    Consumidor Final (B02)
                                </option>
                                <option value="Crédito Fiscal"
                                    {{ old('tipo_comprobante', $comprobante->tipo_comprobante) === 'Crédito Fiscal' ? 'selected' : '' }}>
                                    Crédito Fiscal (B01)
                                </option>
                                <option value="Nota de Crédito"
                                    {{ old('tipo_comprobante', $comprobante->tipo_comprobante) === 'Nota de Crédito' ? 'selected' : '' }}>
                                    Nota de Crédito (B04)
                                </option>
                                <option value="Regímenes Especiales"
                                    {{ old('tipo_comprobante', $comprobante->tipo_comprobante) === 'Regímenes Especiales' ? 'selected' : '' }}>
                                    Regímenes Especiales (B14)
                                </option>
                                <option value="Gubernamental"
                                    {{ old('tipo_comprobante', $comprobante->tipo_comprobante) === 'Gubernamental' ? 'selected' : '' }}>
                                    Gubernamental (B15)
                                </option>
                            </select>
                            @error('tipo_comprobante')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                Prefijo NCF <span style="color:var(--accent);">*</span>
                            </label>
                            <input type="text" name="prefijo_ncf"
                                class="form-control @error('prefijo_ncf') is-invalid @enderror"
                                value="{{ old('prefijo_ncf', $comprobante->prefijo_ncf) }}" placeholder="Ej: B02"
                                maxlength="5">
                            @error('prefijo_ncf')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    Rango inicio <span style="color:var(--accent);">*</span>
                                </label>
                                <input type="number" name="rango_inicio"
                                    class="form-control @error('rango_inicio') is-invalid @enderror"
                                    value="{{ old('rango_inicio', $comprobante->rango_inicio) }}" min="1">
                                @error('rango_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    Rango fin <span style="color:var(--accent);">*</span>
                                </label>
                                <input type="number" name="rango_fin"
                                    class="form-control @error('rango_fin') is-invalid @enderror"
                                    value="{{ old('rango_fin', $comprobante->rango_fin) }}" min="1">
                                @error('rango_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">
                                Fecha de vencimiento <span style="color:var(--accent);">*</span>
                            </label>
                            <input type="date" name="fecha_vencimiento"
                                class="form-control @error('fecha_vencimiento') is-invalid @enderror"
                                value="{{ old('fecha_vencimiento', $comprobante->fecha_vencimiento?->format('Y-m-d')) }}">
                            @error('fecha_vencimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card page-card">
                <div class="card-body p-4">
                    <p class="text-muted mb-3" style="font-size:12.5px; line-height:1.6;">
                        <i class="bi bi-info-circle me-1"></i>
                        El rango de comprobantes debe corresponder exactamente al autorizado
                        por la DGII para tu RNC.
                    </p>
                    <div class="mb-4"
                        style="font-size:12.5px; color:var(--text-muted); background:var(--bg-elevated); padding:10px 12px; border-radius:var(--radius-sm);">
                        Número actual consumido: <strong
                            style="color:var(--text-primary);">{{ $comprobante->numero_actual }}</strong>
                        <br>
                        El rango no puede reducirse por debajo de este número.
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" form="formComprobante" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Guardar cambios
                        </button>
                        <a href="{{ route('comprobantes.index') }}" class="btn btn-secondary">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    @vite(['resources/css/comprobantes.css'])
@endpush
@push('scripts')
    @vite(['resources/js/comprobantes.js'])
@endpush
