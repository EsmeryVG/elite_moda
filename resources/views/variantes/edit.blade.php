@extends('layouts.app')

@section('page_title', 'Editar variante')
@section('page_subtitle', 'Actualiza los datos de la variante seleccionada')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h5 class="mb-1">Editar variante</h5>
                    <p class="text-muted mb-0">
                        Producto asociado: <span class="fw-semibold">{{ $variante->producto?->nombre }}</span>
                    </p>
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

                <form action="{{ route('variantes.update', $variante) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="producto_id" value="{{ old('producto_id', $variante->producto_id) }}">

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion"
                                  id="descripcion"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Describe la variante">{{ old('descripcion', $variante->descripcion) }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="color" class="form-label">Color</label>
                            <input type="text"
                                   name="color"
                                   id="color"
                                   class="form-control"
                                   value="{{ old('color', $variante->color) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="talla" class="form-label">Talla</label>
                            <input type="text"
                                   name="talla"
                                   id="talla"
                                   class="form-control"
                                   value="{{ old('talla', $variante->talla) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="material" class="form-label">Material</label>
                            <input type="text"
                                   name="material"
                                   id="material"
                                   class="form-control"
                                   value="{{ old('material', $variante->material) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="precio_venta" class="form-label">Precio de venta</label>
                            <input type="number"
                                   step="0.01"
                                   name="precio_venta"
                                   id="precio_venta"
                                   class="form-control"
                                   value="{{ old('precio_venta', $variante->precio_venta) }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('productos.show', $variante->producto_id) }}" class="btn btn-secondary">
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