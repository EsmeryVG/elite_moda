@extends('layouts.app')

@section('content')
<div class="card page-card w-100">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-1 fw-semibold">Sucursales</h5>
                <p class="text-muted mb-0" style="font-size:13px;">
                    {{ $sucursales->total() }} sucursales registradas
                </p>
            </div>
            <a href="{{ route('sucursales.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Nueva sucursal
            </a>
        </div>

        <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">
            <div style="position:relative; flex:1; max-width:280px;">
                <i class="bi bi-search"
                   style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                          color:var(--text-muted); font-size:13px; pointer-events:none;"></i>
                <input type="text" id="buscadorSucursales" class="form-control"
                       placeholder="Buscar sucursal..."
                       style="padding-left:36px;"
                       value="{{ request('buscar') }}" autocomplete="off">
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-sm em-filtro {{ !request('estado') ? 'active' : '' }}"
                        data-estado="">Todas</button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'activas' ? 'active' : '' }}"
                        data-estado="activas">Activas</button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'inactivas' ? 'active' : '' }}"
                        data-estado="inactivas">Inactivas</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width:110px;">Código</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th style="width:130px;">Teléfono</th>
                        <th style="width:100px;">Estado</th>
                        <th style="width:100px;" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaContainer" data-url="{{ route('sucursales.index') }}">
                    @include('sucursales._tabla')
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/sucursales.css'])
@endpush

@push('scripts')
    @vite(['resources/js/sucursales.js'])
@endpush