@extends('layouts.app')

@section('page_title', 'Productos')
@section('page_subtitle', 'Administra el catálogo y sus categorías')

@section('content')
<div class="card page-card">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-1">Listado de productos</h5>
                <p class="text-muted mb-0">Consulta, actualiza y elimina productos del sistema.</p>
            </div>
            <a href="{{ route('productos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Nuevo producto
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success rounded-3">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Marca</th>
                        <th>Categoría</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                        <tr>
                            <td>{{ $producto->id }}</td>
                            <td class="fw-semibold">{{ $producto->nombre }}</td>
                            <td>{{ $producto->marca }}</td>
                            <td>
                                <span class="badge badge-soft">
                                    {{ $producto->categoria?->nombre }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('productos.show', $producto) }}" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Eliminar producto?')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No hay productos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection