@extends('layouts.app')

@section('page_title', 'Editar — ' . $rol->nombre)
@section('page_subtitle', 'Modifica los datos del rol y sus permisos')

@section('content')
    <form action="{{ route('roles.update', $rol) }}" method="POST" id="formRol">
        @csrf @method('PUT')

        <div class="row g-4">

            <div class="col-lg-4">
                <div class="card page-card mb-4">
                    <div class="card-body p-4">
                        <p class="prod-section-title">Información del rol</p>

                        <div class="mb-3">
                            <label class="form-label">
                                Nombre <span style="color:var(--accent);">*</span>
                            </label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                value="{{ old('nombre', $rol->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Descripción</label>
                            <input type="text" name="descripcion" class="form-control"
                                value="{{ old('descripcion', $rol->descripcion) }}"
                                placeholder="Descripción opcional del rol">
                        </div>

                    </div>
                </div>

                <div class="card page-card mb-4">
                    <div class="card-body p-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
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

            <div class="col-lg-8">
                <div class="card page-card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="prod-section-title mb-0">Permisos</p>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnMarcarTodos">
                                    Marcar todos
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnDesmarcarTodos">
                                    Desmarcar todos
                                </button>
                            </div>
                        </div>

                        <div class="permisos-scroll-area">
                            @foreach ($permisosPorModulo as $modulo => $permisos)
                                <div class="permiso-modulo-box mb-3">
                                    <div class="permiso-modulo-header">
                                        <span>{{ $modulo }}</span>
                                        <label class="permiso-modulo-toggle">
                                            <input type="checkbox" class="check-modulo" data-modulo="{{ $modulo }}">
                                            <span>Todo el módulo</span>
                                        </label>
                                    </div>
                                    <div class="permiso-lista">
                                        @foreach ($permisos as $permiso)
                                            <label class="permiso-item" title="{{ $permiso->clave }}">
                                                <input type="checkbox" name="permisos[]" value="{{ $permiso->id }}"
                                                    class="check-permiso" data-modulo="{{ $modulo }}"
                                                    {{ in_array($permiso->id, $permisosAsignados) ? 'checked' : '' }}>
                                                <span class="permiso-item-nombre">{{ $permiso->nombre }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </form>
@endsection

@push('styles')
    @vite(['resources/css/usuarios.css'])
@endpush

@push('scripts')
    @vite(['resources/js/usuarios.js'])
@endpush
