@extends('layouts.app')

@section('content')
    <div class="card page-card w-100">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1 fw-semibold">Stock e Inventario</h5>
                    <p class="text-muted mb-0" style="font-size:13px;">
                        {{ $stocks->total() }} registros de stock
                    </p>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">

                <div style="position:relative; flex:1; max-width:300px;">
                    <i class="bi bi-search"
                        style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                          color:var(--text-muted); font-size:13px; pointer-events:none;"></i>
                    <input type="text" id="buscadorStock" class="form-control"
                        placeholder="Buscar por producto o código..." style="padding-left:36px;"
                        value="{{ request('buscar') }}" autocomplete="off">
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <select id="filtroAlmacen" class="form-select form-select-sm" style="width:auto; font-size:13px;">
                        <option value="">Todos los almacenes</option>
                        @foreach ($almacenes as $almacen)
                            <option value="{{ $almacen->id }}" {{ request('almacen') == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>

                    <button class="btn btn-sm em-filtro {{ !request('nivel') ? 'active' : '' }}"
                        data-nivel="">Todos</button>
                    <button class="btn btn-sm em-filtro {{ request('nivel') === 'critico' ? 'active' : '' }}"
                        data-nivel="critico">
                        <i class="bi bi-exclamation-triangle me-1"></i> Crítico
                    </button>
                    <button class="btn btn-sm em-filtro {{ request('nivel') === 'agotado' ? 'active' : '' }}"
                        data-nivel="agotado">
                        <i class="bi bi-x-circle me-1"></i> Agotado
                    </button>
                </div>

            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Variante</th>
                            <th>Almacén</th>
                            <th style="width:100px; text-align:center;">Disponible</th>
                            <th style="width:100px; text-align:center;">Mínimo</th>
                            <th style="width:120px;">Estado</th>
                        </tr>
                    </thead>
                    <tbody id="tablaContainer" data-url="{{ route('stock.index') }}">
                        @include('stock._tabla')
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/inventario.css'])
@endpush

@push('scripts')
    @vite(['resources/js/inventario.js'])
@endpush
