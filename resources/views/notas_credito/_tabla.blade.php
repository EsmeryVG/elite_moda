@forelse($notas as $nota)
    <tr>
        <td style="font-size:13px; font-family:monospace; font-weight:500;">
            {{ $nota->codigo }}
        </td>
        <td style="font-size:12px; font-family:monospace; color:var(--text-muted);">
            {{ $nota->ncf }}
        </td>
        <td style="font-size:12px; color:var(--text-muted);">
            {{ $nota->fecha->format('d/m/Y') }}
        </td>
        <td style="font-size:13px;">
            {{ $nota->cliente?->nombre }} {{ $nota->cliente?->apellido }}
        </td>
        <td style="font-size:12px; color:var(--text-muted); font-family:monospace;">
            @if ($nota->devolucion?->venta)
                <a href="{{ route('ventas.show', $nota->devolucion->venta) }}">
                    {{ $nota->devolucion->venta->codigo }}
                </a>
            @else
                —
            @endif
        </td>
        <td class="text-end" style="font-size:13px; color:var(--text-muted);">
            RD$ {{ number_format($nota->monto_original, 2) }}
        </td>
        <td class="text-end fw-semibold" style="font-size:13px;">
            RD$ {{ number_format($nota->monto_disponible, 2) }}
        </td>
        <td>
            @php
                $badge = match ($nota->estado) {
                    'activa' => 'badge-devolucion-aprobada',
                    'agotada' => 'badge-devolucion-pendiente',
                    'vencida' => 'badge-devolucion-vencida',
                    default => 'badge-devolucion-pendiente',
                };
            @endphp
            <span class="{{ $badge }}">{{ ucfirst($nota->estado) }}</span>
        </td>
        <td class="text-end">
            <a href="{{ route('notas_credito.show', $nota) }}" class="btn btn-outline-info btn-sm" title="Ver detalle">
                <i class="bi bi-eye"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="text-center py-5" style="color:var(--text-muted);">
            <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
            No hay notas de crédito registradas.
        </td>
    </tr>
@endforelse

@if ($notas->hasPages())
    <tr>
        <td colspan="9">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $notas->firstItem() }}–{{ $notas->lastItem() }} de {{ $notas->total() }} notas
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $notas->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $notas->previousPageUrl() }}"><i
                                    class="bi bi-chevron-left"></i></a>
                        </li>
                        @foreach ($notas->getUrlRange(1, $notas->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $notas->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$notas->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $notas->nextPageUrl() }}"><i
                                    class="bi bi-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif
