@php
    function badgeRol($rol)
    {
        if (!$rol) {
            return '<span class="badge-rol-sin-rol">Sin rol</span>';
        }
        return match (strtolower($rol->nombre)) {
            'administrador' => '<span class="badge-rol-administrador">' . $rol->nombre . '</span>',
            'cajero' => '<span class="badge-rol-cajero">' . $rol->nombre . '</span>',
            'contable' => '<span class="badge-rol-contable">' . $rol->nombre . '</span>',
            default => '<span class="badge-rol-sin-rol">' . $rol->nombre . '</span>',
        };
    }
@endphp

@forelse($usuarios as $usuario)
    <tr>
        <td>
            <div class="d-flex align-items-center gap-3">
                <div class="user-avatar">
                    {{ mb_strtoupper(mb_substr($usuario->name, 0, 1), 'UTF-8') }}
                </div>
                <div>
                    <div class="fw-semibold" style="font-size:13px;">
                        {{ $usuario->name }}
                    </div>
                    <div style="font-size:12px; color:var(--text-muted);">
                        {{ $usuario->email }}
                    </div>
                </div>
            </div>
        </td>
        <td>{!! badgeRol($usuario->rol) !!}</td>
        <td>
            @if ($usuario->estado)
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
            <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-outline-warning btn-sm" title="Editar">
                <i class="bi bi-pencil-square"></i>
            </a>
            @if ($usuario->estado)
                <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="d-inline-block">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm" title="Desactivar"
                        onclick="return confirm('¿Desactivar {{ $usuario->name }}?')">
                        <i class="bi bi-toggle-on"></i>
                    </button>
                </form>
            @else
                <form action="{{ route('usuarios.reactivar', $usuario) }}" method="POST" class="d-inline-block">
                    @csrf @method('PATCH')
                    <button class="btn btn-outline-success btn-sm" title="Reactivar"
                        onclick="return confirm('¿Reactivar {{ $usuario->name }}?')">
                        <i class="bi bi-toggle-off"></i>
                    </button>
                </form>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center py-5" style="color:var(--text-muted);">
            @if (request('buscar') || request('estado') || request('rol'))
                <i class="bi bi-search" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No se encontraron usuarios con ese criterio.
                <br>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary btn-sm mt-3">
                    Limpiar filtros
                </a>
            @else
                <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No hay usuarios registrados.
            @endif
        </td>
    </tr>
@endforelse

@if ($usuarios->hasPages())
    <tr>
        <td colspan="4">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $usuarios->firstItem() }}–{{ $usuarios->lastItem() }}
                    de {{ $usuarios->total() }} usuarios
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $usuarios->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $usuarios->previousPageUrl() }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @foreach ($usuarios->getUrlRange(1, $usuarios->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $usuarios->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$usuarios->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $usuarios->nextPageUrl() }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif
