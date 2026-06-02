@extends('layouts.app')

@section('page_title', 'Nueva Sucursal')
@section('page_subtitle', 'Registra una nueva sucursal')

@section('content')
<div class="row g-4">

    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Información de la sucursal</p>

                <form action="{{ route('sucursales.store') }}"
                      method="POST" id="formSucursal">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">
                            Nombre <span style="color:var(--accent);">*</span>
                        </label>
                        <input type="text" name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre') }}"
                               placeholder="Ej: Sucursal Centro"
                               autofocus>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion"
                               class="form-control"
                               value="{{ old('direccion') }}"
                               placeholder="Dirección de la sucursal">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                      <input type="text" name="telefono"
                        class="form-control @error('telefono') is-invalid @enderror"
                        value="{{ old('telefono', $sucursal->telefono ?? '') }}"
                        placeholder="8090000000"
                        maxlength="10"
                        inputmode="numeric">
                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">10 dígitos sin guiones ni espacios.</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="es_principal"
                                   class="form-check-input"
                                   id="esPrincipal" value="1"
                                   {{ old('es_principal') ? 'checked' : '' }}>
                            <label class="form-check-label" for="esPrincipal">
                                Marcar como sucursal principal
                            </label>
                        </div>
                        <small class="text-muted">
                            Solo puede haber una sucursal principal a la vez.
                        </small>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="text-muted mb-4" style="font-size:12.5px; line-height:1.6;">
                    Se asignará el código <strong>SUC-001</strong> automáticamente
                    y quedará <strong>activa</strong>.
                </p>
                <div class="d-grid gap-2">
                    <button type="submit" form="formSucursal" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Crear sucursal
                    </button>
                    <a href="{{ route('sucursales.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
    @vite(['resources/css/sucursales.css'])
@endpush