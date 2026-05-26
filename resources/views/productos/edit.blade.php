@extends('layouts.app')

@section('page_title', 'Editar — ' . $producto->nombre)
@section('page_subtitle', 'Modifica la información y gestiona las variantes')

@section('content')
<div class="row g-4">

    {{-- ── Columna izquierda: info general ──────────── --}}
    <div class="col-lg-4">

        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <p class="prod-section-title">Información general</p>

                <form action="{{ route('productos.update', $producto) }}"
                      method="POST" id="formEditarProducto">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Código</label>
                        <input type="text" class="form-control"
                               value="{{ $producto->codigo }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Nombre <span style="color:var(--accent);">*</span>
                        </label>
                        <input type="text" name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $producto->nombre) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Marca</label>
                        <select name="marca" id="marcaSelect" class="form-select">
                            <option value="">Sin marca</option>
                            @foreach($marcas as $m)
                                <option value="{{ $m }}"
                                    {{ old('marca', in_array($producto->marca, $marcas->toArray()) ? $producto->marca : '__otra__') == $m ? 'selected' : '' }}>
                                    {{ $m }}
                                </option>
                            @endforeach
                            <option value="__otra__"
                                {{ old('marca', in_array($producto->marca, $marcas->toArray()) ? $producto->marca : '__otra__') == '__otra__' ? 'selected' : '' }}>
                                Otra...
                            </option>
                        </select>
                    </div>

                    <div class="mb-3" id="nuevaMarcaWrapper" style="display:none;">
                        <label class="form-label">Nueva marca</label>
                        <input type="text" name="nueva_marca" class="form-control"
                               value="{{ old('nueva_marca', !in_array($producto->marca, $marcas->toArray()) ? $producto->marca : '') }}"
                               placeholder="Nombre de la marca">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Categoría <span style="color:var(--accent);">*</span>
                        </label>
                        <select name="categoria_id" class="form-select" required>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}"
                                        {{ old('categoria_id', $producto->categoria_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control"
                                  rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
                    </div>

                </form>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <button type="submit" form="formEditarProducto"
                            class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Guardar cambios
                    </button>
                    <a href="{{ route('productos.show', $producto) }}"
                       class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>

        {{-- Estado --}}
        <div class="card page-card">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3">Estado</h6>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <div style="font-size:13px; font-weight:500;">
                            {{ $producto->estado ? 'Activo' : 'Inactivo' }}
                        </div>
                        <div style="font-size:12px; color:var(--text-muted);">
                            {{ $producto->estado ? 'Visible en el sistema.' : 'No disponible.' }}
                        </div>
                    </div>
                    <span style="width:10px; height:10px; border-radius:50%;
                                 background:{{ $producto->estado ? '#4caf50' : '#9e9e9e' }};
                                 display:inline-block;"></span>
                </div>

                @if($producto->estado)
                    <form action="{{ route('productos.desactivar', $producto) }}"
                          method="POST">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="btn btn-outline-danger w-100 btn-sm"
                                onclick="return confirm('¿Desactivar {{ $producto->nombre }}?')">
                            <i class="bi bi-toggle-on me-1"></i> Desactivar
                        </button>
                    </form>
                @else
                    <form action="{{ route('productos.reactivar', $producto) }}"
                          method="POST">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="btn btn-outline-success w-100 btn-sm"
                                onclick="return confirm('¿Reactivar {{ $producto->nombre }}?')">
                            <i class="bi bi-toggle-off me-1"></i> Reactivar
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>

    {{-- ── Columna derecha: variantes ─────────────────── --}}
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="fw-semibold mb-1">Variantes</h6>
                        <p class="text-muted mb-0" style="font-size:12px;">
                            {{ $producto->variantes->count() }}
                            {{ $producto->variantes->count() === 1 ? 'variante' : 'variantes' }}
                            registradas
                        </p>
                    </div>
                    @if($producto->tiene_variantes)
                        <button type="button" id="btnMostrarAgregarVariante"
                                class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Agregar variante
                        </button>
                    @endif
                </div>

                {{-- Formulario agregar variante --}}
                @if($producto->tiene_variantes)
                <div id="wrapperAgregarVariante" style="display:none;">
                    <div class="mb-4 p-3"
                         style="border:1px solid var(--border); border-radius:var(--radius-md);
                                background:var(--bg-elevated);">
                        <h6 class="fw-semibold mb-3" style="font-size:13px;">
                            Nueva variante
                        </h6>

                        <form action="{{ route('variantes.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="producto_id"
                                   value="{{ $producto->id }}">

                            <div class="mb-3">
                                <label class="form-label">Atributos</label>
                                <select id="selectorAtributosEdit" multiple
                                        placeholder="Busca o selecciona atributos...">
                                    @foreach($atributos as $atributo)
                                        <option value="{{ $atributo->id }}">
                                            {{ $atributo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="contenedorValoresEdit">
                                @foreach($atributos as $atributo)
                                    <div class="atributo-grupo"
                                         id="grupo-edit-{{ $atributo->id }}"
                                         data-nombre="{{ $atributo->nombre }}"
                                         style="display:none;">
                                        <div class="atributo-grupo-nombre">
                                            {{ $atributo->nombre }}
                                        </div>
                                        <div class="atributo-checks"
                                             id="checks-edit-{{ $atributo->id }}">
                                            @foreach($atributo->valores as $valor)
                                                <div class="form-check">
                                                   <input class="form-check-input"
                                                    type="checkbox"
                                                    id="eval_{{ $valor->id }}"
                                                    name="atributo_valor_ids[]"
                                                    value="{{ $valor->id }}"
                                                    data-valor="{{ $valor->valor }}">
                                                    <label class="form-check-label"
                                                           for="eval_{{ $valor->id }}">
                                                        {{ $valor->valor }}
                                                    </label>
                                                </div>
                                            @endforeach
                                            <div class="nuevo-valor-wrapper">
                                                <input type="text"
                                                       class="form-control form-control-sm input-nuevo-valor-edit"
                                                       placeholder="+ Nuevo valor, Enter para agregar"
                                                       data-atributo-id="{{ $atributo->id }}"
                                                       data-prefix="edit"
                                                       style="max-width:260px;">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Precio de venta
                                        <span style="color:var(--accent);">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"
                                              style="background:var(--bg-elevated);
                                                     border-color:var(--border);
                                                     color:var(--text-muted); font-size:13px;">
                                            RD$
                                        </span>
                                        <input type="number" name="precio_venta"
                                               class="form-control"
                                               placeholder="0.00" step="0.01"
                                               min="0" required>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-3">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-check-circle me-1"></i> Agregar
                                </button>
                                <button type="button" id="btnCancelarAgregarVariante"
                                        class="btn btn-secondary btn-sm">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                {{-- Tabla de variantes --}}
                <div class="table-responsive">
                    <table class="table align-middle" id="tablaVariantes">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Combinación</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($producto->variantes as $variante)
                                {{-- Fila principal --}}
                                <tr id="fila-{{ $variante->id }}">
                                    <td style="font-family:monospace; font-size:12px;
                                               color:var(--text-muted);">
                                        {{ $variante->codigo }}
                                    </td>
                                    <td>
                                        @if($variante->es_default)
                                            <span style="font-size:12px;
                                                         color:var(--text-muted);
                                                         font-style:italic;">
                                                Producto simple
                                            </span>
                                        @else
                                            @foreach($variante->valores as $valor)
                                                <span class="badge"
                                                      style="background:var(--bg-hover);
                                                             color:var(--text-secondary);
                                                             font-size:11px; padding:3px 8px;
                                                             margin-right:3px;">
                                                    {{ $valor->atributo?->nombre }}: {{ $valor->valor }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>RD$ {{ number_format($variante->precio_venta, 2) }}</td>
                                    <td>
                                        @if($variante->estado)
                                            <span style="color:#2e7d32; font-size:12px;">
                                                ● Activa
                                            </span>
                                        @else
                                            <span style="color:#9e9e9e; font-size:12px;">
                                                ● Inactiva
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        {{-- Editar inline --}}
                                        <button type="button"
                                                class="btn btn-outline-warning btn-sm"
                                                title="Editar"
                                                onclick="toggleEditarVariante({{ $variante->id }})">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        {{-- Desactivar / Reactivar --}}
                                        @if($variante->estado)
                                            <form action="{{ route('variantes.desactivar', $variante) }}"
                                                  method="POST" class="d-inline-block">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-outline-danger btn-sm"
                                                        title="Desactivar"
                                                        onclick="return confirm('¿Desactivar?')">
                                                    <i class="bi bi-toggle-on"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('variantes.reactivar', $variante) }}"
                                                  method="POST" class="d-inline-block">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-outline-success btn-sm"
                                                        title="Reactivar"
                                                        onclick="return confirm('¿Reactivar?')">
                                                    <i class="bi bi-toggle-off"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Eliminar --}}
                                        @if(!$variante->es_default)
                                            <form action="{{ route('variantes.destroy', $variante) }}"
                                                  method="POST" class="d-inline-block">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm"
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Eliminar esta variante permanentemente?')">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Fila editar inline --}}
                            <tr id="edit-{{ $variante->id }}" style="display:none;">
                                <td colspan="5" style="background:var(--bg-elevated); padding:16px 20px;">
                                    <form action="{{ route('variantes.update', $variante) }}"
                                        method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="producto_id" value="{{ $producto->id }}">

                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-4">
                                                <label class="form-label" style="font-size:12px;">
                                                    Precio de venta *
                                                </label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text"
                                                        style="background:var(--bg-surface);
                                                                border-color:var(--border);
                                                                color:var(--text-muted);">
                                                        RD$
                                                    </span>
                                                    <input type="number" name="precio_venta"
                                                        class="form-control"
                                                        value="{{ $variante->precio_venta }}"
                                                        step="0.01" min="0" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label" style="font-size:12px;">
                                                    Código de barras
                                                </label>
                                                <input type="text" name="codigo_barras"
                                                    class="form-control form-control-sm"
                                                    value="{{ $variante->codigo_barras }}"
                                                    placeholder="Opcional" maxlength="100">
                                            </div>

                                            <div class="col-md-4">
                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="bi bi-save me-1"></i> Guardar
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                            onclick="toggleEditarVariante({{ $variante->id }})">
                                                        Cancelar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </td>
                            </tr>

                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4"
                                        style="color:var(--text-muted);">
                                        No hay variantes registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
    @vite(['resources/css/productos.css'])
@endpush

@push('scripts')
    @vite(['resources/js/productos.js'])
    <script>
        // Toggle fila editar variante
        function toggleEditarVariante(id) {
            const fila     = document.getElementById(`edit-${id}`);
            const visible  = fila.style.display !== 'none';
            fila.style.display = visible ? 'none' : 'table-row';
        }
    </script>
@endpush