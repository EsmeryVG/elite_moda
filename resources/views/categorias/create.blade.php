@extends('layouts.app')

@section('page_title', 'Nueva Categoría')
@section('page_subtitle', 'Registra una nueva categoría en el catálogo')

@section('content')
<div class="row g-4">

    {{-- Columna principal --}}
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-1">Información de la categoría</h6>
                <p class="text-muted mb-4" style="font-size:13px;">
                    Completa los datos para registrar la categoría.
                </p>

                <form action="{{ route('categorias.store') }}" method="POST" id="formCategoria">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">
                            Nombre <span style="color: var(--accent);">*</span>
                        </label>
                        <input type="text"
                               name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre') }}"
                               placeholder="Ej: Ropa, Calzado, Cosméticos..."
                               autofocus>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion"
                                  class="form-control @error('descripcion') is-invalid @enderror"
                                  rows="3"
                                  placeholder="Descripción opcional de la categoría">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- Columna lateral --}}
    <div class="col-lg-4">
        <div class="card page-card">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3">Guardar</h6>

                <p class="text-muted mb-4" style="font-size:12.5px; line-height:1.6;">
                    La categoría se creará como <strong>activa</strong> y se le asignará
                    un código automático (CAT-001, CAT-002…).
                </p>

                <div class="d-grid gap-2">
                    <button type="submit" form="formCategoria" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Crear categoría
                    </button>
                    <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection