@php
    function badgeEstado($estado) {
        return match($estado) {
            'borrador'   => 'badge-borrador',
            'enviada'    => 'badge-enviada',
            'parcial'    => 'badge-parcial',
            'completada' => 'badge-completada',
            'cancelada'  => 'badge-cancelada',
            default      => 'badge-borrador',
        };
    }
@endphp

@forelse($ordenes as $orden)
    <tr>
        <td>
            <span style="font-family:monospace; font-size:12px; color:var(--text-muted);">
                {{ $orden->codigo }}
            </span>
        </td>
        <td>
            <div class="fw-semibold">{{ $orden->proveedor?->nombre }}</div>
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $orden->almacen?->nombre }}
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $orden->fecha->format('d/m/Y') }}
        </td>
        <td>
            <span class="{{ badgeEstado($orden->estado) }}">
                {{ ucfirst($orden->estado) }}
            </span>
        </td>
        <td class="text-end fw-semibold" style="font-size:13px;">
            RD$ {{ number_format($orden->total, 2) }}
        </td>
        <td class="text-end">
            <a href="{{ route('ordenes_compra.show', $orden) }}"
               class="btn btn-outline-info btn-sm" title="Ver detalle">
                <i class="bi bi-eye"></i>
            </a>
            @if($orden->estado === 'borrador')
                <a href="{{ route('ordenes_compra.edit', $orden) }}"
                   class="btn btn-outline-warning btn-sm" title="Editar">
                    <i class="bi bi-pencil-square"></i>
                </a>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-5" style="color:var(--text-muted);">
            @if(request('buscar') || request('estado') || request('proveedor'))
                <i class="bi bi-search" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No se encontraron órdenes con ese criterio.
                <br>
                <a href="{{ route('ordenes_compra.index') }}" class="btn btn-secondary btn-sm mt-3">
                    Limpiar filtros
                </a>
            @else
                <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No hay órdenes de compra registradas.
            @endif
        </td>
    </tr>
@endforelse

@if($ordenes->hasPages())
    <tr>
        <td colspan="7">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $ordenes->firstItem() }}–{{ $ordenes->lastItem() }}
                    de {{ $ordenes->total() }} órdenes
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $ordenes->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $ordenes->previousPageUrl() }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @foreach($ordenes->getUrlRange(1, $ordenes->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $ordenes->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$ordenes->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $ordenes->nextPageUrl() }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif