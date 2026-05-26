{{-- Tabla --}}
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th style="width:110px;">Código</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th style="width:100px;">Estado</th>
                <th style="width:120px;" class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categorias as $categoria)
                <tr>
                    <td>
                        <span style="font-family:monospace; font-size:12px; color:var(--text-muted);">
                            {{ $categoria->codigo }}
                        </span>
                    </td>

                    <td class="fw-semibold">{{ $categoria->nombre }}</td>

                    <td style="font-size:13px; color:var(--text-muted);">
                        {{ $categoria->descripcion ?? '—' }}
                    </td>

                    <td>
                        @if($categoria->estado)
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
                        <a href="{{ route('categorias.edit', $categoria) }}"
                           class="btn btn-outline-warning btn-sm"
                           title="Editar">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        @if($categoria->estado)
                            <form action="{{ route('categorias.destroy', $categoria) }}"
                                  method="POST" class="d-inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm"
                                        title="Desactivar"
                                        onclick="return confirm('¿Desactivar {{ $categoria->nombre }}?')">
                                    <i class="bi bi-toggle-on"></i>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('categorias.reactivar', $categoria) }}"
                                  method="POST" class="d-inline-block">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-outline-success btn-sm"
                                        title="Reactivar"
                                        onclick="return confirm('¿Reactivar {{ $categoria->nombre }}?')">
                                    <i class="bi bi-toggle-off"></i>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5" style="color:var(--text-muted);">
                        @if(request('buscar') || request('estado'))
                            <i class="bi bi-search" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                            No se encontraron categorías con ese criterio.
                            <br>
                            <a href="{{ route('categorias.index') }}" class="btn btn-secondary btn-sm mt-3">
                                Limpiar filtros
                            </a>
                        @else
                            <i class="bi bi-inbox" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                            No hay categorías registradas.
                            <br>
                            <a href="{{ route('categorias.create') }}" class="btn btn-primary btn-sm mt-3">
                                Crear la primera categoría
                            </a>
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación --}}
@if($categorias->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4 pt-3"
         style="border-top:1px solid var(--border);">

        <span style="font-size:13px; color:var(--text-muted);">
            Mostrando {{ $categorias->firstItem() }}–{{ $categorias->lastItem() }}
            de {{ $categorias->total() }} categorías
        </span>

        <nav>
            <ul class="pagination pagination-sm mb-0 em-pagination">
                <li class="page-item {{ $categorias->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link ajax-page" href="{{ $categorias->previousPageUrl() }}">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>

                @foreach($categorias->getUrlRange(1, $categorias->lastPage()) as $page => $url)
                    <li class="page-item {{ $page == $categorias->currentPage() ? 'active' : '' }}">
                        <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach

                <li class="page-item {{ !$categorias->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link ajax-page" href="{{ $categorias->nextPageUrl() }}">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>

    </div>
@endif

{{-- Contador actualizado --}}
<script>
    document.getElementById('contadorCategorias').textContent =
        '{{ $categorias->total() }} categorías registradas';
</script>