@extends('layouts.app')

@section('page_title', 'Editar — ' . $sucursal->nombre)
@section('page_subtitle', 'Modifica los datos de la sucursal')

@section('content')
<div class="row g-4">

    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Información de la sucursal</p>

                <form action="{{ route('sucursales.update', $sucursal) }}"
                      method="POST" id="formSucursal">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Código</label>
                        <input type="text" class="form-control"
                               value="{{ $sucursal->codigo }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Nombre <span style="color:var(--accent);">*</span>
                        </label>
                        <input type="text" name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $sucursal->nombre) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion"
                               class="form-control"
                               value="{{ old('direccion', $sucursal->direccion) }}"
                               placeholder="Dirección de la sucursal">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                      <input type="text" name="telefono"
                        class="form-control @error('telefono') is-invalid @enderror"
                        value="{{ old('telefono', $sucursal->telefono ?? '') }}"
                        placeholder="8090000000"
                        maxlength="10"
                        inputmode="numeric">
                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">10 dígitos sin guiones ni espacios.</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="es_principal"
                                   class="form-check-input"
                                   id="esPrincipal" value="1"
                                   {{ old('es_principal', $sucursal->es_principal) ? 'checked' : '' }}>
                            <label class="form-check-label" for="esPrincipal">
                                Marcar como sucursal principal
                            </label>
                        </div>
                        <small class="text-muted">
                            Solo puede haber una sucursal principal a la vez.
                        </small>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4 d-flex flex-column gap-4">

        <div class="card page-card">
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <button type="submit" form="formSucursal" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Guardar cambios
                    </button>
                    <a href="{{ route('sucursales.index') }}" class="btn btn-secondary">
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
                            {{ $sucursal->estado ? 'Activa' : 'Inactiva' }}
                        </div>
                        <div style="font-size:12px; color:var(--text-muted);">
                            {{ $sucursal->estado ? 'Visible en el sistema.' : 'No disponible.' }}
                        </div>
                    </div>
                    <span style="width:10px; height:10px; border-radius:50%;
                                 background:{{ $sucursal->estado ? '#4caf50' : '#9e9e9e' }};
                                 display:inline-block;"></span>
                </div>

                @if($sucursal->estado)
                    <form action="{{ route('sucursales.destroy', $sucursal) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100 btn-sm"
                                onclick="return confirm('¿Desactivar {{ $sucursal->nombre }}?')">
                            <i class="bi bi-toggle-on me-1"></i> Desactivar
                        </button>
                    </form>
                @else
                    <form action="{{ route('sucursales.reactivar', $sucursal) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-outline-success w-100 btn-sm"
                                onclick="return confirm('¿Reactivar {{ $sucursal->nombre }}?')">
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
    @vite(['resources/css/sucursales.css'])
@endpush