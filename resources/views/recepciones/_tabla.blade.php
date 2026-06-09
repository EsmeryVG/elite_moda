@forelse($recepciones as $recepcion)
    <tr>
        <td>
            <span style="font-family:monospace; font-size:12px; color:var(--text-muted);">
                {{ $recepcion->codigo }}
            </span>
        </td>
        <td>
            <span style="font-family:monospace; font-size:12px; color:var(--text-muted);">
                {{ $recepcion->orden?->codigo }}
            </span>
        </td>
        <td style="font-size:13px;">
            {{ $recepcion->orden?->proveedor?->nombre ?? '—' }}
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $recepcion->orden?->almacen?->nombre ?? '—' }}
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $recepcion->fecha->format('d/m/Y') }}
        </td>
        <td>
            @php
                $badgeTipo = match($recepcion->tipo) {
                    'completa'    => ['bg' => 'rgba(76,175,80,0.12)',  'color' => '#2e7d32', 'texto' => 'Completa'],
                    'parcial'     => ['bg' => 'rgba(255,152,0,0.12)',  'color' => '#e65100', 'texto' => 'Parcial'],
                    'no_conforme' => ['bg' => 'rgba(211,47,47,0.10)',  'color' => '#c62828', 'texto' => 'No conforme'],
                    default       => ['bg' => 'var(--bg-hover)',       'color' => 'var(--text-muted)', 'texto' => ucfirst($recepcion->tipo)],
                };
            @endphp
            <span class="badge rounded-pill"
                  style="background:{{ $badgeTipo['bg'] }}; color:{{ $badgeTipo['color'] }};
                         font-size:11px; padding:3px 10px;">
                {{ $badgeTipo['texto'] }}
            </span>
        </td>
        <td class="text-end">
            <a href="{{ route('recepciones.show', $recepcion) }}"
               class="btn btn-outline-info btn-sm" title="Ver detalle">
                <i class="bi bi-eye"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-5" style="color:var(--text-muted);">
            @if(request('buscar') || request('tipo'))
                <i class="bi bi-search" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No se encontraron recepciones con ese criterio.
                <br>
                <a href="{{ route('recepciones.index') }}" class="btn btn-secondary btn-sm mt-3">
                    Limpiar filtros
                </a>
            @else
                <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No hay recepciones registradas.
            @endif
        </td>
    </tr>
@endforelse

@if($recepciones->hasPages())
    <tr>
        <td colspan="7">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $recepciones->firstItem() }}–{{ $recepciones->lastItem() }}
                    de {{ $recepciones->total() }} recepciones
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $recepciones->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $recepciones->previousPageUrl() }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @foreach($recepciones->getUrlRange(1, $recepciones->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $recepciones->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$recepciones->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $recepciones->nextPageUrl() }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif