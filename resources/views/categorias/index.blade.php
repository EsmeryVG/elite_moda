@extends('layouts.app')

@section('page_title', 'Categorías')
@section('page_subtitle', 'Gestiona las categorías del catálogo')

@section('content')
<div class="card page-card w-100">
    <div class="card-body p-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-1 fw-semibold">Listado de categorías</h5>
                <p class="text-muted mb-0" style="font-size:13px;" id="contadorCategorias">
                    {{ $categorias->total() }} categorías registradas
                </p>
            </div>
            <a href="{{ route('categorias.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Nueva categoría
            </a>
        </div>

        {{-- Buscador + Filtros --}}
        <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">

            <div style="position:relative; flex:1; max-width:320px;">
                <i class="bi bi-search"
                   style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                          color:var(--text-muted); font-size:13px; pointer-events:none;"></i>
                <input type="text"
                       id="buscadorCategorias"
                       class="form-control"
                       placeholder="Buscar por nombre, código o descripción..."
                       style="padding-left:36px;"
                       value="{{ request('buscar') }}"
                       autocomplete="off">
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-sm em-filtro {{ !request('estado') ? 'active' : '' }}"
                        data-filtro="todos"
                        data-estado="">
                    Todas
                </button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'activas' ? 'active' : '' }}"
                        data-filtro="activas"
                        data-estado="activas">
                    Activas
                </button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'inactivas' ? 'active' : '' }}"
                        data-filtro="inactivas"
                        data-estado="inactivas">
                    Inactivas
                </button>
            </div>

        </div>

        {{-- Contenedor de tabla (se reemplaza con AJAX) --}}
        <div id="tablaContainer" data-url="{{ route('categorias.index') }}">
         @include('categorias._tabla')
        </div>

    </div>
</div>
@endsection


@push('styles')
    @vite(['resources/css/categorias.css'])
@endpush

// Scripts específicos para esta página
@push('scripts')
    @vite(['resources/js/categorias.js'])
@endpush