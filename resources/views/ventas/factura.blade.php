<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Factura {{ $venta->ncf }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10.5px;
            color: #000;
            background: #fff;
            width: 80mm;
            margin: 0 auto;
            padding: 3mm 2mm;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .small {
            font-size: 9.5px;
        }

        .upper {
            text-transform: uppercase;
        }

        .negocio-nombre {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .negocio-info {
            font-size: 10px;
            text-align: center;
            line-height: 1.4;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 3px 0;
        }

        hr.solid {
            border-top: 1px solid #000;
        }

        /* ── Encabezado de columnas ── */
        .col-header {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 2px 0;
            margin: 3px 0;
        }

        .col-desc {
            flex: 1;
        }

        .col-itbis {
            width: 30px;
            text-align: right;
        }

        .col-valor {
            width: 50px;
            text-align: right;
        }

        /* ── Producto ── */
        .producto-item {
            margin-bottom: 3px;
        }

        .producto-codigo {
            font-size: 9px;
            color: #555;
        }

        .producto-nombre {
            font-size: 10.5px;
            font-weight: bold;
            word-break: break-word;
        }

        .producto-atributos {
            font-size: 9.5px;
            color: #444;
        }

        .producto-row {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }

        .producto-descuento {
            font-size: 9.5px;
            color: #555;
        }

        /* ── Totales ── */
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 10.5px;
            margin-bottom: 1px;
        }

        .total-row.grand {
            font-size: 14px;
            font-weight: bold;
            margin-top: 2px;
            padding-top: 2px;
            border-top: 1px solid #000;
        }

        .total-row.pago {
            font-size: 11px;
        }

        /* ── NCF ── */
        .ncf-label {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            margin: 2px 0;
        }

        .ncf-numero {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 2px;
        }

        /* ── Info cliente al pie ── */
        .cliente-info {
            font-size: 10px;
            margin: 1px 0;
        }

        /* ── Código de barras placeholder ── */
        .barcode-area {
            text-align: center;
            margin: 4px 0;
            font-size: 9px;
            color: #555;
            border: 1px solid #ccc;
            padding: 4px;
        }

        /* ── Mensaje pie ── */
        .mensaje-pie {
            text-align: center;
            font-size: 10.5px;
            margin-top: 4px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .mensaje-sub {
            text-align: center;
            font-size: 9.5px;
            margin-top: 2px;
        }

        /* ── Botón imprimir (solo pantalla) ── */
        .btn-imprimir {
            display: block;
            width: 100%;
            padding: 10px;
            background: #1f2125;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            margin-bottom: 10px;
            font-family: inherit;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                width: 80mm;
                margin: 0;
                padding: 2mm;
            }

            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <div class="no-print" style="margin-bottom:8px;">
        <button class="btn-imprimir" onclick="window.print()">
            🖨️ Imprimir factura
        </button>
    </div>

    {{-- ── Encabezado ── --}}
    <div class="negocio-nombre">
        {{ $config['negocio_nombre']?->valor ?? 'Elite Moda' }}
    </div>
    @if ($venta->almacen?->sucursal?->nombre)
        <div class="negocio-info">{{ $venta->almacen->sucursal->nombre }}</div>
    @endif
    @if ($venta->almacen?->sucursal?->direccion)
        <div class="negocio-info">{{ $venta->almacen->sucursal->direccion }}</div>
    @endif
    @if ($venta->almacen?->sucursal?->telefono)
        <div class="negocio-info">Tel: {{ $venta->almacen->sucursal->telefono }}</div>
    @endif
    @if ($config['negocio_rnc']?->valor)
        <div class="negocio-info bold">RNC: {{ $config['negocio_rnc']->valor }}</div>
    @endif

    <hr class="solid">

    {{-- ── Datos de la transacción ── --}}
    <div style="font-size:10px;">
        {{ $venta->fecha->format('d/m/y') }} {{ $venta->fecha->format('H:i:s') }}
    </div>
    <div style="font-size:10px;">
        NCF: <strong>{{ $venta->ncf }}</strong>
    </div>
    <div class="ncf-label">
        {{ $venta->comprobanteFiscal?->tipo_comprobante ?? 'Factura de Consumo' }}
    </div>

    <hr>

    {{-- ── Columnas de productos ── --}}
    <div class="col-header">
        <span class="col-desc">DESCRIPCION</span>
        <span class="col-itbis">ITBIS</span>
        <span class="col-valor">VALOR</span>
    </div>

    @foreach ($venta->detalles as $detalle)
        @php
            $precioConDescuento = $detalle->subtotal / $detalle->cantidad;
            $itbisLinea = 0;
            if ($detalle->itbis_aplicado) {
                $base = $detalle->subtotal / (1 + ($config['itbis_porcentaje']?->valor ?? 18) / 100);
                $itbisLinea = $detalle->subtotal - $base;
            }
            $atributos = $detalle->variante?->valores
                ->map(fn($v) => $v->atributo?->nombre . ': ' . $v->valor)
                ->join(', ');
        @endphp
        <div class="producto-item">
            <div class="producto-codigo">{{ $detalle->variante?->codigo }}</div>
            <div class="producto-nombre">{{ $detalle->variante?->producto?->nombre }}</div>
            @if ($atributos)
                <div class="producto-atributos">{{ $atributos }}</div>
            @endif
            <div class="producto-row">
                <span>{{ $detalle->cantidad }} x {{ number_format($precioConDescuento, 2) }}</span>
                <span class="col-itbis">
                    @if ($detalle->itbis_aplicado)
                        {{ number_format($itbisLinea, 2) }}
                    @else
                        0.00
                    @endif
                </span>
                <span class="col-valor">{{ number_format($detalle->subtotal, 2) }}</span>
            </div>
            @if ($detalle->descuento_aplicado > 0)
                <div class="producto-descuento">
                    Desc: -{{ number_format($detalle->descuento_aplicado, 2) }}
                </div>
            @endif
        </div>
    @endforeach

    <hr class="solid">

    {{-- ── Totales ── --}}
    <div class="total-row">
        <span>SUBTOTAL</span>
        <span>{{ number_format($venta->subtotal, 2) }}</span>
    </div>
    @if ($venta->descuento_total > 0)
        <div class="total-row">
            <span>DESCUENTO</span>
            <span>-{{ number_format($venta->descuento_total, 2) }}</span>
        </div>
    @endif
    <div class="total-row">
        <span>ITBIS ({{ $config['itbis_porcentaje']?->valor ?? '18' }}%)</span>
        <span>{{ number_format($venta->impuesto, 2) }}</span>
    </div>
    <div class="total-row grand">
        <span>TOTAL A PAGAR</span>
        <span>{{ number_format($venta->total, 2) }}</span>
    </div>

    <hr>

    {{-- ── Pagos ── --}}
    @foreach ($venta->pagos as $pago)
        <div class="total-row pago">
            <span>{{ strtoupper($pago->tipoPago?->nombre ?? '—') }}</span>
            <span>{{ number_format($pago->monto, 2) }}</span>
        </div>
    @endforeach

    @php $cambio = $venta->pagos->sum('monto') - $venta->total; @endphp
    @if ($cambio > 0.01)
        <div class="total-row pago bold">
            <span>CAMBIO</span>
            <span>{{ number_format($cambio, 2) }}</span>
        </div>
    @endif

    <hr class="solid">

    {{-- ── Info del cliente al pie (como La Sirena) ── --}}
    @if (!$venta->cliente?->es_default)
        <div class="cliente-info bold">
            {{ strtoupper($venta->cliente?->nombre . ' ' . $venta->cliente?->apellido) }}
        </div>
        @if ($venta->cliente?->cedula)
            <div class="cliente-info">CÉD: {{ $venta->cliente->cedula }}</div>
        @endif
        @if ($venta->cliente?->rnc)
            <div class="cliente-info">RNC: {{ $venta->cliente->rnc }}</div>
        @endif
    @else
        <div class="cliente-info bold">CONSUMIDOR FINAL</div>
    @endif

    <div class="cliente-info small" style="margin-top:2px;">
        Cajero: {{ $venta->usuario?->name ?? '—' }}
        @if ($venta->empleado)
            | Vendedor: {{ $venta->empleado->nombre_completo }}
        @endif
    </div>

    <hr>

    {{-- ── Código de barras (NCF como texto representativo) ── --}}
    <div class="barcode-area">
        <div style="font-size:8px; letter-spacing:3px;">
            ||| {{ $venta->ncf }} |||
        </div>
        <div style="font-size:9px; margin-top:2px;">{{ $venta->ncf }}</div>
    </div>

    <hr>

    {{-- ── Mensaje pie ── --}}
    <div class="mensaje-pie">
        {{ $config['factura_mensaje_pie']?->valor ?? '¡Gracias por su compra!' }}
    </div>
    @if ($config['negocio_email']?->valor)
        <div class="mensaje-sub">{{ $config['negocio_email']->valor }}</div>
    @endif

    <div class="center small" style="margin-top:6px; color:#666;">
        Sistema Elite Moda
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>

</body>

</html>
