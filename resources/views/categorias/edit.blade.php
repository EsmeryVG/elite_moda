@extends('layouts.app')

@section('page_title', 'Editar categoría')
@section('page_subtitle', 'Actualiza la información de la categoría')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h5 class="mb-1">Editar categoría</h5>
                    <p class="text-muted mb-0">Modifica los datos de la categoría y guarda los cambios.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('categorias.update', $categoria) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text"
                               name="nombre"
                               id="nombre"
                               class="form-control"
                               value="{{ old('nombre', $categoria->nombre) }}">
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion"
                                  id="descripcion"
                                  class="form-control"
                                  rows="3">{{ old('descripcion', $categoria->descripcion) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="estado" class="form-label">Estado</label>
                        <select name="estado" id="estado" class="form-select">
                            <option value="1" {{ old('estado', $categoria->estado) == 1 ? 'selected' : '' }}>Activa</option>
                            <option value="0" {{ old('estado', $categoria->estado) == 0 ? 'selected' : '' }}>Inactiva</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
                            Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection