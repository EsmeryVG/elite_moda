@extends('layouts.app')

@section('page_title', 'Editar Categoría')
@section('page_subtitle', 'Modifica los datos de la categoría')

@section('content')
<div class="row g-4">

    {{-- Columna principal --}}
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-1">Información de la categoría</h6>
                <p class="text-muted mb-4" style="font-size:13px;">
                    Modifica los campos que necesites y guarda los cambios.
                </p>

                <form action="{{ route('categorias.update', $categoria) }}"
                      method="POST"
                      id="formCategoria">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Código</label>
                        <input type="text"
                               class="form-control"
                               value="{{ $categoria->codigo }}"
                               disabled>
                        <small class="text-muted">El código es generado automáticamente y no se puede modificar.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Nombre <span style="color: var(--accent);">*</span>
                        </label>
                        <input type="text"
                               name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $categoria->nombre) }}"
                               placeholder="Nombre de la categoría">
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion"
                                  class="form-control @error('descripcion') is-invalid @enderror"
                                  rows="3"
                                  placeholder="Descripción opcional">{{ old('descripcion', $categoria->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- Columna lateral --}}
    <div class="col-lg-4 d-flex flex-column gap-4">

        {{-- Guardar --}}
        <div class="card page-card">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3">Guardar cambios</h6>
                <div class="d-grid gap-2">
                    <button type="submit" form="formCategoria" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Actualizar categoría
                    </button>
                    <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>

        {{-- Estado --}}
        <div class="card page-card">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3">Estado</h6>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <div style="font-size:13px; font-weight:500;">
                            {{ $categoria->estado ? 'Activa' : 'Inactiva' }}
                        </div>
                        <div style="font-size:12px; color: var(--text-muted);">
                            {{ $categoria->estado
                                ? 'Visible y disponible para productos.'
                                : 'No disponible para nuevos productos.' }}
                        </div>
                    </div>
                    @if($categoria->estado)
                        <span style="width:10px; height:10px; border-radius:50%; background:#4caf50; display:inline-block;"></span>
                    @else
                        <span style="width:10px; height:10px; border-radius:50%; background:#9e9e9e; display:inline-block;"></span>
                    @endif
                </div>

                @if($categoria->estado)
                    <form action="{{ route('categorias.destroy', $categoria) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="btn btn-outline-danger w-100 btn-sm"
                                onclick="return confirm('¿Desactivar la categoría {{ $categoria->nombre }}?')">
                            <i class="bi bi-toggle-on me-1"></i> Desactivar
                        </button>
                    </form>
                @else
                    <form action="{{ route('categorias.reactivar', $categoria) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="btn btn-outline-success w-100 btn-sm"
                                onclick="return confirm('¿Reactivar la categoría {{ $categoria->nombre }}?')">
                            <i class="bi bi-toggle-off me-1"></i> Reactivar
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection