@forelse($devoluciones as $devolucion)
    <tr>
        <td style="font-size:13px; font-family:monospace;">
            {{ $devolucion->codigo }}
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $devolucion->fecha->format('d/m/Y') }}
        </td>
        <td style="font-size:13px;">
            {{ $devolucion->cliente?->nombre }} {{ $devolucion->cliente?->apellido }}
        </td>
        <td style="font-size:12px; color:var(--text-muted); font-family:monospace;">
            {{ $devolucion->venta?->codigo }}
        </td>
        <td class="text-end fw-semibold" style="font-size:13px;">
            RD$ {{ number_format($devolucion->total, 2) }}
        </td>
        <td>
            @php
                $badge = match ($devolucion->estado) {
                    'pendiente' => 'badge-devolucion-pendiente',
                    'aprobada' => 'badge-devolucion-aprobada',
                    default => 'badge-devolucion-pendiente',
                };
            @endphp
            <span class="{{ $badge }}">
                {{ ucfirst($devolucion->estado) }}
            </span>
        </td>
        <td class="text-end">
            <a href="{{ route('devoluciones.show', $devolucion) }}" class="btn btn-outline-info btn-sm"
                title="Ver detalle">
                <i class="bi bi-eye"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-5" style="color:var(--text-muted);">
            @if (request('busqueda') || request('estado'))
                <i class="bi bi-search" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No se encontraron devoluciones con ese criterio.
                <br>
                <a href="{{ route('devoluciones.index') }}" class="btn btn-secondary btn-sm mt-3">
                    Limpiar filtros
                </a>
            @else
                <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No hay devoluciones registradas.
            @endif
        </td>
    </tr>
@endforelse

@if ($devoluciones->hasPages())
    <tr>
        <td colspan="7">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $devoluciones->firstItem() }}–{{ $devoluciones->lastItem() }}
                    de {{ $devoluciones->total() }} devoluciones
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $devoluciones->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $devoluciones->previousPageUrl() }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @foreach ($devoluciones->getUrlRange(1, $devoluciones->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $devoluciones->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$devoluciones->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $devoluciones->nextPageUrl() }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif
