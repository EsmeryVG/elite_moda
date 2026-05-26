@extends('layouts.app')

@section('page_title', $producto->nombre)
@section('page_subtitle', 'Detalle del producto')

@section('content')
<div class="row g-4">

    {{-- Info del producto --}}
    <div class="col-lg-4">
        <div class="card page-card">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h6 class="fw-semibold mb-1">Información general</h6>
                        <p class="text-muted mb-0" style="font-size:12px;">
                            Datos registrados del producto.
                        </p>
                    </div>
                    @if($producto->estado)
                        <span class="badge rounded-pill"
                              style="background:rgba(76,175,80,0.12); color:#2e7d32;
                                     font-size:11px; padding:4px 10px;">
                            Activo
                        </span>
                    @else
                        <span class="badge rounded-pill"
                              style="background:rgba(158,158,158,0.15); color:#757575;
                                     font-size:11px; padding:4px 10px;">
                            Inactivo
                        </span>
                    @endif
                </div>

                <div class="mb-3">
                    <span class="field-label">Código</span>
                    <div class="field-readonly"
                         style="font-family:monospace; font-size:13px;">
                        {{ $producto->codigo }}
                    </div>
                </div>

                <div class="mb-3">
                    <span class="field-label">Nombre</span>
                    <div class="field-readonly">{{ $producto->nombre }}</div>
                </div>

                <div class="mb-3">
                    <span class="field-label">Marca</span>
                    <div class="field-readonly">{{ $producto->marca ?? '—' }}</div>
                </div>

                <div class="mb-3">
                    <span class="field-label">Categoría</span>
                    <div class="field-readonly">
                        {{ $producto->categoria?->nombre ?? '—' }}
                    </div>
                </div>

                <div class="mb-3">
                    <span class="field-label">Descripción</span>
                    <div class="field-readonly multiline">
                        {{ $producto->descripcion ?? 'Sin descripción.' }}
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('productos.edit', $producto) }}"
                       class="btn btn-primary flex-fill">
                        <i class="bi bi-pencil-square me-1"></i> Editar
                    </a>
                    <a href="{{ route('productos.index') }}"
                       class="btn btn-secondary">
                        Volver
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- Variantes --}}
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="fw-semibold mb-1">Variantes</h6>
                        <p class="text-muted mb-0" style="font-size:12px;">
                            {{ $producto->variantes->count() }}
                            {{ $producto->variantes->count() === 1 ? 'variante' : 'variantes' }}
                            registradas
                        </p>
                    </div>
                    <a href="{{ route('productos.edit', $producto) }}"
                       class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil-square me-1"></i> Gestionar variantes
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Combinación</th>
                                <th>Precio</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($producto->variantes as $variante)
                                <tr>
                                    <td style="font-family:monospace; font-size:12px;
                                               color:var(--text-muted);">
                                        {{ $variante->codigo }}
                                    </td>
                                    <td>
                                        @if($variante->es_default)
                                            <span style="font-size:12px;
                                                         color:var(--text-muted);
                                                         font-style:italic;">
                                                Producto simple
                                            </span>
                                        @else
                                            @foreach($variante->valores as $valor)
                                                <span class="badge"
                                                      style="background:var(--bg-hover);
                                                             color:var(--text-secondary);
                                                             font-size:11px; padding:3px 8px;
                                                             margin-right:3px;">
                                                    {{ $valor->atributo?->nombre }}: {{ $valor->valor }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>RD$ {{ number_format($variante->precio_venta, 2) }}</td>
                                    <td>
                                        @if($variante->estado)
                                            <span style="color:#2e7d32; font-size:12px;">
                                                ● Activa
                                            </span>
                                        @else
                                            <span style="color:#9e9e9e; font-size:12px;">
                                                ● Inactiva
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4"
                                        style="color:var(--text-muted);">
                                        No hay variantes registradas.
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

@push('styles')
    @vite(['resources/css/productos.css'])
@endpush