@forelse($almacenes as $almacen)
    <tr>
        <td class="fw-semibold">{{ $almacen->nombre }}</td>
        <td>
            @if ($almacen->tipo === 'principal')
                <span class="badge-tipo-principal">Principal</span>
            @else
                <span class="badge-tipo-secundario">Secundario</span>
            @endif
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $almacen->sucursal?->nombre ?? '—' }}
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $almacen->direccion ?? '—' }}
        </td>
        <td>
            @if ($almacen->estado)
                <span class="badge rounded-pill"
                    style="background:rgba(76,175,80,0.12); color:#2e7d32;
                             font-size:11px; padding:4px 10px;">
                    <span
                        style="display:inline-block; width:6px; height:6px;
                                 border-radius:50%; background:#2e7d32;
                                 margin-right:5px; vertical-align:middle;"></span>
                    Activo
                </span>
            @else
                <span class="badge rounded-pill"
                    style="background:rgba(158,158,158,0.15); color:#757575;
                             font-size:11px; padding:4px 10px;">
                    <span
                        style="display:inline-block; width:6px; height:6px;
                                 border-radius:50%; background:#9e9e9e;
                                 margin-right:5px; vertical-align:middle;"></span>
                    Inactivo
                </span>
            @endif
        </td>
        <td class="text-end">
            @permiso('productos.gestionar')
                <a href="{{ route('almacenes.edit', $almacen) }}" class="btn btn-outline-warning btn-sm" title="Editar">
                    <i class="bi bi-pencil-square"></i>
                </a>
                @if ($almacen->estado)
                    <form action="{{ route('almacenes.destroy', $almacen) }}" method="POST" class="d-inline-block">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm" title="Desactivar"
                            onclick="return confirm('¿Desactivar {{ $almacen->nombre }}?')">
                            <i class="bi bi-toggle-on"></i>
                        </button>
                    </form>
                @else
                    <form action="{{ route('almacenes.reactivar', $almacen) }}" method="POST" class="d-inline-block">
                        @csrf @method('PATCH')
                        <button class="btn btn-outline-success btn-sm" title="Reactivar"
                            onclick="return confirm('¿Reactivar {{ $almacen->nombre }}?')">
                            <i class="bi bi-toggle-off"></i>
                        </button>
                    </form>
                @endif
            @else
                <span style="font-size:12px; color:var(--text-muted);">—</span>
            @endpermiso
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-5" style="color:var(--text-muted);">
            @if (request('buscar') || request('estado') || request('sucursal'))
                <i class="bi bi-search" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No se encontraron almacenes con ese criterio.
                <br>
                <a href="{{ route('almacenes.index') }}" class="btn btn-secondary btn-sm mt-3">
                    Limpiar filtros
                </a>
            @else
                <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No hay almacenes registrados.
            @endif
        </td>
    </tr>
@endforelse

@if ($almacenes->hasPages())
    <tr>
        <td colspan="6">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $almacenes->firstItem() }}–{{ $almacenes->lastItem() }}
                    de {{ $almacenes->total() }} almacenes
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $almacenes->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $almacenes->previousPageUrl() }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @foreach ($almacenes->getUrlRange(1, $almacenes->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $almacenes->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$almacenes->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $almacenes->nextPageUrl() }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif
