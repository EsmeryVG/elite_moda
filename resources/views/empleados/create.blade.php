@extends('layouts.app')

@section('page_title', 'Nuevo Empleado')
@section('page_subtitle', 'Registra un nuevo empleado')

@section('content')
<div class="row g-4">

    <div class="col-lg-8">

        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <p class="prod-section-title">Información personal</p>

                <form action="{{ route('empleados.store') }}"
                      method="POST" id="formEmpleado">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                Nombre <span style="color:var(--accent);">*</span>
                            </label>
                            <input type="text" name="nombre"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre') }}"
                                   placeholder="Nombre" autofocus>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                Apellido <span style="color:var(--accent);">*</span>
                            </label>
                            <input type="text" name="apellido"
                                   class="form-control @error('apellido') is-invalid @enderror"
                                   value="{{ old('apellido') }}"
                                   placeholder="Apellido">
                            @error('apellido')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                Cédula <span style="color:var(--accent);">*</span>
                            </label>
                            <input type="text" name="cedula"
                                   class="form-control @error('cedula') is-invalid @enderror"
                                   value="{{ old('cedula') }}"
                                   placeholder="11 dígitos sin guiones"
                                   maxlength="11" inputmode="numeric">
                            @error('cedula')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono"
                                   class="form-control @error('telefono') is-invalid @enderror"
                                   value="{{ old('telefono') }}"
                                   placeholder="10 dígitos sin guiones"
                                   maxlength="10" inputmode="numeric">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion"
                                   class="form-control"
                                   value="{{ old('direccion') }}"
                                   placeholder="Dirección del empleado">
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <p class="prod-section-title">Información laboral</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Cargo</label>
                        <input type="text" name="cargo" form="formEmpleado"
                               class="form-control"
                               value="{{ old('cargo') }}"
                               placeholder="Ej: Cajero, Vendedor, Supervisor">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha de ingreso</label>
                        <input type="date" name="fecha_ingreso" form="formEmpleado"
                               class="form-control"
                               value="{{ old('fecha_ingreso') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Salario base</label>
                        <div class="input-group">
                            <span class="input-group-text"
                                  style="background:var(--bg-elevated);
                                         border-color:var(--border);
                                         color:var(--text-muted); font-size:13px;">
                                RD$
                            </span>
                            <input type="number" name="salario_base" form="formEmpleado"
                                   class="form-control @error('salario_base') is-invalid @enderror"
                                   value="{{ old('salario_base', 0) }}"
                                   placeholder="0.00" step="0.01" min="0">
                            @error('salario_base')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Comisión por venta (%)</label>
                        <div class="input-group">
                            <input type="number" name="comision_porcentaje" form="formEmpleado"
                                   class="form-control @error('comision_porcentaje') is-invalid @enderror"
                                   value="{{ old('comision_porcentaje', 0) }}"
                                   placeholder="0" step="0.01" min="0" max="100">
                            <span class="input-group-text"
                                  style="background:var(--bg-elevated);
                                         border-color:var(--border);
                                         color:var(--text-muted);">%</span>
                            @error('comision_porcentaje')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Usuario del sistema</p>
                <p style="font-size:12.5px; color:var(--text-muted); margin-bottom:16px;">
                    Opcional — asocia este empleado a un usuario del sistema.
                </p>

                <select name="user_id" form="formEmpleado" class="form-select">
                    <option value="">Sin usuario asignado</option>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id }}"
                                {{ old('user_id') == $usuario->id ? 'selected' : '' }}>
                            {{ $usuario->name }} — {{ $usuario->email }}
                            @if($usuario->rol) ({{ $usuario->rol->nombre }}) @endif
                        </option>
                    @endforeach
                </select>

            </div>
        </div>

    </div>

    <div class="col-lg-4">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="text-muted mb-4" style="font-size:12.5px; line-height:1.6;">
                    Se asignará el código <strong>EMP-001</strong> automáticamente
                    y quedará <strong>activo</strong>.
                </p>
                <div class="d-grid gap-2">
                    <button type="submit" form="formEmpleado" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Crear empleado
                    </button>
                    <a href="{{ route('empleados.index') }}" class="btn btn-secondary">
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