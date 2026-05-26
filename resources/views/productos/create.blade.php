@extends('layouts.app')

@section('page_title', 'Nuevo Producto')
@section('page_subtitle', 'Registra un nuevo producto en el catálogo')

@section('content')
<form action="{{ route('productos.store') }}" method="POST" id="formCrearProducto">
@csrf

<div class="row g-4">

    {{-- ── Columna izquierda ─────────────────────────── --}}
    <div class="col-lg-4">

        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <p class="prod-section-title">Información general</p>

                <div class="mb-3">
                    <label class="form-label">
                        Nombre <span style="color:var(--accent);">*</span>
                    </label>
                    <input type="text" name="nombre"
                           class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}"
                           placeholder="Ej: Camiseta Nike Dri-Fit"
                           autofocus>
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
                                    {{ old('marca') == $m ? 'selected' : '' }}>
                                {{ $m }}
                            </option>
                        @endforeach
                        <option value="__otra__"
                                {{ old('marca') == '__otra__' ? 'selected' : '' }}>
                            Otra...
                        </option>
                    </select>
                </div>

                <div class="mb-3" id="nuevaMarcaWrapper" style="display:none;">
                    <label class="form-label">Nueva marca</label>
                    <input type="text" name="nueva_marca" class="form-control"
                           value="{{ old('nueva_marca') }}"
                           placeholder="Nombre de la marca">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Categoría <span style="color:var(--accent);">*</span>
                    </label>
                    <select name="categoria_id"
                            class="form-select @error('categoria_id') is-invalid @enderror">
                        <option value="">Selecciona una categoría</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}"
                                    {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoria_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3"
                              placeholder="Descripción opcional">{{ old('descripcion') }}</textarea>
                </div>

            </div>
        </div>

        <div class="card page-card">
            <div class="card-body p-4">
                <p class="text-muted mb-3" style="font-size:12.5px; line-height:1.6;">
                    Se asignará el código <strong>PROD-001</strong> automáticamente
                    y quedará <strong>activo</strong>.
                </p>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Crear producto
                    </button>
                    <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
                <div class="mt-3">
                    <a href="{{ route('atributos.index') }}" target="_blank"
                       style="font-size:12px; color:var(--text-muted);">
                        <i class="bi bi-arrow-up-right-square me-1"></i>
                        Gestionar atributos
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Columna derecha ───────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Tipo de producto</p>

                <div class="tipo-toggle">
                    <button type="button" id="btnSimple" class="tipo-toggle-btn active">
                        <i class="bi bi-box me-1"></i> Producto simple
                    </button>
                    <button type="button" id="btnVariantes" class="tipo-toggle-btn">
                        <i class="bi bi-collection me-1"></i> Con variantes
                    </button>
                </div>

                {{-- Producto simple --}}
                <div id="seccionSimple">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                Precio de venta <span style="color:var(--accent);">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"
                                      style="background:var(--bg-elevated);
                                             border-color:var(--border);
                                             color:var(--text-muted); font-size:13px;">
                                    RD$
                                </span>
                                <input type="number" name="precio_simple"
                                       class="form-control @error('precio_simple') is-invalid @enderror"
                                       value="{{ old('precio_simple') }}"
                                       placeholder="0.00" step="0.01" min="0">
                                @error('precio_simple')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Con variantes --}}
                <div id="seccionVariantes" style="display:none;">

                    <div class="mb-4">
                        <label class="form-label">
                            Atributos que aplican a este producto
                        </label>
                        <select id="selectorAtributos" multiple
                                placeholder="Busca o selecciona atributos...">
                            @foreach($atributos as $atributo)
                                <option value="{{ $atributo->id }}">
                                    {{ $atributo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Bloques de valores --}}
                    <div id="contenedorValores">
                        @foreach($atributos as $atributo)
                            <div class="atributo-grupo"
                                 id="grupo-{{ $atributo->id }}"
                                 data-nombre="{{ $atributo->nombre }}"
                                 style="display:none;">
                                <div class="atributo-grupo-nombre">
                                    {{ $atributo->nombre }}
                                </div>
                                <div class="atributo-checks"
                                     id="checks-{{ $atributo->id }}">
                                    @foreach($atributo->valores as $valor)
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   id="val_{{ $valor->id }}"
                                                   value="{{ $valor->id }}"
                                                   data-valor="{{ $valor->valor }}">
                                            <label class="form-check-label"
                                                   for="val_{{ $valor->id }}">
                                                {{ $valor->valor }}
                                            </label>
                                        </div>
                                    @endforeach

                                    {{-- Nuevo valor inline --}}
                                    <div class="nuevo-valor-wrapper">
                                        <input type="text"
                                               class="form-control form-control-sm input-nuevo-valor"
                                               placeholder="+ Nuevo valor, Enter para agregar"
                                               data-atributo-id="{{ $atributo->id }}"
                                               style="max-width:260px;">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($atributos->count() > 0)
                        <button type="button" id="btnGenerarGrilla"
                                class="btn btn-secondary btn-sm mt-3">
                            <i class="bi bi-grid me-1"></i> Generar combinaciones
                        </button>
                        <div id="contenedorGrilla"></div>
                    @endif

                </div>

            </div>
        </div>
    </div>

</div>
</form>
@endsection

@push('styles')
    @vite(['resources/css/productos.css'])
@endpush

@push('scripts')
    @vite(['resources/js/productos.js'])
@endpush