@forelse($productos as $producto)
    {{-- Fila del Producto Principal --}}
    <tr class="producto-row {{ $producto->variantes->count() > 0 ? '' : 'no-expand' }} align-middle"
        data-producto-id="{{ $producto->id }}">

        <td style="width: 32px;" class="text-center">
            @if($producto->variantes->count() > 0)
                <svg class="chevron-icon text-secondary" xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                     style="width: 16px; height: 16px; cursor: pointer; transition: transform 0.2s;">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            @endif
        </td>

        <td>
            <span class="text-secondary fw-mono" style="font-size: 12px;">
                {{ $producto->codigo }}
            </span>
        </td>

        <td class="fw-semibold text-dark">{{ $producto->nombre }}</td>

        <td class="text-muted" style="font-size: 13px;">
            {{ $producto->marca ?? '—' }}
        </td>

        <td>
            <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 11px;">
                {{ $producto->categoria?->nombre ?? '—' }}
            </span>
        </td>

        <td class="text-center text-muted fw-medium" style="font-size: 13px;">
            {{ $producto->variantes->count() }}
        </td>

        <td>
            @if($producto->estado)
                <span class="text-success small fw-medium">● Activo</span>
            @else
                <span class="text-muted small fw-medium">● Inactivo</span>
            @endif
        </td>

        <td class="text-end">
    <a href="{{ route('productos.show', $producto) }}"
       class="btn btn-outline-info btn-sm" title="Ver detalle">
        <i class="bi bi-eye"></i>
    </a>
    <a href="{{ route('productos.edit', $producto) }}"
       class="btn btn-outline-warning btn-sm" title="Editar">
        <i class="bi bi-pencil-square"></i>
    </a>
    @if($producto->estado)
        <form action="{{ route('productos.desactivar', $producto) }}"
              method="POST" class="d-inline-block">
            @csrf @method('PATCH')
            <button class="btn btn-outline-danger btn-sm"
                    title="Desactivar"
                    onclick="return confirm('¿Desactivar {{ $producto->nombre }}?')">
                <i class="bi bi-toggle-on"></i>
            </button>
        </form>
    @else
        <form action="{{ route('productos.reactivar', $producto) }}"
              method="POST" class="d-inline-block">
            @csrf @method('PATCH')
            <button class="btn btn-outline-success btn-sm"
                    title="Reactivar"
                    onclick="return confirm('¿Reactivar {{ $producto->nombre }}?')">
                <i class="bi bi-toggle-off"></i>
            </button>
        </form>
    @endif
</td>
    </tr>

    {{-- Fila expandible de variantes corregida --}}
    @if($producto->variantes->count() > 0)
        <tr class="variantes-row" id="variantes-{{ $producto->id }}">
            <td colspan="8" class="p-3">
                <div class="variantes-inner p-3 bg-white rounded border shadow-sm mx-4">
                    <div class="d-flex align-items-center mb-2">
                        <span class="fw-semibold text-secondary" style="font-size: 13px;">Desglose de Combinaciones</span>
                    </div>
                    
                    {{-- Corrección del Ancho: Añadidas clases estructurales de Bootstrap --}}
                    <table class="table table-sm table-hover table-striped align-middle mb-0 w-100" style="font-size: 13px;">
                        <thead class="table-light text-muted">
                            <tr>
                                <th style="width: 140px;">Código Variante</th>
                                <th>Combinación / Atributos</th>
                                <th style="width: 150px;" class="text-end">Precio de Venta</th>
                                <th style="width: 100px;" class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($producto->variantes as $variante)
                                <tr>
                                    <td class="fw-mono text-secondary" style="font-size: 12px;">
                                        {{ $variante->codigo }}
                                    </td>
                                    <td>
                                        @if($variante->es_default)
                                            <span class="text-muted fst-italic">Producto estándar (Simple)</span>
                                        @else
                                            @foreach($variante->valores as $valor)
                                                <span class="badge bg-light text-dark border me-1" style="font-size: 11px;">
                                                    <strong>{{ $valor->atributo?->nombre }}:</strong> {{ $valor->valor }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold text-dark">
                                        RD$ {{ number_format($variante->precio_venta, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if($variante->estado)
                                            <span class="text-success" style="font-size: 12px;">● Activa</span>
                                        @else
                                            <span class="text-muted" style="font-size: 12px;">● Inactiva</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    @endif

@empty
    <tr>
        <td colspan="8" class="text-center py-5 text-muted">
            @if(request('buscar') || request('estado') || request('categoria'))
                <i class="bi bi-search d-block mb-2" style="font-size: 28px;"></i>
                No se encontraron productos con esos criterios de búsqueda.
                <br>
                <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-sm mt-3">
                    Limpiar filtros
                </a>
            @else
                <i class="bi bi-inbox d-block mb-2" style="font-size: 28px;"></i>
                No hay productos registrados en el catálogo.
                <br>
                <a href="{{ route('productos.create') }}" class="btn btn-primary btn-sm mt-3">
                    Crear el primer producto
                </a>
            @endif
        </td>
    </tr>
@endforelse

{{-- Fila de Paginación --}}
@if($productos->hasPages())
    <tr>
        <td colspan="8" class="bg-light-subtle">
            <div class="d-flex justify-content-between align-items-center py-2 px-3">
                <span class="text-muted small">
                    Mostrando {{ $productos->firstItem() }}–{{ $productos->lastItem() }}
                    de {{ $productos->total() }} productos
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $productos->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $productos->previousPageUrl() }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @foreach($productos->getUrlRange(1, $productos->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $productos->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$productos->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $url ?? $productos->nextPageUrl() }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif

<tr>
    <td colspan="8" style="display:none;" id="contadorRow">
        <script>
            const el = document.getElementById('contadorProductos');
            if (el) el.textContent = '{{ $productos->total() }} productos registrados';
        </script>
    </td>
</tr>