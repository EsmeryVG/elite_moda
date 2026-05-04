@extends('layouts.app')

@section('page_title', 'Detalle de categoría')
@section('page_subtitle', 'Consulta la información de la categoría seleccionada')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h5 class="mb-1">Información de la categoría</h5>
                    <p class="text-muted mb-0">Detalles generales registrados en el sistema.</p>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted">Código</label>
                        <div class="form-control bg-light">{{ $categoria->codigo }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">Estado</label>
                        <div class="form-control bg-light">
                            {{ $categoria->estado ? 'Activa' : 'Inactiva' }}
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-muted">Nombre</label>
                        <div class="form-control bg-light">{{ $categoria->nombre }}</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-muted">Descripción</label>
                        <div class="form-control bg-light" style="min-height: 100px;">
                            {{ $categoria->descripcion ?: 'Sin descripción registrada.' }}
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-warning">
                        <i class="bi bi-pencil-square me-1"></i> Editar
                    </a>
                    <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection