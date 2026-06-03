@extends('layouts.app')

@section('page_title', 'Nueva Orden de Compra')
@section('page_subtitle', 'Registra una nueva orden de compra')

@section('content')
<form action="{{ route('ordenes_compra.store') }}" method="POST" id="formOrden">
@csrf

<div class="row g-4">

    {{-- Columna izquierda --}}
    <div class="col-lg-4">

        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <p class="prod-section-title">Información general</p>

                <div class="mb-3">
                    <label class="form-label">
                        Proveedor <span style="color:var(--accent);">*</span>
                    </label>
                    <select name="proveedor_id"
                            class="form-select @error('proveedor_id') is-invalid @enderror">
                        <option value="">Selecciona un proveedor</option>
                        @foreach($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}"
                                    {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                {{ $proveedor->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('proveedor_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Almacén destino <span style="color:var(--accent);">*</span>
                    </label>
                    <select name="almacen_id"
                            class="form-select @error('almacen_id') is-invalid @enderror">
                        <option value="">Selecciona un almacén</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}"
                                    {{ old('almacen_id') == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                                @if($almacen->sucursal)
                                    — {{ $almacen->sucursal->nombre }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('almacen_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Fecha <span style="color:var(--accent);">*</span>
                    </label>
                    <input type="date" name="fecha"
                           class="form-control @error('fecha') is-invalid @enderror"
                           value="{{ old('fecha', now()->format('Y-m-d')) }}">
                    @error('fecha')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha esperada de entrega</label>
                    <input type="date" name="fecha_esperada"
                           class="form-control"
                           value="{{ old('fecha_esperada') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="3"
                              placeholder="Notas u observaciones opcionales">{{ old('observaciones') }}</textarea>
                </div>

            </div>
        </div>

        {{-- Totales --}}
        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <p class="prod-section-title">Resumen</p>
                <div class="totales-box">
                    <div class="totales-row">
                        <span>Subtotal</span>
                        <span id="resumenSubtotal">RD$ 0.00</span>
                    </div>
                    <div class="totales-row">
                        <span>ITBIS (18%)</span>
                        <span id="resumenImpuesto">RD$ 0.00</span>
                    </div>
                    <div class="totales-row">
                        <span>Total</span>
                        <span id="resumenTotal">RD$ 0.00</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="card page-card">
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Crear orden
                    </button>
                    <a href="{{ route('ordenes_compra.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- Columna derecha: líneas --}}
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <p class="prod-section-title mb-0">Líneas de la orden</p>
                    <button type="button" id="btnAgregarLinea" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Agregar línea
                    </button>
                </div>

                @error('lineas')
                    <div class="alert alert-danger rounded-3 mb-3" style="font-size:13px;">
                        {{ $message }}
                    </div>
                @enderror

                <div id="contenedorLineas" class="lineas-container"></div>

                <p class="text-muted mt-3" style="font-size:12px;">
                    <i class="bi bi-info-circle me-1"></i>
                    Para líneas por <strong>variante</strong> busca el producto ya registrado.
                    Para líneas por <strong>características</strong> describe libremente lo que vas a comprar.
                </p>

            </div>
        </div>
    </div>

</div>
</form>
@endsection

@push('styles')
    @vite(['resources/css/ordenes_compra.css'])
@endpush

@push('scripts')
    @vite(['resources/js/ordenes_compra.js'])
@endpush