@extends('layouts.app')

@section('page_title', 'Nueva Devolución')
@section('page_subtitle', 'Venta ' . $venta->codigo)

@section('content')
    <form action="{{ route('devoluciones.store') }}" method="POST" id="formDevolucion">
        @csrf
        <input type="hidden" name="venta_id" value="{{ $venta->id }}">

        <div class="row g-4">

            <div class="col-lg-4">
                <div class="card page-card mb-4">
                    <div class="card-body p-4">
                        <p class="prod-section-title">Información de la venta</p>

                        <div class="mb-3">
                            <span class="field-label">Cliente</span>
                            <div class="field-readonly">
                                {{ $venta->cliente?->nombre }} {{ $venta->cliente?->apellido }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <span class="field-label">Fecha de venta</span>
                            <div class="field-readonly">
                                {{ $venta->fecha->format('d/m/Y') }}
                                <span style="color:var(--text-muted); font-size:12px;">
                                    ({{ $diasTranscurridos }} días)
                                </span>
                            </div>
                        </div>

                        <div class="mb-3" id="wrapperEmpleado">
                            <label class="form-label">
                                Empleado que atiende
                                @unless ($esAdmin)
                                    <span style="color:var(--accent);">*</span>
                                @endunless
                            </label>
                            <select id="selectEmpleado" name="empleado_id" placeholder="Buscar empleado..."
                                {{ $esAdmin ? '' : 'required' }} data-es-admin="{{ $esAdmin ? '1' : '0' }}"></select>
                            @if ($esAdmin)
                                <small class="text-muted">Opcional — si no seleccionas empleado, quedará registrado a tu
                                    nombre como administrador.</small>
                            @endif
                            @error('empleado_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($fueraDeTiempo)
                            <div class="alerta-autorizacion mb-3" id="alertaAutorizacion">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Esta venta supera los {{ $diasLimite }} días permitidos.
                                Se requiere <strong>autorización de un administrador</strong>
                                y la nota de crédito <strong>no incluirá el ITBIS</strong>.
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Contraseña de administrador <span style="color:var(--accent);">*</span>
                                </label>
                                <input type="password" name="admin_password"
                                    class="form-control @error('admin_password') is-invalid @enderror">
                                @error('admin_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                    </div>
                </div>

                <div class="card page-card">
                    <div class="card-body p-4">
                        <div class="totales-box mb-3">
                            <div class="totales-row">
                                <span>Total estimado de la NC</span>
                                <span id="resumenTotalDevolucion">RD$ 0.00</span>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i> Registrar devolución
                            </button>
                            <a href="{{ route('ventas.show', $venta) }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card page-card">
                    <div class="card-body p-4">
                        <p class="prod-section-title mb-4">Productos disponibles para devolver</p>

                        @error('lineas')
                            <div class="alert alert-danger rounded-3 mb-3" style="font-size:13px;">
                                {{ $message }}
                            </div>
                        @enderror

                        @forelse($lineas as $idx => $linea)
                            <div class="linea-devolucion-card" data-precio="{{ $linea['precio_unitario'] }}">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input linea-devolucion-check"
                                        id="check-{{ $idx }}" name="lineas[{{ $idx }}][incluir]"
                                        value="1">
                                    <label class="form-check-label" for="check-{{ $idx }}"
                                        style="font-size:13px; font-weight:500;">
                                        {{ $linea['variante']->producto?->nombre }}
                                    </label>
                                    <div class="linea-devolucion-disponible">
                                        Vendido: {{ $linea['cantidad_original'] }}
                                        @if ($linea['cantidad_devuelta'] > 0)
                                            · Ya devuelto: {{ $linea['cantidad_devuelta'] }}
                                        @endif
                                        · Disponible: {{ $linea['cantidad_disponible'] }}
                                        · RD$ {{ number_format($linea['precio_unitario'], 2) }} c/u
                                    </div>
                                </div>

                                <input type="hidden" name="lineas[{{ $idx }}][detalle_venta_id]"
                                    value="{{ $linea['detalle_venta_id'] }}">

                                <div class="linea-devolucion-body">
                                    <div>
                                        <label
                                            style="font-size:11px; font-weight:600; text-transform:uppercase;
                                              letter-spacing:0.06em; color:var(--text-muted); margin-bottom:6px;
                                              display:block;">
                                            Motivo
                                        </label>
                                        <select name="lineas[{{ $idx }}][motivo]"
                                            class="form-select motivo-devolucion" disabled>
                                            <option value="talla_incorrecta">Talla incorrecta</option>
                                            <option value="no_satisfaccion">No satisfacción del cliente</option>
                                            <option value="defecto_fabrica">Defecto de fábrica</option>
                                            <option value="producto_danado">Producto dañado</option>
                                            <option value="error_facturacion">Error de facturación</option>
                                            <option value="otro">Otro</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            style="font-size:11px; font-weight:600; text-transform:uppercase;
                                              letter-spacing:0.06em; color:var(--text-muted); margin-bottom:6px;
                                              display:block;">
                                            Cantidad
                                        </label>
                                        <input type="number" name="lineas[{{ $idx }}][cantidad]"
                                            class="form-control cantidad-devolver" min="1"
                                            max="{{ $linea['cantidad_disponible'] }}" value="1" disabled>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4" style="font-size:13px;">
                                No hay productos disponibles para devolver en esta venta.
                            </p>
                        @endforelse

                    </div>
                </div>
            </div>

        </div>
    </form>
@endsection

@push('styles')
    @vite(['resources/css/devoluciones.css'])
@endpush

@push('scripts')
    @vite(['resources/js/devoluciones.js'])
@endpush
