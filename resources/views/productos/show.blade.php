@extends('layouts.app')

@section('page_title', 'Detalle del producto')
@section('page_subtitle', 'Consulta la información del producto y administra sus variantes')

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card page-card">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h5 class="mb-1">Información del producto</h5>
                    <p class="text-muted mb-0">Datos generales registrados en el sistema.</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success rounded-3">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-12">
                     <label class="form-label text-muted">Código</label>
                      <div class="form-control bg-light">{{ $producto->codigo }}</div>
                 </div>

                    <div class="col-12">
                        <label class="form-label text-muted">Nombre</label>
                        <div class="form-control bg-light">{{ $producto->nombre }}</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-muted">Marca</label>
                        <div class="form-control bg-light">{{ $producto->marca ?: 'Sin marca registrada.' }}</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-muted">Categoría</label>
                        <div class="form-control bg-light">{{ $producto->categoria?->nombre }}</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-muted">Descripción</label>
                        <div class="form-control bg-light" style="min-height: 120px;">
                            {{ $producto->descripcion ?: 'Sin descripción registrada.' }}
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('productos.edit', $producto) }}" class="btn btn-warning">
                        <i class="bi bi-pencil-square me-1"></i> Editar producto
                    </a>
                    <a href="{{ route('productos.index', $producto) }}" class="btn btn-secondary">
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card page-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="mb-1">Variantes del producto</h5>
                        <p class="text-muted mb-0">Gestiona colores, tallas, materiales y precios.</p>
                    </div>

                    <a href="{{ route('variantes.create', $producto) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Agregar variante
                    </a>
                </div>

                <div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Código</th>
                <th>Combinación</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($producto->variantes as $variante)
                <tr>
                    <td>{{ $variante->codigo }}</td>

                    <td>
                        @forelse($variante->valores as $valor)
                            <span class="badge bg-light text-dark border me-1 mb-1">
                                {{ $valor->atributo?->nombre }}: {{ $valor->valor }}
                            </span>
                        @empty
                            <span class="text-muted">Sin atributos</span>
                        @endforelse
                    </td>

                    <td>{{ $variante->descripcion ?: 'Sin descripción' }}</td>

                    <td>RD$ {{ number_format($variante->precio_venta, 2) }}</td>

                    <td class="text-end">
                        <a href="{{ route('variantes.edit', $variante) }}" class="btn btn-outline-warning btn-sm" title="Editar variante">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <form action="{{ route('variantes.destroy', $variante) }}" method="POST" class="d-inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-outline-danger btn-sm"
                                    title="Eliminar variante"
                                    onclick="return confirm('¿Eliminar esta variante?')">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        No hay variantes registradas para este producto.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
            </div>
        </div>
    </div>
</div>
@endsection