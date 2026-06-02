@extends('layouts.app')

@section('page_title', 'Editar — ' . $almacen->nombre)
@section('page_subtitle', 'Modifica los datos del almacén')

@section('content')
<div class="row g-4">

    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Información del almacén</p>

                <form action="{{ route('almacenes.update', $almacen) }}"
                      method="POST" id="formAlmacen">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">
                            Nombre <span style="color:var(--accent);">*</span>
                        </label>
                        <input type="text" name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $almacen->nombre) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Tipo <span style="color:var(--accent);">*</span>
                        </label>
                        <select name="tipo" class="form-select" required>
                            <option value="principal"
                                    {{ old('tipo', $almacen->tipo) === 'principal' ? 'selected' : '' }}>
                                Principal
                            </option>
                            <option value="secundario"
                                    {{ old('tipo', $almacen->tipo) === 'secundario' ? 'selected' : '' }}>
                                Secundario
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sucursal</label>
                        <select name="sucursal_id" class="form-select">
                            <option value="">Sin sucursal asignada</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}"
                                        {{ old('sucursal_id', $almacen->sucursal_id) == $sucursal->id ? 'selected' : '' }}>
                                    {{ $sucursal->nombre }}
                                    @if($sucursal->es_principal) (Principal) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion"
                               class="form-control"
                               value="{{ old('direccion', $almacen->direccion) }}"
                               placeholder="Dirección del almacén (opcional)">
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4 d-flex flex-column gap-4">

        <div class="card page-card">
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <button type="submit" form="formAlmacen" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Guardar cambios
                    </button>
                    <a href="{{ route('almacenes.index') }}" class="btn btn-secondary">
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
                            {{ $almacen->estado ? 'Activo' : 'Inactivo' }}
                        </div>
                        <div style="font-size:12px; color:var(--text-muted);">
                            {{ $almacen->estado ? 'Disponible para stock.' : 'No disponible.' }}
                        </div>
                    </div>
                    <span style="width:10px; height:10px; border-radius:50%;
                                 background:{{ $almacen->estado ? '#4caf50' : '#9e9e9e' }};
                                 display:inline-block;"></span>
                </div>

                @if($almacen->estado)
                    <form action="{{ route('almacenes.destroy', $almacen) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100 btn-sm"
                                onclick="return confirm('¿Desactivar {{ $almacen->nombre }}?')">
                            <i class="bi bi-toggle-on me-1"></i> Desactivar
                        </button>
                    </form>
                @else
                    <form action="{{ route('almacenes.reactivar', $almacen) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-outline-success w-100 btn-sm"
                                onclick="return confirm('¿Reactivar {{ $almacen->nombre }}?')">
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
    @vite(['resources/css/almacenes.css'])
@endpush