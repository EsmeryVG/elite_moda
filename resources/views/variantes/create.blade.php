@extends('layouts.app')

@section('page_title', 'Crear variante')
@section('page_subtitle', 'Agrega una nueva variante al producto seleccionado')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h5 class="mb-1">Nueva variante</h5>
                    <p class="text-muted mb-0">
                        Producto: <span class="fw-semibold">{{ $producto->nombre }}</span>
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

                <form action="{{ route('variantes.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="producto_id" value="{{ $producto->id }}">

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion"
                                  id="descripcion"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Describe la variante">{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="color" class="form-label">Color</label>
                            <input type="text"
                                   name="color"
                                   id="color"
                                   class="form-control"
                                   value="{{ old('color') }}"
                                   placeholder="Ej: Negro">
                        </div>

                        <div class="col-md-6">
                            <label for="talla" class="form-label">Talla</label>
                            <input type="text"
                                   name="talla"
                                   id="talla"
                                   class="form-control"
                                   value="{{ old('talla') }}"
                                   placeholder="Ej: M">
                        </div>

                        <div class="col-md-6">
                            <label for="material" class="form-label">Material</label>
                            <input type="text"
                                   name="material"
                                   id="material"
                                   class="form-control"
                                   value="{{ old('material') }}"
                                   placeholder="Ej: Algodón">
                        </div>

                        <div class="col-md-6">
                            <label for="precio_venta" class="form-label">Precio de venta</label>
                            <input type="number"
                                   step="0.01"
                                   name="precio_venta"
                                   id="precio_venta"
                                   class="form-control"
                                   value="{{ old('precio_venta') }}"
                                   placeholder="Ej: 1200.00">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('productos.show', $producto) }}" class="btn btn-secondary">
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