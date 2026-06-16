@extends('layouts.app')

@section('page_title', 'Nuevo Rol')
@section('page_subtitle', 'Registra un nuevo rol en el sistema')

@section('content')
<div class="row g-4">

    <div class="col-lg-7">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Información del rol</p>

                <form action="{{ route('roles.store') }}" method="POST" id="formRol">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">
                            Nombre <span style="color:var(--accent);">*</span>
                        </label>
                        <input type="text" name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre') }}"
                               placeholder="Ej: Administrador, Cajero, Contable"
                               autofocus>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <input type="text" name="descripcion"
                               class="form-control"
                               value="{{ old('descripcion') }}"
                               placeholder="Descripción opcional del rol">
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card page-card">
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <button type="submit" form="formRol" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Crear rol
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
    @vite(['resources/css/usuarios.css'])
@endpush