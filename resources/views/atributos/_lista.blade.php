{{-- Lista de atributos (parcial AJAX) --}}

@forelse($atributos as $atributo)
    <div class="atributo-card {{ $loop->first ? 'open' : '' }}">

        {{-- Header --}}
        <div class="atributo-card__header">
            <div class="atributo-card__left">
                <svg class="atributo-card__chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9" />
                </svg>
                <div>
                    <span class="atributo-card__nombre">{{ $atributo->nombre }}</span>
                    <span class="atributo-card__meta ms-2">
                        {{ $atributo->valores_count }}
                        {{ $atributo->valores_count === 1 ? 'valor activo' : 'valores activos' }}
                    </span>
                </div>
                @if (!$atributo->estado)
                    <span class="badge rounded-pill ms-2"
                        style="background:rgba(158,158,158,0.15); color:#757575;
                                 font-size:10px; padding:3px 8px;">
                        Inactivo
                    </span>
                @endif
            </div>

            <div class="atributo-card__actions">
                @permiso('productos.gestionar')
                    <button class="btn btn-outline-warning btn-sm" title="Editar nombre" data-open-modal="editarAtributo"
                        data-id="{{ $atributo->id }}" data-nombre="{{ $atributo->nombre }}">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    @if ($atributo->estado)
                        <form action="{{ route('atributos.destroy', $atributo) }}" method="POST" class="d-inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm" title="Desactivar"
                                onclick="return confirm('¿Desactivar el atributo {{ $atributo->nombre }}?')">
                                <i class="bi bi-toggle-on"></i>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('atributos.reactivar', $atributo) }}" method="POST" class="d-inline-block">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-outline-success btn-sm" title="Reactivar"
                                onclick="return confirm('¿Reactivar el atributo {{ $atributo->nombre }}?')">
                                <i class="bi bi-toggle-off"></i>
                            </button>
                        </form>
                    @endif
                @endpermiso
            </div>
        </div>

        {{-- Body: valores --}}
        <div class="atributo-card__body">

            @if ($atributo->valores->count() > 0)
                <table class="valores-table">
                    <thead>
                        <tr>
                            <th>Valor</th>
                            <th style="width:80px; text-align:center;">Orden</th>
                            <th style="width:100px;">Estado</th>
                            <th style="width:100px;" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($atributo->valores->sortBy('orden') as $valor)
                            <tr>
                                <td class="fw-semibold">{{ $valor->valor }}</td>
                                <td style="text-align:center; color:var(--text-muted); font-size:12px;">
                                    {{ $valor->orden }}
                                </td>
                                <td>
                                    @if ($valor->estado)
                                        <span class="badge rounded-pill"
                                            style="background:rgba(76,175,80,0.12); color:#2e7d32;
                                                     font-size:10px; padding:3px 8px;">
                                            <span
                                                style="display:inline-block; width:5px; height:5px;
                                                         border-radius:50%; background:#2e7d32;
                                                         margin-right:4px; vertical-align:middle;"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span class="badge rounded-pill"
                                            style="background:rgba(158,158,158,0.15); color:#757575;
                                                     font-size:10px; padding:3px 8px;">
                                            <span
                                                style="display:inline-block; width:5px; height:5px;
                                                         border-radius:50%; background:#9e9e9e;
                                                         margin-right:4px; vertical-align:middle;"></span>
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @permiso('productos.gestionar')
                                        <button class="btn btn-outline-warning btn-sm" title="Editar valor"
                                            data-open-modal="editarValor" data-atributo-id="{{ $atributo->id }}"
                                            data-valor-id="{{ $valor->id }}" data-valor="{{ $valor->valor }}"
                                            data-orden="{{ $valor->orden }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        @if ($valor->estado)
                                            <form action="{{ route('atributos.valores.destroy', [$atributo, $valor]) }}"
                                                method="POST" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm" title="Desactivar valor"
                                                    onclick="return confirm('¿Desactivar el valor {{ $valor->valor }}?')">
                                                    <i class="bi bi-toggle-on"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('atributos.valores.reactivar', [$atributo, $valor]) }}"
                                                method="POST" class="d-inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-outline-success btn-sm" title="Reactivar valor"
                                                    onclick="return confirm('¿Reactivar el valor {{ $valor->valor }}?')">
                                                    <i class="bi bi-toggle-off"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        —
                                    @endpermiso
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="font-size:13px; color:var(--text-muted); margin-bottom:12px;">
                    No hay valores registrados para este atributo.
                </p>
            @endif

            @permiso('productos.gestionar')
                {{-- Formulario agregar nuevo valor --}}
                <form action="{{ route('atributos.valores.store', $atributo) }}" method="POST" class="form-nuevo-valor">
                    @csrf
                    <input type="text" name="valor" class="form-control"
                        placeholder="Nuevo valor (ej: XL, Verde, Algodón...)" required autocomplete="off">
                    <button type="submit" class="btn btn-primary btn-sm" style="white-space:nowrap;">
                        <i class="bi bi-plus-circle me-1"></i> Agregar
                    </button>
                </form>
            @endpermiso

        </div>
    </div>
@empty
    <div class="text-center py-5" style="color:var(--text-muted);">
        @if (request('buscar') || request('estado'))
            <i class="bi bi-search" style="font-size:32px; display:block; margin-bottom:8px;"></i>
            No se encontraron atributos con ese criterio.
            <br>
            <a href="{{ route('atributos.index') }}" class="btn btn-secondary btn-sm mt-3">
                Limpiar filtros
            </a>
        @else
            <i class="bi bi-inbox" style="font-size:32px; display:block; margin-bottom:8px;"></i>
            No hay atributos registrados todavía.
        @endif
    </div>
@endforelse

{{-- Paginación --}}
@if ($atributos->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4 pt-3"
        style="border-top:1px solid var(--border);">

        <span style="font-size:13px; color:var(--text-muted);">
            Mostrando {{ $atributos->firstItem() }}–{{ $atributos->lastItem() }}
            de {{ $atributos->total() }} atributos
        </span>

        <nav>
            <ul class="pagination pagination-sm mb-0 em-pagination">
                <li class="page-item {{ $atributos->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link ajax-page" href="{{ $atributos->previousPageUrl() }}">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>

                @foreach ($atributos->getUrlRange(1, $atributos->lastPage()) as $page => $url)
                    <li class="page-item {{ $page == $atributos->currentPage() ? 'active' : '' }}">
                        <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach

                <li class="page-item {{ !$atributos->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link ajax-page" href="{{ $atributos->nextPageUrl() }}">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
@endif

{{-- Actualizar contador --}}
<script>
    const contador = document.getElementById('contadorAtributos');
    if (contador) contador.textContent = '{{ $atributos->total() }} atributos registrados';
</script>
