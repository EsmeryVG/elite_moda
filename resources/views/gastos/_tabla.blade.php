@forelse($gastos as $gasto)
    <tr>
        <td style="font-size:12px; color:var(--text-muted);">
            {{ $gasto->fecha->format('d/m/Y') }}
        </td>
        <td style="font-size:13px; font-weight:500;">
            {{ $gasto->nombre }}
            @if ($gasto->es_reposicion_extraordinaria)
                <span class="badge-extraordinaria ms-1">Extraordinaria</span>
            @endif
        </td>
        <td style="font-size:12.5px; color:var(--text-muted);">
            {{ $gasto->categoria?->nombre ?? '—' }}
        </td>
        <td>
            <span class="{{ $gasto->origen === 'caja_chica' ? 'badge-origen-caja_chica' : 'badge-origen-directo' }}">
                {{ $gasto->origen === 'caja_chica' ? 'Caja chica' : 'Directo' }}
            </span>
        </td>
        <td style="font-size:12.5px; color:var(--text-muted);">
            {{ $gasto->usuario?->name ?? '—' }}
        </td>
        <td class="text-end fw-semibold" style="font-size:13px;">
            RD$ {{ number_format($gasto->monto, 2) }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-5" style="color:var(--text-muted);">
            <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
            No hay gastos registrados con ese criterio.
        </td>
    </tr>
@endforelse

@if ($gastos->hasPages())
    <tr>
        <td colspan="6">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $gastos->firstItem() }}–{{ $gastos->lastItem() }} de {{ $gastos->total() }} gastos
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $gastos->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $gastos->previousPageUrl() }}"><i
                                    class="bi bi-chevron-left"></i></a>
                        </li>
                        @foreach ($gastos->getUrlRange(1, $gastos->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $gastos->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$gastos->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $gastos->nextPageUrl() }}"><i
                                    class="bi bi-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif
