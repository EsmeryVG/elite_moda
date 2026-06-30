@extends('layouts.app')

@section('content')
<div class="card page-card w-100">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-1 fw-semibold">Movimientos de inventario</h5>
                <p class="text-muted mb-0" style="font-size:13px;">
                    {{ $movimientos->total() }} movimientos registrados
                </p>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">

            <div style="position:relative; flex:1; max-width:280px;">
                <i class="bi bi-search"
                   style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                          color:var(--text-muted); font-size:13px; pointer-events:none;"></i>
                <input type="text" id="buscadorMovimientos" class="form-control"
                       placeholder="Buscar por producto o código..."
                       style="padding-left:36px;"
                       value="{{ request('buscar') }}" autocomplete="off">
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select id="filtroAlmacen" class="form-select form-select-sm"
                        style="width:auto; font-size:13px;">
                    <option value="">Todos los almacenes</option>
                    @foreach($almacenes as $almacen)
                        <option value="{{ $almacen->id }}"
                                {{ request('almacen') == $almacen->id ? 'selected' : '' }}>
                            {{ $almacen->nombre }}
                        </option>
                    @endforeach
                </select>

                <button class="btn btn-sm em-filtro {{ !request('tipo') ? 'active' : '' }}"
                        data-tipo="">Todos</button>
                <button class="btn btn-sm em-filtro {{ request('tipo') === 'entrada_compra' ? 'active' : '' }}"
                        data-tipo="entrada_compra">Compras</button>
                <button class="btn btn-sm em-filtro {{ request('tipo') === 'salida_venta' ? 'active' : '' }}"
                        data-tipo="salida_venta">Ventas</button>
                <button class="btn btn-sm em-filtro {{ request('tipo') === 'ajuste_positivo' ? 'active' : '' }}"
                        data-tipo="ajuste_positivo">Ajuste (+)</button>
                <button class="btn btn-sm em-filtro {{ request('tipo') === 'ajuste_negativo' ? 'active' : '' }}"
                        data-tipo="ajuste_negativo">Ajuste (-)</button>
            </div>

        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width:130px;">Fecha</th>
                        <th>Producto</th>
                        <th style="width:130px;">Almacén</th>
                        <th style="width:170px;">Tipo</th>
                        <th style="width:80px;" class="text-center">Cantidad</th>
                        <th style="width:160px;">Referencia</th>
                        <th style="width:120px;">Usuario</th>
                    </tr>
                </thead>
                <tbody id="tablaContainer" data-url="{{ route('movimientos.index') }}">
                    @include('movimientos._tabla')
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
    @vite(['resources/js/movimientos.js'])
@endpush