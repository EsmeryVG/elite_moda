@extends('layouts.app')

@section('page_title', 'Nuevo Ajuste de Inventario')
@section('page_subtitle', 'Registra un conteo físico, merma, daño o corrección')

@section('content')
<form action="{{ route('ajustes.store') }}" method="POST" id="formAjuste">
@csrf

<div class="row g-4">

    <div class="col-lg-4">
        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <p class="prod-section-title">Información general</p>

                <div class="mb-3">
                    <label class="form-label">
                        Almacén <span style="color:var(--accent);">*</span>
                    </label>
                    <select name="almacen_id" id="almacenSelect"
                            class="form-select @error('almacen_id') is-invalid @enderror">
                        <option value="">Selecciona un almacén</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}"
                                    {{ old('almacen_id') == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('almacen_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Tipo de ajuste <span style="color:var(--accent);">*</span>
                    </label>
                    <select name="tipo"
                            class="form-select @error('tipo') is-invalid @enderror">
                        <option value="conteo_fisico"
                                {{ old('tipo', 'conteo_fisico') === 'conteo_fisico' ? 'selected' : '' }}>
                            Conteo físico
                        </option>
                        <option value="merma" {{ old('tipo') === 'merma' ? 'selected' : '' }}>
                            Merma
                        </option>
                        <option value="daño" {{ old('tipo') === 'daño' ? 'selected' : '' }}>
                            Daño
                        </option>
                        <option value="correccion" {{ old('tipo') === 'correccion' ? 'selected' : '' }}>
                            Corrección
                        </option>
                        <option value="otro" {{ old('tipo') === 'otro' ? 'selected' : '' }}>
                            Otro
                        </option>
                    </select>
                    @error('tipo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Fecha <span style="color:var(--accent);">*</span>
                    </label>
                    <input type="date" name="fecha"
                           class="form-control @error('fecha') is-invalid @enderror"
                           value="{{ old('fecha', now()->format('Y-m-d')) }}">
                    @error('fecha')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Motivo</label>
                    <textarea name="motivo" class="form-control" rows="3"
                              placeholder="Ej: Conteo físico de fin de mes...">{{ old('motivo') }}</textarea>
                </div>

            </div>
        </div>

        <div class="card page-card">
            <div class="card-body p-4">
                <p class="text-muted mb-4" style="font-size:12.5px; line-height:1.6;">
                    <i class="bi bi-info-circle me-1"></i>
                    El ajuste quedará <strong>pendiente de aprobación</strong>. El stock
                    solo se actualiza cuando un administrador lo aprueba.
                </p>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Registrar ajuste
                    </button>
                    <a href="{{ route('ajustes.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <p class="prod-section-title mb-0">Líneas del ajuste</p>
                    <button type="button" id="btnAgregarLinea" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Agregar línea
                    </button>
                </div>

                @error('lineas')
                    <div class="alert alert-danger rounded-3 mb-3" style="font-size:13px;">
                        {{ $message }}
                    </div>
                @enderror

                <p class="text-muted mb-3" style="font-size:12px;">
                    <i class="bi bi-info-circle me-1"></i>
                    Selecciona primero el almacén para que la cantidad de sistema cargue correctamente.
                </p>

                <div id="contenedorLineas"></div>

            </div>
        </div>
    </div>

</div>
</form>
@endsection

@push('styles')
    @vite(['resources/css/ajustes.css'])
@endpush

@push('scripts')
    @vite(['resources/js/ajustes.js'])
@endpush