@extends('layouts.app')

@section('page_title', 'Nuevo Usuario')
@section('page_subtitle', 'Registra un nuevo usuario en el sistema')

@section('content')
<div class="row g-4">

    <div class="col-lg-7">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Información del usuario</p>

                <form action="{{ route('usuarios.store') }}" method="POST" id="formUsuario">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">
                            Nombre <span style="color:var(--accent);">*</span>
                        </label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="Nombre completo"
                               autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Email <span style="color:var(--accent);">*</span>
                        </label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder="correo@ejemplo.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="rol_id"
                                class="form-select @error('rol_id') is-invalid @enderror">
                            <option value="">Sin rol asignado</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id }}"
                                        {{ old('rol_id') == $rol->id ? 'selected' : '' }}>
                                    {{ $rol->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('rol_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Contraseña <span style="color:var(--accent);">*</span>
                        </label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Mínimo 8 caracteres">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Confirmar contraseña <span style="color:var(--accent);">*</span>
                        </label>
                        <input type="password" name="password_confirmation"
                               class="form-control"
                               placeholder="Repite la contraseña">
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="text-muted mb-4" style="font-size:12.5px; line-height:1.6;">
                    El usuario quedará <strong>activo</strong> al crearse.
                </p>
                <div class="d-grid gap-2">
                    <button type="submit" form="formUsuario" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Crear usuario
                    </button>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
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