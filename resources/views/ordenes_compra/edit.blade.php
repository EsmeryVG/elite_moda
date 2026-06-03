@extends('layouts.app')

@section('page_title', 'Editar — ' . $orden->codigo)
@section('page_subtitle', 'Modifica la orden de compra en borrador')

@section('content')
<form action="{{ route('ordenes_compra.update', $orden) }}" method="POST" id="formOrden">
@csrf
@method('PUT')

<div class="row g-4">

    <div class="col-lg-4">

        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <p class="prod-section-title">Información general</p>

                <div class="mb-3">
                    <label class="form-label">Código</label>
                    <input type="text" class="form-control"
                           value="{{ $orden->codigo }}" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Proveedor <span style="color:var(--accent);">*</span>
                    </label>
                    <select name="proveedor_id" class="form-select" required>
                        @foreach($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}"
                                    {{ old('proveedor_id', $orden->proveedor_id) == $proveedor->id ? 'selected' : '' }}>
                                {{ $proveedor->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Almacén destino <span style="color:var(--accent);">*</span>
                    </label>
                    <select name="almacen_id" class="form-select" required>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}"
                                    {{ old('almacen_id', $orden->almacen_id) == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                                @if($almacen->sucursal)
                                    — {{ $almacen->sucursal->nombre }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Fecha <span style="color:var(--accent);">*</span>
                    </label>
                    <input type="date" name="fecha" class="form-control"
                           value="{{ old('fecha', $orden->fecha->format('Y-m-d')) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha esperada</label>
                    <input type="date" name="fecha_esperada" class="form-control"
                           value="{{ old('fecha_esperada', $orden->fecha_esperada?->format('Y-m-d')) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control"
                              rows="3">{{ old('observaciones', $orden->observaciones) }}</textarea>
                </div>

            </div>
        </div>

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

        <div class="card page-card">
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Guardar cambios
                    </button>
                    <a href="{{ route('ordenes_compra.show', $orden) }}"
                       class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>

    </div>

    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <p class="prod-section-title mb-0">Líneas de la orden</p>
                    <button type="button" id="btnAgregarLinea"
                            class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Agregar línea
                    </button>
                </div>

                <div class="tabla-lineas">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:150px;">Tipo</th>
                                <th>Producto / Descripción</th>
                                <th style="width:90px;">Cantidad</th>
                                <th style="width:140px;">Precio unit.</th>
                                <th style="width:130px;" class="text-end">Subtotal</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="tbodyLineas"></tbody>
                    </table>
                </div>

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
    <script>
        // Cargar líneas existentes al editar
        document.addEventListener('DOMContentLoaded', () => {
            const lineas = @json($orden->detalles->map(fn($d) => [
                'tipo'        => $d->esPorCaracteristicas() ? 'caracteristicas' : 'variante',
                'varianteId'  => $d->variante_producto_id,
                'varianteTexto' => $d->descripcion,
                'descripcion' => $d->caracteristicas_solicitadas['descripcion'] ?? null,
                'notas'       => $d->caracteristicas_solicitadas['notas'] ?? null,
                'cantidad'    => $d->cantidad_solicitada,
                'precio'      => $d->precio_unitario,
            ]));

            lineas.forEach(linea => OrdenesCompraModule.agregarLinea(linea));
        });
    </script>
@endpush