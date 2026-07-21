@forelse($proveedores as $proveedor)
    <tr>
        <td>
            <span style="font-family:monospace; font-size:12px; color:var(--text-muted);">
                {{ $proveedor->codigo }}
            </span>
        </td>
        <td>
            <div class="fw-semibold">{{ $proveedor->nombre }}</div>
            @if ($proveedor->contacto_nombre)
                <div style="font-size:12px; color:var(--text-muted);">
                    {{ $proveedor->contacto_nombre }}
                </div>
            @endif
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $proveedor->rnc ?? '—' }}
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $proveedor->telefono ?? '—' }}
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $proveedor->email ?? '—' }}
        </td>
        <td>
            @if ($proveedor->estado)
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
            <a href="{{ route('proveedores.show', $proveedor) }}" class="btn btn-outline-info btn-sm"
                title="Ver detalle">
                <i class="bi bi-eye"></i>
            </a>
            @permiso('compras.gestionar')
                <a href="{{ route('proveedores.edit', $proveedor) }}" class="btn btn-outline-warning btn-sm" title="Editar">
                    <i class="bi bi-pencil-square"></i>
                </a>
                @if ($proveedor->estado)
                    <form action="{{ route('proveedores.destroy', $proveedor) }}" method="POST" class="d-inline-block">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm" title="Desactivar"
                            onclick="return confirm('¿Desactivar {{ $proveedor->nombre }}?')">
                            <i class="bi bi-toggle-on"></i>
                        </button>
                    </form>
                @else
                    <form action="{{ route('proveedores.reactivar', $proveedor) }}" method="POST" class="d-inline-block">
                        @csrf @method('PATCH')
                        <button class="btn btn-outline-success btn-sm" title="Reactivar"
                            onclick="return confirm('¿Reactivar {{ $proveedor->nombre }}?')">
                            <i class="bi bi-toggle-off"></i>
                        </button>
                    </form>
                @endif
            @endpermiso
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-5" style="color:var(--text-muted);">
            @if (request('buscar') || request('estado'))
                <i class="bi bi-search" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No se encontraron proveedores con ese criterio.
                <br>
                <a href="{{ route('proveedores.index') }}" class="btn btn-secondary btn-sm mt-3">
                    Limpiar filtros
                </a>
            @else
                <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No hay proveedores registrados.
            @endif
        </td>
    </tr>
@endforelse

@if ($proveedores->hasPages())
    <tr>
        <td colspan="7">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $proveedores->firstItem() }}–{{ $proveedores->lastItem() }}
                    de {{ $proveedores->total() }} proveedores
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $proveedores->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $proveedores->previousPageUrl() }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @foreach ($proveedores->getUrlRange(1, $proveedores->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $proveedores->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$proveedores->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $proveedores->nextPageUrl() }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif
