@extends('layouts.app')

@section('page_title', 'Editar — ' . $empleado->nombre_completo)
@section('page_subtitle', 'Modifica los datos del empleado')

@section('content')
<div class="row g-4">

    <div class="col-lg-8">

        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <p class="prod-section-title">Información personal</p>

                <form action="{{ route('empleados.update', $empleado) }}"
                      method="POST" id="formEmpleado">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Código</label>
                            <input type="text" class="form-control"
                                   value="{{ $empleado->codigo }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                Nombre <span style="color:var(--accent);">*</span>
                            </label>
                            <input type="text" name="nombre"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre', $empleado->nombre) }}" required>
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
                                   value="{{ old('apellido', $empleado->apellido) }}" required>
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
                                   value="{{ old('cedula', $empleado->cedula) }}"
                                   maxlength="11" inputmode="numeric" required>
                            @error('cedula')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono"
                                   class="form-control @error('telefono') is-invalid @enderror"
                                   value="{{ old('telefono', $empleado->telefono) }}"
                                   maxlength="10" inputmode="numeric">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion"
                                   class="form-control"
                                   value="{{ old('direccion', $empleado->direccion) }}">
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
                               value="{{ old('cargo', $empleado->cargo) }}"
                               placeholder="Ej: Cajero, Vendedor, Supervisor">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha de ingreso</label>
                        <input type="date" name="fecha_ingreso" form="formEmpleado"
                               class="form-control"
                               value="{{ old('fecha_ingreso', $empleado->fecha_ingreso?->format('Y-m-d')) }}">
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
                                   class="form-control"
                                   value="{{ old('salario_base', $empleado->salario_base) }}"
                                   step="0.01" min="0">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Comisión por venta (%)</label>
                        <div class="input-group">
                            <input type="number" name="comision_porcentaje" form="formEmpleado"
                                   class="form-control"
                                   value="{{ old('comision_porcentaje', $empleado->comision_porcentaje) }}"
                                   step="0.01" min="0" max="100">
                            <span class="input-group-text"
                                  style="background:var(--bg-elevated);
                                         border-color:var(--border);
                                         color:var(--text-muted);">%</span>
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
                                {{ old('user_id', $empleado->user_id) == $usuario->id ? 'selected' : '' }}>
                            {{ $usuario->name }} — {{ $usuario->email }}
                            @if($usuario->rol) ({{ $usuario->rol->nombre }}) @endif
                        </option>
                    @endforeach
                </select>

            </div>
        </div>

    </div>

    <div class="col-lg-4 d-flex flex-column gap-4">

        <div class="card page-card">
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <button type="submit" form="formEmpleado" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Guardar cambios
                    </button>
                    <a href="{{ route('empleados.index') }}" class="btn btn-secondary">
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
                            {{ $empleado->estado ? 'Activo' : 'Inactivo' }}
                        </div>
                        <div style="font-size:12px; color:var(--text-muted);">
                            {{ $empleado->estado ? 'Empleado activo.' : 'Empleado inactivo.' }}
                        </div>
                    </div>
                    <span style="width:10px; height:10px; border-radius:50%;
                                 background:{{ $empleado->estado ? '#4caf50' : '#9e9e9e' }};
                                 display:inline-block;"></span>
                </div>

                @if($empleado->estado)
                    <form action="{{ route('empleados.destroy', $empleado) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100 btn-sm"
                                onclick="return confirm('¿Desactivar {{ $empleado->nombre_completo }}?')">
                            <i class="bi bi-toggle-on me-1"></i> Desactivar
                        </button>
                    </form>
                @else
                    <form action="{{ route('empleados.reactivar', $empleado) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-outline-success w-100 btn-sm"
                                onclick="return confirm('¿Reactivar {{ $empleado->nombre_completo }}?')">
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