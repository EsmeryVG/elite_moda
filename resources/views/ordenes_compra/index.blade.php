@extends('layouts.app')

@section('content')
<div class="card page-card w-100">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-1 fw-semibold">Órdenes de compra</h5>
                <p class="text-muted mb-0" style="font-size:13px;">
                    {{ $ordenes->total() }} órdenes registradas
                </p>
            </div>
            <a href="{{ route('ordenes_compra.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Nueva orden
            </a>
        </div>

        <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">

            <div style="position:relative; flex:1; max-width:280px;">
                <i class="bi bi-search"
                   style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                          color:var(--text-muted); font-size:13px; pointer-events:none;"></i>
                <input type="text" id="buscadorOrdenes" class="form-control"
                       placeholder="Buscar por código..."
                       style="padding-left:36px;"
                       value="{{ request('buscar') }}" autocomplete="off">
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select id="filtroProveedor" class="form-select form-select-sm"
                        style="width:auto; font-size:13px;">
                    <option value="">Todos los proveedores</option>
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}"
                                {{ request('proveedor') == $proveedor->id ? 'selected' : '' }}>
                            {{ $proveedor->nombre }}
                        </option>
                    @endforeach
                </select>

                <button class="btn btn-sm em-filtro {{ !request('estado') ? 'active' : '' }}"
                        data-estado="">Todas</button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'borrador' ? 'active' : '' }}"
                        data-estado="borrador">Borrador</button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'confirmada' ? 'active' : '' }}"
                        data-estado="confirmada">Confirmada</button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'parcial' ? 'active' : '' }}"
                        data-estado="parcial">Parcial</button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'completada' ? 'active' : '' }}"
                        data-estado="completada">Completada</button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'cancelada' ? 'active' : '' }}"
                        data-estado="cancelada">Cancelada</button>
            </div>

        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width:110px;">Código</th>
                        <th>Proveedor</th>
                        <th>Almacén</th>
                        <th style="width:110px;">Fecha</th>
                        <th style="width:110px;">Estado</th>
                        <th style="width:100px;" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaContainer" data-url="{{ route('ordenes_compra.index') }}">
                    @include('ordenes_compra._tabla')
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/ordenes_compra.css'])
@endpush

@push('scripts')
    @vite(['resources/js/ordenes_compra.js'])
@endpush