@extends('layouts.app')

@section('page_title', 'Crear producto')
@section('page_subtitle', 'Registra un nuevo producto en el catálogo')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h5 class="mb-1">Nuevo producto</h5>
                    <p class="text-muted mb-0">Completa la información para registrar el producto.</p>
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

                <form action="{{ route('productos.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text"
                               name="nombre"
                               id="nombre"
                               class="form-control"
                               value="{{ old('nombre') }}"
                               placeholder="Ej: Camisa casual">
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion"
                                  id="descripcion"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Describe el producto">{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="marca" class="form-label">Marca</label>
                        <input type="text"
                               name="marca"
                               id="marca"
                               class="form-control"
                               value="{{ old('marca') }}"
                               placeholder="Ej: Zara">
                    </div>

                    <div class="mb-4">
                        <label for="categoria_id" class="form-label">Categoría</label>
                        <select name="categoria_id" id="categoria_id" class="form-select">
                            <option value="">Seleccione una categoría</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}"
                                    {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                            Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection