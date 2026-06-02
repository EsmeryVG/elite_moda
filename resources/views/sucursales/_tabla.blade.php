@forelse($sucursales as $sucursal)
    <tr>
        <td>
            <span style="font-family:monospace; font-size:12px; color:var(--text-muted);">
                {{ $sucursal->codigo }}
            </span>
        </td>
        <td>
            <div class="d-flex align-items-center gap-2">
                <span class="fw-semibold">{{ $sucursal->nombre }}</span>
                @if($sucursal->es_principal)
                    <span class="badge-principal">Principal</span>
                @endif
            </div>
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $sucursal->direccion ?? '—' }}
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $sucursal->telefono ?? '—' }}
        </td>
        <td>
            @if($sucursal->estado)
                <span class="badge rounded-pill"
                      style="background:rgba(76,175,80,0.12); color:#2e7d32;
                             font-size:11px; padding:4px 10px;">
                    <span style="display:inline-block; width:6px; height:6px;
                                 border-radius:50%; background:#2e7d32;
                                 margin-right:5px; vertical-align:middle;"></span>
                    Activa
                </span>
            @else
                <span class="badge rounded-pill"
                      style="background:rgba(158,158,158,0.15); color:#757575;
                             font-size:11px; padding:4px 10px;">
                    <span style="display:inline-block; width:6px; height:6px;
                                 border-radius:50%; background:#9e9e9e;
                                 margin-right:5px; vertical-align:middle;"></span>
                    Inactiva
                </span>
            @endif
        </td>
        <td class="text-end">
            <a href="{{ route('sucursales.edit', $sucursal) }}"
               class="btn btn-outline-warning btn-sm" title="Editar">
                <i class="bi bi-pencil-square"></i>
            </a>

            @if($sucursal->estado)
                <form action="{{ route('sucursales.destroy', $sucursal) }}"
                      method="POST" class="d-inline-block">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm"
                            title="Desactivar"
                            onclick="return confirm('¿Desactivar {{ $sucursal->nombre }}?')">
                        <i class="bi bi-toggle-on"></i>
                    </button>
                </form>
            @else
                <form action="{{ route('sucursales.reactivar', $sucursal) }}"
                      method="POST" class="d-inline-block">
                    @csrf @method('PATCH')
                    <button class="btn btn-outline-success btn-sm"
                            title="Reactivar"
                            onclick="return confirm('¿Reactivar {{ $sucursal->nombre }}?')">
                        <i class="bi bi-toggle-off"></i>
                    </button>
                </form>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-5" style="color:var(--text-muted);">
            @if(request('buscar') || request('estado'))
                <i class="bi bi-search" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No se encontraron sucursales con ese criterio.
                <br>
                <a href="{{ route('sucursales.index') }}" class="btn btn-secondary btn-sm mt-3">
                    Limpiar filtros
                </a>
            @else
                <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No hay sucursales registradas.
            @endif
        </td>
    </tr>
@endforelse

@if($sucursales->hasPages())
    <tr>
        <td colspan="6">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $sucursales->firstItem() }}–{{ $sucursales->lastItem() }}
                    de {{ $sucursales->total() }} sucursales
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $sucursales->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $sucursales->previousPageUrl() }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @foreach($sucursales->getUrlRange(1, $sucursales->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $sucursales->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$sucursales->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $sucursales->nextPageUrl() }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif