@extends('layouts.app')

@section('page_title', $venta->codigo)
@section('page_subtitle', 'Detalle de la venta')

@section('content')
    <div class="row g-4">

        <div class="col-lg-4">
            <div class="card page-card mb-4">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <h6 class="fw-semibold mb-0">Información general</h6>
                        @php
                            $badge = match ($venta->estado) {
                                'pendiente' => 'badge-venta-pendiente',
                                'completada' => 'badge-venta-completada',
                                'anulada' => 'badge-venta-anulada',
                                default => 'badge-venta-pendiente',
                            };
                        @endphp
                        <span class="{{ $badge }}">
                            {{ ucfirst($venta->estado) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <span class="field-label">Código</span>
                        <div class="field-readonly" style="font-family:monospace;">
                            {{ $venta->codigo }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">NCF</span>
                        <div class="field-readonly" style="font-family:monospace;">
                            {{ $venta->ncf }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Cliente</span>
                        <div class="field-readonly">
                            {{ $venta->cliente?->nombre }} {{ $venta->cliente?->apellido }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Vendedor</span>
                        <div class="field-readonly">
                            {{ $venta->empleado?->nombre_completo ?? '—' }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Cajero</span>
                        <div class="field-readonly">
                            {{ $venta->usuario?->name ?? '—' }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Fecha</span>
                        <div class="field-readonly">
                            {{ $venta->fecha->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    @if ($venta->observaciones)
                        <div class="mb-3">
                            <span class="field-label">Observaciones</span>
                            <div class="field-readonly">{{ $venta->observaciones }}</div>
                        </div>
                    @endif

                </div>
            </div>

            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <p class="prod-section-title">Resumen</p>
                    <div class="totales-box">
                        <div class="totales-row">
                            <span>Subtotal</span>
                            <span>RD$ {{ number_format($venta->subtotal, 2) }}</span>
                        </div>
                        <div class="totales-row">
                            <span>Descuento</span>
                            <span style="color:#2e7d32;">-RD$ {{ number_format($venta->descuento_total, 2) }}</span>
                        </div>
                        <div class="totales-row">
                            <span>ITBIS</span>
                            <span>RD$ {{ number_format($venta->impuesto, 2) }}</span>
                        </div>
                        <div class="totales-row">
                            <span>Total</span>
                            <span>RD$ {{ number_format($venta->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card page-card">
                <div class="card-body p-4">
                    <p class="prod-section-title mb-3">Acciones</p>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('ventas.factura', $venta) }}" target="_blank"
                            class="btn-accion btn-accion-neutro justify-content-center">
                            <i class="bi bi-printer"></i> Reimprimir factura
                        </a>

                        @if ($venta->estado === 'completada')
                            <a href="{{ route('devoluciones.create', $venta) }}"
                                class="btn-accion btn-accion-warning justify-content-center">
                                <i class="bi bi-arrow-return-left"></i> Devolver productos
                            </a>
                        @endif

                        @if ($venta->esAnulable())
                            <form action="{{ route('ventas.anular', $venta) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-accion btn-accion-danger justify-content-center w-100"
                                    onclick="return confirm('¿Anular esta venta? El stock será restaurado.')">
                                    <i class="bi bi-x-circle"></i> Anular venta
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('ventas.index') }}" class="btn-accion btn-accion-neutro justify-content-center">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">

            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-4">Productos</h6>

                    <div class="tabla-lineas">
                        <table>
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th style="text-align:center; width:70px;">Cant.</th>
                                    <th style="text-align:right; width:100px;">Precio</th>
                                    <th style="text-align:right; width:100px;">Descuento</th>
                                    <th style="width:60px;">ITBIS</th>
                                    <th style="text-align:right; width:110px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($venta->detalles as $detalle)
                                    <tr>
                                        <td>
                                            <div style="font-size:13px; font-weight:500;">
                                                {{ $detalle->variante?->producto?->nombre }}
                                            </div>
                                            <div style="font-size:11.5px; color:var(--text-muted);">
                                                @foreach ($detalle->variante?->valores ?? [] as $valor)
                                                    {{ $valor->atributo?->nombre }}: {{ $valor->valor }}
                                                    @if (!$loop->last)
                                                        ·
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="text-center" style="font-size:13px;">
                                            {{ $detalle->cantidad }}
                                        </td>
                                        <td class="text-end" style="font-size:13px;">
                                            RD$ {{ number_format($detalle->precio_unitario, 2) }}
                                        </td>
                                        <td class="text-end" style="font-size:13px; color:#2e7d32;">
                                            @if ($detalle->descuento_aplicado > 0)
                                                -RD$ {{ number_format($detalle->descuento_aplicado, 2) }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            @if ($detalle->itbis_aplicado)
                                                <span style="color:#2e7d32; font-size:12px;">● Sí</span>
                                            @else
                                                <span style="color:var(--text-muted); font-size:12px;">● No</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-semibold" style="font-size:13px;">
                                            RD$ {{ number_format($detalle->subtotal, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card page-card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-4">Pagos</h6>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Método</th>
                                    <th>Referencia</th>
                                    <th class="text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($venta->pagos as $pago)
                                    <tr>
                                        <td style="font-size:13px;">{{ $pago->tipoPago?->nombre }}</td>
                                        <td style="font-size:12px; color:var(--text-muted);">
                                            {{ $pago->referencia ?? '—' }}
                                            @if ($pago->banco)
                                                — {{ $pago->banco }}
                                            @endif
                                        </td>
                                        <td class="text-end fw-semibold" style="font-size:13px;">
                                            RD$ {{ number_format($pago->monto, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

@push('styles')
    @vite(['resources/css/ventas.css'])
@endpush

@push('scripts')
    <script>
        @if (session('abrir_factura'))
            window.open('{{ session('abrir_factura') }}', '_blank');
        @endif
    </script>
@endpush
