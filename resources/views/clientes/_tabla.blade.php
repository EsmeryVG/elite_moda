@forelse($clientes as $cliente)
    <tr>
        <td>
            <span style="font-family:monospace; font-size:12px; color:var(--text-muted);">
                {{ $cliente->codigo }}
            </span>
        </td>
        <td>
            <div class="fw-semibold">{{ $cliente->nombre_completo }}</div>
            @if ($cliente->cedula)
                <div style="font-size:12px; color:var(--text-muted);">
                    {{ $cliente->cedula }}
                </div>
            @endif
        </td>
        <td>
            @if ($cliente->grupo)
                <span class="badge-grupo">{{ $cliente->grupo->nombre }}</span>
            @else
                <span style="color:var(--text-muted); font-size:12px;">—</span>
            @endif
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $cliente->telefono ?? '—' }}
        </td>
        <td>
            @if ($cliente->credito_activo)
                <div class="credito-activo">
                    <i class="bi bi-check-circle me-1"></i> Activo
                </div>
                <div style="font-size:11px; color:var(--text-muted);">
                    Límite: RD$ {{ number_format($cliente->limite_credito, 2) }}
                </div>
                @if ($cliente->balance_credito > 0)
                    <div class="balance-deuda" style="font-size:11px;">
                        Debe: RD$ {{ number_format($cliente->balance_credito, 2) }}
                    </div>
                @endif
            @else
                <span class="credito-inactivo">Sin crédito</span>
            @endif
        </td>
        <td>
            @if ($cliente->estado)
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
            <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-outline-info btn-sm" title="Ver ficha">
                <i class="bi bi-eye"></i>
            </a>
            @permiso('clientes.gestionar')
                <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-outline-warning btn-sm" title="Editar">
                    <i class="bi bi-pencil-square"></i>
                </a>
                @if ($cliente->estado)
                    <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="d-inline-block">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm" title="Desactivar"
                            onclick="return confirm('¿Desactivar {{ $cliente->nombre_completo }}?')">
                            <i class="bi bi-toggle-on"></i>
                        </button>
                    </form>
                @else
                    <form action="{{ route('clientes.reactivar', $cliente) }}" method="POST" class="d-inline-block">
                        @csrf @method('PATCH')
                        <button class="btn btn-outline-success btn-sm" title="Reactivar"
                            onclick="return confirm('¿Reactivar {{ $cliente->nombre_completo }}?')">
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
            @if (request('buscar') || request('estado') || request('grupo'))
                <i class="bi bi-search" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No se encontraron clientes con ese criterio.
                <br>
                <a href="{{ route('clientes.index') }}" class="btn btn-secondary btn-sm mt-3">
                    Limpiar filtros
                </a>
            @else
                <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No hay clientes registrados.
            @endif
        </td>
    </tr>
@endforelse
@if ($clientes->hasPages())
    <tr>
        <td colspan="7">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $clientes->firstItem() }}–{{ $clientes->lastItem() }}
                    de {{ $clientes->total() }} clientes
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $clientes->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $clientes->previousPageUrl() }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @foreach ($clientes->getUrlRange(1, $clientes->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $clientes->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$clientes->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $clientes->nextPageUrl() }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif
