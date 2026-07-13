@extends('layouts.app')

@section('content')
    <div id="tpvRoot" class="tpv-fullscreen" data-itbis="{{ \App\Models\Configuracion::get('itbis_porcentaje', 18) }}"
        data-horario-cierre="{{ \App\Models\Configuracion::get('horario_cierre', '19:00') }}">

        <div class="tpv-fullscreen-header">
            <div>
                <h5 class="mb-0 fw-semibold">Punto de venta</h5>
                <p class="text-muted mb-0" style="font-size:12px;">Elite Moda</p>
            </div>
            <a href="{{ route('ventas.index') }}" class="tpv-salir-btn">
                <i class="bi bi-x-lg"></i> Salir
            </a>
        </div>

        <form action="{{ route('ventas.store') }}" method="POST" id="formVenta" class="tpv-form-wrapper">
            @csrf

            <div class="tpv-layout">

                {{-- Columna izquierda: buscador + categorías + grid --}}
                <div class="tpv-col-izquierda">
                    <div class="card page-card tpv-buscador-card">
                        <div class="card-body p-3">
                            <div class="tpv-buscador-wrapper">
                                <i class="bi bi-upc-scan tpv-buscador-icon"></i>
                                <select id="buscadorProducto"
                                    placeholder="Escanea un código de barras o busca por nombre...">
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card page-card tpv-carrito-card">
                        <div class="card-body p-3" style="display:flex; flex-direction:column; height:100%; gap:10px;">

                            {{-- Tabs: Carrito / Categorías --}}
                            <div style="display:flex; gap:8px; flex-shrink:0;">
                                <button type="button" id="tabCarrito" onclick="VentasModule.mostrarTab('carrito')"
                                    style="flex:1; padding:7px; border-radius:var(--radius-sm);
                                border:1px solid #1f2125; background:#1f2125; color:#fff;
                                font-size:12.5px; font-weight:600; cursor:pointer;">
                                    <i class="bi bi-cart3 me-1"></i> Carrito
                                    <span id="carritoCount"
                                        style="background:var(--accent); color:#fff;
                            border-radius:10px; padding:1px 7px; font-size:11px; margin-left:4px;
                            display:none;">0</span>
                                </button>
                                <button type="button" id="tabCatalogo" onclick="VentasModule.mostrarTab('catalogo')"
                                    style="flex:1; padding:7px; border-radius:var(--radius-sm);
                                border:1px solid var(--border); background:var(--bg-elevated); color:var(--text-secondary);
                                font-size:12.5px; font-weight:600; cursor:pointer;">
                                    <i class="bi bi-grid me-1"></i> Catálogo
                                </button>
                            </div>

                            {{-- Panel: Carrito --}}
                            <div id="panelCarrito" style="flex:1; min-height:0; display:flex; flex-direction:column;">
                                <div id="carritoLista" class="carrito-lista"></div>
                            </div>

                            {{-- Panel: Catálogo --}}
                            <div id="panelCatalogo"
                                style="flex:1; min-height:0; display:none; flex-direction:column; gap:8px;">
                                <div id="tpvCategoriasList" class="tpv-categorias-lista"></div>
                                <div id="tpvProductosGrid" class="tpv-productos-grid">
                                    <div class="tpv-productos-vacio">
                                        <i class="bi bi-hand-index"
                                            style="font-size:28px; display:block; margin-bottom:8px;"></i>
                                        Selecciona una categoría para ver los productos
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Columna derecha: cliente, vendedor, totales, cobrar --}}
                <div class="tpv-panel">

                    <div class="card page-card">
                        <div class="card-body p-3">
                            <label class="form-label" style="font-size:11.5px;">Cliente</label>
                            <select id="selectCliente" name="cliente_id" data-default-id="{{ $clienteDefault?->id }}"
                                data-default-texto="{{ $clienteDefault?->nombre }} {{ $clienteDefault?->apellido }}">
                            </select>

                            <label class="form-label mt-2" style="font-size:11.5px;">Vendedor (opcional)</label>
                            <select id="selectEmpleado" name="empleado_id"></select>
                        </div>
                    </div>

                    <div class="card page-card">
                        <div class="card-body p-3">
                            <div class="itbis-toggle-global">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" id="itbisGlobalToggle"
                                        name="itbis_global" value="1" checked>
                                </div>
                                <span>Aplicar ITBIS ({{ \App\Models\Configuracion::get('itbis_porcentaje', 18) }}%)</span>
                            </div>
                        </div>
                    </div>

                    <div class="card page-card">
                        <div class="card-body p-3">
                            <div class="tpv-totales-box">
                                <div class="tpv-total-row">
                                    <span>Subtotal</span>
                                    <span id="resumenSubtotal">RD$ 0.00</span>
                                </div>
                                <div class="tpv-total-row descuento">
                                    <span>Descuento</span>
                                    <span id="resumenDescuento">-RD$ 0.00</span>
                                </div>
                                <div class="tpv-total-row">
                                    <span>ITBIS</span>
                                    <span id="resumenImpuesto">RD$ 0.00</span>
                                </div>
                                <div class="tpv-total-row grand-total">
                                    <span>Total</span>
                                    <span id="resumenTotal">RD$ 0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @error('lineas')
                        <div class="alert alert-danger rounded-3 mb-0 py-2" style="font-size:12.5px;">
                            {{ $message }}
                        </div>
                    @enderror
                    @error('ncf')
                        <div class="alert alert-danger rounded-3 mb-0 py-2" style="font-size:12.5px;">
                            {{ $message }}
                        </div>
                    @enderror

                    <button type="button" id="btnCobrar" class="btn-cobrar" disabled>
                        <i class="bi bi-cash-coin"></i> Cobrar
                    </button>

                </div>

            </div>

            {{-- Inputs ocultos para pagos (se llenan desde el modal) --}}
            <div id="pagosHiddenContainer"></div>
            <div id="lineasOcultas"></div>

            {{-- Template de tipos de pago para el modal --}}
            <template id="tiposPagoTemplate">
                @foreach ($tiposPago as $tipo)
                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                @endforeach
            </template>
        </form>

    </div>
@endsection

@push('styles')
    @vite(['resources/css/ventas.css'])
@endpush

@push('scripts')
    @vite(['resources/js/ventas.js'])
@endpush
