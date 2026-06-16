@extends('layouts.app')

@section('page_title', 'Editar — ' . $rol->nombre)
@section('page_subtitle', 'Modifica los datos del rol')

@section('content')
<div class="row g-4">

    <div class="col-lg-7">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Información del rol</p>

                <form action="{{ route('roles.update', $rol) }}"
                      method="POST" id="formRol">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">
                            Nombre <span style="color:var(--accent);">*</span>
                        </label>
                        <input type="text" name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $rol->nombre) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <input type="text" name="descripcion"
                               class="form-control"
                               value="{{ old('descripcion', $rol->descripcion) }}"
                               placeholder="Descripción opcional del rol">
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5 d-flex flex-column gap-4">

        <div class="card page-card">
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <button type="submit" form="formRol" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Guardar cambios
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>

        <div class="card page-card">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-2">Usuarios con este rol</h6>
                <p style="font-size:28px; font-weight:700; color:var(--text-primary); margin:0;">
                    {{ $rol->usuarios()->count() }}
                </p>
                <p style="font-size:12px; color:var(--text-muted); margin:0;">
                    usuarios asignados
                </p>
            </div>
        </div>

    </div>

</div>
@endsection

@push('styles')
    @vite(['resources/css/usuarios.css'])
@endpush