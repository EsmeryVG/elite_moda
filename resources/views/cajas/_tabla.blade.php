@forelse($cajas as $caja)
    <tr>
        <td style="font-size:13px; font-weight:500;">
            {{ $caja->nombre }}
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $caja->sucursal?->nombre ?? '—' }}
        </td>
        <td style="font-size:12px; color:var(--text-muted);">
            {{ $caja->almacen?->nombre ?? 'Se asigna en la primera apertura' }}
        </td>
        <td>
            <span class="{{ $caja->estado === 'activa' ? 'badge-sesion-abierta' : 'badge-sesion-cerrada' }}">
                {{ ucfirst($caja->estado) }}
            </span>
        </td>
        <td class="text-end">
            <a href="{{ route('cajas.edit', $caja) }}" class="btn btn-outline-secondary btn-sm" title="Editar">
                <i class="bi bi-pencil"></i>
            </a>
            @if ($caja->estado === 'activa')
                <form action="{{ route('cajas.destroy', $caja) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Desactivar"
                        onclick="return confirm('¿Desactivar esta caja?')">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </form>
            @else
                <form action="{{ route('cajas.reactivar', $caja) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-outline-success btn-sm" title="Reactivar">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </form>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center py-5" style="color:var(--text-muted);">
            <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
            No hay cajas registradas.
        </td>
    </tr>
@endforelse

@if ($cajas->hasPages())
    <tr>
        <td colspan="5">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $cajas->firstItem() }}–{{ $cajas->lastItem() }}
                    de {{ $cajas->total() }} cajas
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $cajas->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $cajas->previousPageUrl() }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @foreach ($cajas->getUrlRange(1, $cajas->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $cajas->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$cajas->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $cajas->nextPageUrl() }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif
