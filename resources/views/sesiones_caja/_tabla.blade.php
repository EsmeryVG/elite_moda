@forelse($sesiones as $sesion)
    <tr>
        <td style="font-size:13px; font-weight:500;">
            {{ $sesion->caja?->nombre }}
        </td>
        <td style="font-size:13px;">
            {{ $sesion->usuarioApertura?->name ?? '—' }}
        </td>
        <td style="font-size:12px; color:var(--text-muted);">
            {{ $sesion->fecha_apertura->format('d/m/Y H:i') }}
        </td>
        <td style="font-size:12px; color:var(--text-muted);">
            {{ $sesion->fecha_cierre?->format('d/m/Y H:i') ?? '—' }}
        </td>
        <td class="text-end" style="font-size:13px;">
            RD$ {{ number_format($sesion->monto_cierre_esperado, 2) }}
        </td>
        <td class="text-end" style="font-size:13px;">
            @if (is_null($sesion->diferencia))
                —
            @else
                <span
                    style="color:{{ $sesion->diferencia > 0 ? '#2e7d32' : ($sesion->diferencia < 0 ? 'var(--accent)' : 'var(--text-muted)') }}; font-weight:600;">
                    {{ $sesion->diferencia > 0 ? '+' : '' }}RD$ {{ number_format($sesion->diferencia, 2) }}
                </span>
            @endif
        </td>
        <td>
            <span class="{{ $sesion->estado === 'abierta' ? 'badge-sesion-abierta' : 'badge-sesion-cerrada' }}">
                {{ ucfirst($sesion->estado) }}
            </span>
        </td>
        <td class="text-end">
            <a href="{{ route('sesiones_caja.show', $sesion) }}" class="btn btn-outline-info btn-sm"
                title="Ver detalle">
                <i class="bi bi-eye"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center py-5" style="color:var(--text-muted);">
            <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
            No hay sesiones de caja registradas.
        </td>
    </tr>
@endforelse

@if ($sesiones->hasPages())
    <tr>
        <td colspan="8">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $sesiones->firstItem() }}–{{ $sesiones->lastItem() }}
                    de {{ $sesiones->total() }} sesiones
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $sesiones->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $sesiones->previousPageUrl() }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @foreach ($sesiones->getUrlRange(1, $sesiones->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $sesiones->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$sesiones->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $sesiones->nextPageUrl() }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif
