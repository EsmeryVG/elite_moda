@forelse($ajustes as $ajuste)
    <tr>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $ajuste->fecha->format('d/m/Y') }}
        </td>
        <td style="font-size:13px;">
            {{ $ajuste->almacen?->nombre }}
        </td>
        <td style="font-size:13px;">
            {{ ucfirst(str_replace('_', ' ', $ajuste->tipo)) }}
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $ajuste->motivo ?? '—' }}
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $ajuste->usuario?->name ?? '—' }}
        </td>
        <td>
            @php
                $badge = match($ajuste->estado) {
                    'pendiente'  => 'badge-ajuste-pendiente',
                    'aprobado'   => 'badge-ajuste-aprobado',
                    'rechazado'  => 'badge-ajuste-rechazado',
                    default      => 'badge-ajuste-pendiente',
                };
            @endphp
            <span class="{{ $badge }}">
                {{ ucfirst($ajuste->estado) }}
            </span>
        </td>
        <td class="text-end">
            <a href="{{ route('ajustes.show', $ajuste) }}"
               class="btn btn-outline-info btn-sm" title="Ver detalle">
                <i class="bi bi-eye"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-5" style="color:var(--text-muted);">
            @if(request('buscar') || request('estado'))
                <i class="bi bi-search" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No se encontraron ajustes con ese criterio.
                <br>
                <a href="{{ route('ajustes.index') }}" class="btn btn-secondary btn-sm mt-3">
                    Limpiar filtros
                </a>
            @else
                <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No hay ajustes registrados.
            @endif
        </td>
    </tr>
@endforelse

@if($ajustes->hasPages())
    <tr>
        <td colspan="7">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $ajustes->firstItem() }}–{{ $ajustes->lastItem() }}
                    de {{ $ajustes->total() }} ajustes
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $ajustes->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $ajustes->previousPageUrl() }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @foreach($ajustes->getUrlRange(1, $ajustes->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $ajustes->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$ajustes->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $ajustes->nextPageUrl() }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif