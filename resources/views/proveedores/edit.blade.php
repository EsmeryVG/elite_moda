@extends('layouts.app')

@section('page_title', 'Editar — ' . $proveedor->nombre)
@section('page_subtitle', 'Modifica los datos del proveedor')

@section('content')
<div class="row g-4">

    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Información del proveedor</p>

                <form action="{{ route('proveedores.update', $proveedor) }}"
                      method="POST" id="formProveedor">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Código</label>
                            <input type="text" class="form-control"
                                   value="{{ $proveedor->codigo }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                Nombre <span style="color:var(--accent);">*</span>
                            </label>
                            <input type="text" name="nombre"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre', $proveedor->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">RNC</label>
                            <input type="text" name="rnc"
                                   class="form-control @error('rnc') is-invalid @enderror"
                                   value="{{ old('rnc', $proveedor->rnc) }}"
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
                                   value="{{ old('telefono', $proveedor->telefono) }}"
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
                                   value="{{ old('email', $proveedor->email) }}"
                                   placeholder="correo@ejemplo.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Persona de contacto</label>
                            <input type="text" name="contacto_nombre"
                                   class="form-control"
                                   value="{{ old('contacto_nombre', $proveedor->contacto_nombre) }}"
                                   placeholder="Nombre del contacto">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion"
                                   class="form-control"
                                   value="{{ old('direccion', $proveedor->direccion) }}"
                                   placeholder="Dirección del proveedor">
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4 d-flex flex-column gap-4">

        <div class="card page-card">
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <button type="submit" form="formProveedor" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Guardar cambios
                    </button>
                    <a href="{{ route('proveedores.show', $proveedor) }}"
                       class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>

        <div class="card page-card">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3">Estado</h6>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <div style="font-size:13px; font-weight:500;">
                            {{ $proveedor->estado ? 'Activo' : 'Inactivo' }}
                        </div>
                        <div style="font-size:12px; color:var(--text-muted);">
                            {{ $proveedor->estado ? 'Disponible para órdenes.' : 'No disponible.' }}
                        </div>
                    </div>
                    <span style="width:10px; height:10px; border-radius:50%;
                                 background:{{ $proveedor->estado ? '#4caf50' : '#9e9e9e' }};
                                 display:inline-block;"></span>
                </div>

                @if($proveedor->estado)
                    <form action="{{ route('proveedores.destroy', $proveedor) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100 btn-sm"
                                onclick="return confirm('¿Desactivar {{ $proveedor->nombre }}?')">
                            <i class="bi bi-toggle-on me-1"></i> Desactivar
                        </button>
                    </form>
                @else
                    <form action="{{ route('proveedores.reactivar', $proveedor) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-outline-success w-100 btn-sm"
                                onclick="return confirm('¿Reactivar {{ $proveedor->nombre }}?')">
                            <i class="bi bi-toggle-off me-1"></i> Reactivar
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection

@push('styles')
    @vite(['resources/css/proveedores.css'])
@endpush