@extends('layouts.app')

@section('content')
<div id="tpvRoot" class="tpv-fullscreen" data-itbis="{{ \App\Models\Configuracion::get('itbis_porcentaje', 18) }}">

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

    {{-- Columna izquierda: buscador + carrito --}}
    <div class="tpv-col-izquierda">
        <div class="card page-card tpv-buscador-card">
            <div class="card-body p-3">
                <div class="tpv-buscador-wrapper">
                    <i class="bi bi-upc-scan tpv-buscador-icon"></i>
                    <select id="buscadorProducto" placeholder="Escanea un código de barras o busca por nombre...">
                    </select>
                </div>
            </div>
        </div>

        <div class="card page-card tpv-carrito-card">
            <div class="card-body p-3">
                <div id="carritoLista" class="carrito-lista"></div>
                <div id="lineasOcultas"></div>
            </div>
        </div>
    </div>

    {{-- Columna derecha: cliente, vendedor, totales, pago --}}
    <div class="tpv-panel">

        <div class="card page-card">
            <div class="card-body p-3">
                <label class="form-label" style="font-size:11.5px;">Cliente</label>
                <select id="selectCliente" name="cliente_id"
                        data-default-id="{{ $clienteDefault?->id }}"
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

        <div class="card page-card">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label mb-0" style="font-size:11.5px;">Métodos de pago</label>
                    <button type="button" id="btnAgregarPago" class="btn btn-outline-primary btn-sm py-0 px-2">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>

                <div id="pagosContainer" class="pagos-lista"></div>

                <div id="pagoRestante" class="pago-restante incompleto">
                    Faltan RD$ 0.00
                </div>

                <template id="tiposPagoTemplate">
                    @foreach($tiposPago as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                    @endforeach
                </template>
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

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-1"></i> Confirmar venta
        </button>

    </div>

</div>
</form>

</div>
@endsection

@push('styles')
    @vite(['resources/css/ventas.css'])
@endpush

@push('scripts')
    @vite(['resources/js/ventas.js'])
@endpush