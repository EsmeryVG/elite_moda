@extends('layouts.app')

@section('page_title', 'Editar — ' . $usuario->name)
@section('page_subtitle', 'Modifica los datos del usuario')

@section('content')
<div class="row g-4">

    <div class="col-lg-7">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Información del usuario</p>

                <form action="{{ route('usuarios.update', $usuario) }}"
                      method="POST" id="formUsuario">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">
                            Nombre <span style="color:var(--accent);">*</span>
                        </label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $usuario->name) }}" required>
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
                               value="{{ old('email', $usuario->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="rol_id" class="form-select">
                            <option value="">Sin rol asignado</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id }}"
                                        {{ old('rol_id', $usuario->rol_id) == $rol->id ? 'selected' : '' }}>
                                    {{ $rol->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <hr style="border-color:var(--border); margin:20px 0;">

                    <p class="prod-section-title">Cambiar contraseña</p>
                    <p style="font-size:12px; color:var(--text-muted); margin-bottom:16px;">
                        Deja estos campos vacíos si no deseas cambiar la contraseña.
                    </p>

                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña</label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Mínimo 8 caracteres">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirmar nueva contraseña</label>
                        <input type="password" name="password_confirmation"
                               class="form-control"
                               placeholder="Repite la nueva contraseña">
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5 d-flex flex-column gap-4">

        <div class="card page-card">
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <button type="submit" form="formUsuario" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Guardar cambios
                    </button>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
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
                            {{ $usuario->estado ? 'Activo' : 'Inactivo' }}
                        </div>
                        <div style="font-size:12px; color:var(--text-muted);">
                            {{ $usuario->estado ? 'Puede acceder al sistema.' : 'Sin acceso.' }}
                        </div>
                    </div>
                    <span style="width:10px; height:10px; border-radius:50%;
                                 background:{{ $usuario->estado ? '#4caf50' : '#9e9e9e' }};
                                 display:inline-block;"></span>
                </div>

                @if($usuario->estado)
                    <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100 btn-sm"
                                onclick="return confirm('¿Desactivar {{ $usuario->name }}?')">
                            <i class="bi bi-toggle-on me-1"></i> Desactivar
                        </button>
                    </form>
                @else
                    <form action="{{ route('usuarios.reactivar', $usuario) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-outline-success w-100 btn-sm"
                                onclick="return confirm('¿Reactivar {{ $usuario->name }}?')">
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
    @vite(['resources/css/usuarios.css'])
@endpush