@forelse($stocks as $stock)
    <tr>
        <td>
            <div class="fw-semibold" style="font-size:13px;">
                {{ $stock->variante?->producto?->nombre }}
            </div>
            <div style="font-size:11px; color:var(--text-muted);">
                {{ $stock->variante?->producto?->categoria?->nombre }}
            </div>
        </td>
        <td>
            <div style="font-size:12px; color:var(--text-secondary);">
                @foreach ($stock->variante?->valores ?? [] as $valor)
                    <span style="margin-right:6px;">
                        <span style="color:var(--text-muted);">{{ $valor->atributo?->nombre }}:</span>
                        {{ $valor->valor }}
                    </span>
                @endforeach
            </div>
            <div style="font-family:monospace; font-size:11px; color:var(--text-muted);">
                {{ $stock->variante?->codigo }}
            </div>
        </td>
        <td style="font-size:13px;">
            {{ $stock->almacen?->nombre }}
            @if ($stock->almacen?->sucursal)
                <div style="font-size:11px; color:var(--text-muted);">
                    {{ $stock->almacen->sucursal->nombre }}
                </div>
            @endif
        </td>
        <td class="text-center">
            <span
                style="font-size:18px; font-weight:700;
                         color:{{ $stock->cantidad_disponible <= 0
                             ? 'var(--accent)'
                             : ($stock->cantidad_disponible <= $stock->stock_minimo
                                 ? '#e65100'
                                 : 'var(--text-primary)') }};">
                {{ $stock->cantidad_disponible }}
            </span>
        </td>
        <td class="text-center" style="font-size:12px; color:var(--text-muted);">
            {{ $stock->stock_minimo }}
        </td>
        <td>
            @if ($stock->cantidad_disponible <= 0)
                <span class="badge-stock-agotado">
                    <i class="bi bi-x-circle me-1"></i> Agotado
                </span>
            @elseif($stock->cantidad_disponible <= $stock->stock_minimo)
                <span class="badge-stock-critico">
                    <i class="bi bi-exclamation-triangle me-1"></i> Crítico
                </span>
            @endif
        </td>
    </tr>
@empty
    {{-- sin cambios --}}
    <tr>
        <td colspan="6" class="text-center py-5" style="color:var(--text-muted);">
            @if (request('buscar') || request('almacen') || request('nivel'))
                <i class="bi bi-search" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No se encontraron registros con ese criterio.
                <br>
                <a href="{{ route('stock.index') }}" class="btn btn-secondary btn-sm mt-3">
                    Limpiar filtros
                </a>
            @else
                <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No hay stock registrado.
            @endif
        </td>
    </tr>
@endforelse

{{-- paginación sin cambios --}}
@if ($stocks->hasPages())
    <tr>
        <td colspan="6">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $stocks->firstItem() }}–{{ $stocks->lastItem() }}
                    de {{ $stocks->total() }} registros
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $stocks->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $stocks->previousPageUrl() }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @foreach ($stocks->getUrlRange(1, $stocks->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $stocks->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$stocks->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $stocks->nextPageUrl() }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif
