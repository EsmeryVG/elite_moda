@forelse($empleados as $empleado)
    <tr>
        <td>
            <span style="font-family:monospace; font-size:12px; color:var(--text-muted);">
                {{ $empleado->codigo }}
            </span>
        </td>
        <td>
            <div class="fw-semibold" style="font-size:13px;">
                {{ $empleado->nombre_completo }}
            </div>
            <div style="font-size:12px; color:var(--text-muted);">
                {{ $empleado->cedula }}
            </div>
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $empleado->cargo ?? '—' }}
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $empleado->telefono ?? '—' }}
        </td>
        <td>
            @if ($empleado->usuario)
                <div style="font-size:12px;">
                    <div class="fw-semibold" style="color:var(--text-primary);">
                        {{ $empleado->usuario->name }}
                    </div>
                    <div style="color:var(--text-muted);">
                        {{ $empleado->usuario->rol?->nombre ?? 'Sin rol' }}
                    </div>
                </div>
            @else
                <span style="font-size:12px; color:var(--text-muted); font-style:italic;">
                    Sin usuario
                </span>
            @endif
        </td>
        <td>
            @if ($empleado->estado)
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
            @permiso('usuarios.gestionar')
                <a href="{{ route('empleados.edit', $empleado) }}" class="btn btn-outline-warning btn-sm" title="Editar">
                    <i class="bi bi-pencil-square"></i>
                </a>
                @if ($empleado->estado)
                    <form action="{{ route('empleados.destroy', $empleado) }}" method="POST" class="d-inline-block">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm" title="Desactivar"
                            onclick="return confirm('¿Desactivar {{ $empleado->nombre_completo }}?')">
                            <i class="bi bi-toggle-on"></i>
                        </button>
                    </form>
                @else
                    <form action="{{ route('empleados.reactivar', $empleado) }}" method="POST" class="d-inline-block">
                        @csrf @method('PATCH')
                        <button class="btn btn-outline-success btn-sm" title="Reactivar"
                            onclick="return confirm('¿Reactivar {{ $empleado->nombre_completo }}?')">
                            <i class="bi bi-toggle-off"></i>
                        </button>
                    </form>
                @endif
            @else
                —
            @endpermiso
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-5" style="color:var(--text-muted);">
            @if (request('buscar') || request('estado'))
                <i class="bi bi-search" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No se encontraron empleados con ese criterio.
                <br>
                <a href="{{ route('empleados.index') }}" class="btn btn-secondary btn-sm mt-3">
                    Limpiar filtros
                </a>
            @else
                <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No hay empleados registrados.
            @endif
        </td>
    </tr>
@endforelse
@if ($empleados->hasPages())
    <tr>
        <td colspan="7">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $empleados->firstItem() }}–{{ $empleados->lastItem() }}
                    de {{ $empleados->total() }} empleados
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $empleados->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $empleados->previousPageUrl() }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @foreach ($empleados->getUrlRange(1, $empleados->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $empleados->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$empleados->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $empleados->nextPageUrl() }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif
