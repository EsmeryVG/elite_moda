@forelse($ventas as $venta)
    <tr>
        <td>
            <span style="font-family:monospace; font-size:12px; color:var(--text-muted);">
                {{ $venta->codigo }}
            </span>
        </td>
        <td style="font-size:12px; color:var(--text-muted);">
            {{ $venta->fecha->format('d/m/Y H:i') }}
        </td>
        <td style="font-size:13px;">
            {{ $venta->cliente?->nombre }} {{ $venta->cliente?->apellido }}
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $venta->empleado?->nombre_completo ?? '—' }}
        </td>
        <td style="font-family:monospace; font-size:11px; color:var(--text-muted);">
            {{ $venta->ncf }}
        </td>
        <td>
            @php
                $badge = match($venta->estado) {
                    'pendiente'  => 'badge-venta-pendiente',
                    'completada' => 'badge-venta-completada',
                    'anulada'    => 'badge-venta-anulada',
                    default      => 'badge-venta-pendiente',
                };
            @endphp
            <span class="{{ $badge }}">
                {{ ucfirst($venta->estado) }}
            </span>
        </td>
        <td class="text-end fw-semibold" style="font-size:13px;">
            RD$ {{ number_format($venta->total, 2) }}
        </td>
        <td class="text-end">
            <a href="{{ route('ventas.show', $venta) }}"
               class="btn btn-outline-info btn-sm" title="Ver detalle">
                <i class="bi bi-eye"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center py-5" style="color:var(--text-muted);">
            @if(request('buscar') || request('estado'))
                <i class="bi bi-search" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No se encontraron ventas con ese criterio.
                <br>
                <a href="{{ route('ventas.index') }}" class="btn btn-secondary btn-sm mt-3">
                    Limpiar filtros
                </a>
            @else
                <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No hay ventas registradas.
            @endif
        </td>
    </tr>
@endforelse

@if($ventas->hasPages())
    <tr>
        <td colspan="8">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $ventas->firstItem() }}–{{ $ventas->lastItem() }}
                    de {{ $ventas->total() }} ventas
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $ventas->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $ventas->previousPageUrl() }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @foreach($ventas->getUrlRange(1, $ventas->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $ventas->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$ventas->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $ventas->nextPageUrl() }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif