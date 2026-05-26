@extends('layouts.app')

@section('page_title', 'Atributos')
@section('page_subtitle', 'Gestiona los atributos y sus valores para las variantes de productos')

@section('content')
<div class="card page-card w-100">
    <div class="card-body p-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-1 fw-semibold">Atributos y valores</h5>
                <p class="text-muted mb-0" style="font-size:13px;" id="contadorAtributos">
                    {{ $atributos->total() }} atributos registrados
                </p>
            </div>
            <button class="btn btn-primary"
                    data-open-modal="nuevoAtributo">
                <i class="bi bi-plus-circle me-1"></i> Nuevo atributo
            </button>
        </div>

        {{-- Buscador + Filtros --}}
        <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">

            <div style="position:relative; flex:1; max-width:320px;">
                <i class="bi bi-search"
                   style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                          color:var(--text-muted); font-size:13px; pointer-events:none;"></i>
                <input type="text"
                       id="buscadorAtributos"
                       class="form-control"
                       placeholder="Buscar atributo..."
                       style="padding-left:36px;"
                       value="{{ request('buscar') }}"
                       autocomplete="off">
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-sm em-filtro {{ !request('estado') ? 'active' : '' }}"
                        data-filtro="todos" data-estado="">
                    Todos
                </button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'activos' ? 'active' : '' }}"
                        data-filtro="activos" data-estado="activos">
                    Activos
                </button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'inactivos' ? 'active' : '' }}"
                        data-filtro="inactivos" data-estado="inactivos">
                    Inactivos
                </button>
            </div>

        </div>

        {{-- Lista de atributos (AJAX) --}}
        <div id="listaAtributos" data-url="{{ route('atributos.index') }}">
            @include('atributos._lista')
        </div>

    </div>
</div>

{{-- ══ Modal: Nuevo atributo ══════════════════════════ --}}
<div class="em-modal-overlay" id="modalNuevoAtributo">
    <div class="em-modal">
        <h6 class="em-modal__title">Nuevo atributo</h6>

        <form action="{{ route('atributos.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">
                    Nombre <span style="color:var(--accent);">*</span>
                </label>
                <input type="text"
                       name="nombre"
                       class="form-control @error('nombre') is-invalid @enderror"
                       placeholder="Ej: Talla, Color, Material..."
                       value="{{ old('nombre') }}"
                       autofocus
                       autocomplete="off">
                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">
                    Después podrás agregar los valores desde esta misma pantalla.
                </small>
            </div>

            <div class="em-modal__actions">
                <button type="button"
                        class="btn btn-secondary"
                        data-close-modal="modalNuevoAtributo">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Crear atributo
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══ Modal: Editar atributo ═════════════════════════ --}}
<div class="em-modal-overlay" id="modalEditarAtributo">
    <div class="em-modal">
        <h6 class="em-modal__title">Editar atributo</h6>

        <form id="formEditarAtributo" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="modalAtributoId">

            <div class="mb-3">
                <label class="form-label">
                    Nombre <span style="color:var(--accent);">*</span>
                </label>
                <input type="text"
                       name="nombre"
                       id="modalAtributoNombre"
                       class="form-control"
                       required
                       autocomplete="off">
            </div>

            <div class="em-modal__actions">
                <button type="button"
                        class="btn btn-secondary"
                        data-close-modal="modalEditarAtributo">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══ Modal: Editar valor ════════════════════════════ --}}
<div class="em-modal-overlay" id="modalEditarValor">
    <div class="em-modal">
        <h6 class="em-modal__title">Editar valor</h6>

        <form id="formEditarValor" method="POST">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label class="form-label">
                    Valor <span style="color:var(--accent);">*</span>
                </label>
                <input type="text"
                       name="valor"
                       id="modalValorTexto"
                       class="form-control"
                       required
                       autocomplete="off">
            </div>

            <div class="mb-3">
                <label class="form-label">Orden</label>
                <input type="number"
                       name="orden"
                       id="modalValorOrden"
                       class="form-control"
                       min="0"
                       style="width:120px;">
                <small class="text-muted">
                    Define el orden en que aparece en los dropdowns.
                </small>
            </div>

            <div class="em-modal__actions">
                <button type="button"
                        class="btn btn-secondary"
                        data-close-modal="modalEditarValor">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
    @vite(['resources/css/atributos.css'])
@endpush

@push('scripts')
    @vite(['resources/js/atributos.js'])
@endpush