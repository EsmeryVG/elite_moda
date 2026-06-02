@extends('layouts.app')

@section('content')
<div class="card page-card w-100">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-1 fw-semibold">Almacenes</h5>
                <p class="text-muted mb-0" style="font-size:13px;">
                    {{ $almacenes->total() }} almacenes registrados
                </p>
            </div>
            <a href="{{ route('almacenes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Nuevo almacén
            </a>
        </div>

        <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">

            <div style="position:relative; flex:1; max-width:280px;">
                <i class="bi bi-search"
                   style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                          color:var(--text-muted); font-size:13px; pointer-events:none;"></i>
                <input type="text" id="buscadorAlmacenes" class="form-control"
                       placeholder="Buscar almacén..."
                       style="padding-left:36px;"
                       value="{{ request('buscar') }}" autocomplete="off">
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select id="filtroSucursal" class="form-select form-select-sm"
                        style="width:auto; font-size:13px;">
                    <option value="">Todas las sucursales</option>
                    @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}"
                                {{ request('sucursal') == $sucursal->id ? 'selected' : '' }}>
                            {{ $sucursal->nombre }}
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

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th style="width:110px;">Tipo</th>
                        <th>Sucursal</th>
                        <th>Dirección</th>
                        <th style="width:100px;">Estado</th>
                        <th style="width:100px;" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaContainer" data-url="{{ route('almacenes.index') }}">
                    @include('almacenes._tabla')
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/almacenes.css'])
@endpush

@push('scripts')
    @vite(['resources/js/almacenes.js'])
@endpush