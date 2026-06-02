@extends('layouts.app')

@section('page_title', 'Nuevo Proveedor')
@section('page_subtitle', 'Registra un nuevo proveedor')

@section('content')
<div class="row g-4">

    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Información del proveedor</p>

                <form action="{{ route('proveedores.store') }}"
                      method="POST" id="formProveedor">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                Nombre <span style="color:var(--accent);">*</span>
                            </label>
                            <input type="text" name="nombre"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre') }}"
                                   placeholder="Nombre del proveedor"
                                   autofocus>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">RNC</label>
                            <input type="text" name="rnc"
                                   class="form-control @error('rnc') is-invalid @enderror"
                                   value="{{ old('rnc') }}"
                                   placeholder="9 a 11 dígitos"
                                   maxlength="11">
                            @error('rnc')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono"
                                   class="form-control @error('telefono') is-invalid @enderror"
                                   value="{{ old('telefono') }}"
                                   placeholder="10 dígitos sin guiones"
                                   maxlength="10"
                                   inputmode="numeric">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">10 dígitos sin guiones ni espacios.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="correo@ejemplo.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Persona de contacto</label>
                            <input type="text" name="contacto_nombre"
                                   class="form-control"
                                   value="{{ old('contacto_nombre') }}"
                                   placeholder="Nombre del contacto">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion"
                                   class="form-control"
                                   value="{{ old('direccion') }}"
                                   placeholder="Dirección del proveedor">
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="text-muted mb-4" style="font-size:12.5px; line-height:1.6;">
                    Se asignará el código <strong>PROV-001</strong> automáticamente
                    y quedará <strong>activo</strong>.
                </p>
                <div class="d-grid gap-2">
                    <button type="submit" form="formProveedor" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Crear proveedor
                    </button>
                    <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
    @vite(['resources/css/proveedores.css'])
@endpush