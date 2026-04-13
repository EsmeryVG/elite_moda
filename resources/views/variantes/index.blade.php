@extends('layouts.app')

@section('page_title', 'Variantes')
@section('page_subtitle', 'Consulta todas las variantes registradas en el sistema')

@section('content')
<div class="card page-card">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-1">Listado de variantes</h5>
                <p class="text-muted mb-0">Visualiza color, talla, material, precio y el producto al que pertenecen.</p>
            </div>
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
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Color</th>
                        <th>Talla</th>
                        <th>Material</th>
                        <th>Precio</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($variantes as $variante)
                        <tr>
                            <td>{{ $variante->codigo }}</td>
                            <td class="fw-semibold">{{ $variante->producto?->nombre }}</td>
                            <td>{{ $variante->color }}</td>
                            <td>{{ $variante->talla }}</td>
                            <td>{{ $variante->material }}</td>
                            <td>RD$ {{ number_format($variante->precio_venta, 2) }}</td>
                            <td class="text-end">
                                <a href="{{ route('productos.show', $variante->producto_id) }}" class="btn btn-outline-info btn-sm" title="Ver producto">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route('variantes.edit', $variante) }}" class="btn btn-outline-warning btn-sm" title="Editar variante">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form action="{{ route('variantes.destroy', $variante) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar variante" onclick="return confirm('¿Eliminar esta variante?')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No hay variantes registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection