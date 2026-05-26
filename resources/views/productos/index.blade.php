@extends('layouts.app')

@section('page_title', 'Productos')
@section('page_subtitle', 'Administra el catálogo de productos y sus variantes')

@section('content')
<div class="card page-card w-100">
    <div class="card-body p-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-1 fw-semibold">Listado de productos</h5>
                <p class="text-muted mb-0" style="font-size:13px;" id="contadorProductos">
                    {{ $productos->total() }} productos registrados
                </p>
            </div>
            <a href="{{ route('productos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Nuevo producto
            </a>
        </div>

        {{-- Filtros --}}
        <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">

            <div style="position:relative; flex:1; max-width:280px;">
                <i class="bi bi-search"
                   style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                          color:var(--text-muted); font-size:13px; pointer-events:none;"></i>
                <input type="text" id="buscadorProductos" class="form-control"
                       placeholder="Buscar por nombre, código o marca..."
                       style="padding-left:36px;"
                       value="{{ request('buscar') }}" autocomplete="off">
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select id="filtroCategoria" class="form-select form-select-sm"
                        style="width:auto; font-size:13px;">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}"
                                {{ request('categoria') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>

                <button class="btn btn-sm em-filtro {{ !request('estado') ? 'active' : '' }}"
                        data-estado="">Todos</button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'activos' ? 'active' : '' }}"
                        data-estado="activos">Activos</button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'inactivos' ? 'active' : '' }}"
                        data-estado="inactivos">Inactivos</button>
            </div>

        </div>

        {{-- Tabla --}}
        <div class="table-responsive">
            <table class="table align-middle" id="tablaProductos">
                <thead>
                    <tr>
                        <th style="width:32px;"></th>
                        <th style="width:110px;">Código</th>
                        <th>Nombre</th>
                        <th>Marca</th>
                        <th>Categoría</th>
                        <th style="width:90px; text-align:center;">Variantes</th>
                        <th style="width:90px;">Estado</th>
                        <th style="width:140px;" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaContainer" data-url="{{ route('productos.index') }}">
                    @include('productos._tabla')
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/productos.css'])
@endpush

@push('scripts')
    @vite(['resources/js/productos.js'])
@endpush