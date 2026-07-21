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
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background: #fff;
            width: 80mm;
            margin: 0 auto;
            padding: 4mm 3mm;
            line-height: 1.35;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: 700;
        }

        .small {
            font-size: 9.5px;
        }

        .upper {
            text-transform: uppercase;
        }

        .negocio-nombre {
            font-size: 17px;
            font-weight: 800;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .negocio-info {
            font-size: 10px;
            text-align: center;
            color: #444;
            line-height: 1.5;
        }

        .negocio-rnc {
            font-size: 10.5px;
            text-align: center;
            font-weight: 600;
            margin-top: 2px;
        }

        hr {
            border: none;
            border-top: 1px dashed #999;
            margin: 6px 0;
        }

        hr.solid {
            border-top: 1.5px solid #1a1a1a;
        }

        .tipo-factura-banner {
            text-align: center;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            background: #f2f2f2;
            padding: 4px 0;
            margin: 6px 0;
            border-radius: 3px;
        }

        .datos-transaccion {
            font-size: 10px;
            color: #333;
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .ncf-numero {
            font-size: 11.5px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 1px;
            margin-top: 4px;
        }

        .col-header {
            display: flex;
            justify-content: space-between;
            font-size: 9.5px;
            font-weight: 700;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-top: 1px solid #1a1a1a;
            border-bottom: 1px solid #1a1a1a;
            padding: 4px 0;
            margin: 6px 0 4px;
        }

        .col-desc {
            flex: 1;
        }

        .col-itbis {
            width: 44px;
            text-align: right;
            flex-shrink: 0;
        }

        .col-valor {
            width: 60px;
            text-align: right;
            flex-shrink: 0;
        }

        .producto-item {
            margin-bottom: 6px;
        }

        .producto-codigo {
            font-size: 8.5px;
            color: #999;
        }

        .producto-nombre {
            font-size: 11px;
            font-weight: 600;
            word-break: break-word;
        }

        .producto-atributos {
            font-size: 9.5px;
            color: #666;
        }

        .producto-row {
            display: flex;
            align-items: baseline;
            font-size: 10.5px;
            color: #333;
            margin-top: 2px;
        }

        .producto-row>span:first-child {
            flex: 1;
        }

        .producto-descuento {
            font-size: 9.5px;
            color: #c62828;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 10.5px;
            margin-bottom: 3px;
            color: #333;
        }

        .total-row.grand {
            font-size: 15px;
            font-weight: 800;
            color: #1a1a1a;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1.5px solid #1a1a1a;
        }

        .total-row.pago {
            font-size: 11px;
        }

        .cliente-box {
            background: #fafafa;
            border: 1px solid #eee;
            border-radius: 4px;
            padding: 6px 8px;
            margin: 6px 0;
        }

        .cliente-info {
            font-size: 10px;
            margin: 1px 0;
        }

        .barcode-area {
            text-align: center;
            margin: 8px 0;
        }

        .barcode-lines {
            font-family: 'Libre Barcode 39', monospace;
            font-size: 32px;
            letter-spacing: 2px;
            line-height: 1;
        }

        .barcode-numero {
            font-size: 9px;
            letter-spacing: 2px;
            margin-top: 2px;
        }

        .mensaje-pie {
            text-align: center;
            font-size: 11px;
            margin-top: 8px;
            font-weight: 700;
        }

        .mensaje-sub {
            text-align: center;
            font-size: 9.5px;
            color: #666;
            margin-top: 3px;
        }

        .footer-final {
            text-align: center;
            font-size: 8.5px;
            color: #aaa;
            margin-top: 10px;
        }

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
                padding: 3mm;
            }

            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    @php
        $negocioNombre = $config['negocio_nombre'] ?? null;
        $negocioRnc = $config['negocio_rnc'] ?? null;
        $itbisConfig = $config['itbis_porcentaje'] ?? null;
        $mensajePie = $config['factura_mensaje_pie'] ?? null;
        $negocioEmail = $config['negocio_email'] ?? null;
    @endphp
    <div class="no-print" style="margin-bottom:8px;">
        <button class="btn-imprimir" onclick="window.print()">
            🖨️ Imprimir factura
        </button>
    </div>
    {{-- ── Encabezado ── --}}
    <div class="negocio-nombre">
        {{ mb_strtoupper($negocioNombre?->valor ?? 'Elite Moda', 'UTF-8') }}
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
    @if ($negocioRnc?->valor)
        <div class="negocio-rnc">RNC: {{ $negocioRnc->valor }}</div>
    @endif
    <div class="tipo-factura-banner">
        {{ mb_strtoupper($venta->comprobanteFiscal?->tipo_comprobante ?? 'Factura de Consumo', 'UTF-8') }}
    </div>
    {{-- ── Datos de la transacción ── --}}
    <div class="datos-transaccion">
        <span>{{ $venta->fecha->format('d/m/Y') }}</span>
        <span>{{ $venta->fecha->format('H:i:s') }}</span>
    </div>
    <div class="ncf-numero">NCF: {{ $venta->ncf }}</div>
    <hr class="solid">
    {{-- ── Columnas de productos ── --}}
    <div class="col-header">
        <span class="col-desc">Descripción</span>
        <span class="col-itbis">Itbis</span>
        <span class="col-valor">Valor</span>
    </div>
    @foreach ($venta->detalles as $detalle)
        @php
            $precioConDescuento = $detalle->subtotal / $detalle->cantidad;
            $itbisLinea = 0;
            if ($detalle->itbis_aplicado) {
                $base = $detalle->subtotal / (1 + ($itbisConfig?->valor ?? 18) / 100);
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
                    Descuento: -{{ number_format($detalle->descuento_aplicado, 2) }}
                </div>
            @endif
        </div>
    @endforeach
    <hr class="solid">
    {{-- ── Totales ── --}}
    <div class="total-row">
        <span>Subtotal</span>
        <span>{{ number_format($venta->subtotal, 2) }}</span>
    </div>
    @if ($venta->descuento_total > 0)
        <div class="total-row">
            <span>Descuento</span>
            <span>-{{ number_format($venta->descuento_total, 2) }}</span>
        </div>
    @endif
    <div class="total-row">
        <span>ITBIS ({{ $itbisConfig?->valor ?? '18' }}%)</span>
        <span>{{ number_format($venta->impuesto, 2) }}</span>
    </div>
    <div class="total-row grand">
        <span>Total a pagar</span>
        <span>RD$ {{ number_format($venta->total, 2) }}</span>
    </div>
    <hr>
    {{-- ── Pagos ── --}}
    @foreach ($venta->pagos as $pago)
        <div class="total-row pago">
            <span>{{ mb_strtoupper($pago->tipoPago?->nombre ?? '—', 'UTF-8') }}</span>
            <span>{{ number_format($pago->monto, 2) }}</span>
        </div>
    @endforeach
    @php $cambio = $venta->pagos->sum('monto') - $venta->total; @endphp
    @if ($cambio > 0.01)
        <div class="total-row pago bold">
            <span>Cambio</span>
            <span>{{ number_format($cambio, 2) }}</span>
        </div>
    @endif
    {{-- ── Info del cliente ── --}}
    <div class="cliente-box">
        @if (!$venta->cliente?->es_default)
            <div class="cliente-info bold">
                {{ mb_strtoupper(trim($venta->cliente?->nombre . ' ' . $venta->cliente?->apellido), 'UTF-8') }}
            </div>
            @if ($venta->cliente?->cedula)
                <div class="cliente-info">Cédula: {{ $venta->cliente->cedula }}</div>
            @endif
            @if ($venta->cliente?->rnc)
                <div class="cliente-info">RNC: {{ $venta->cliente->rnc }}</div>
            @endif
        @else
            <div class="cliente-info bold">Consumidor Final</div>
        @endif
        <div class="cliente-info small" style="margin-top:3px; color:#777;">
            Cajero: {{ $venta->usuario?->name ?? '—' }}
        </div>
    </div>
    <hr>
    {{-- ── Código representativo del NCF ── --}}
    <div class="barcode-area">
        <div class="barcode-numero">{{ $venta->ncf }}</div>
    </div>
    <hr>
    {{-- ── Mensaje pie ── --}}
    <div class="mensaje-pie">
        {{ $mensajePie?->valor ?? '¡Gracias por su compra!' }}
    </div>
    @if ($negocioEmail?->valor)
        <div class="mensaje-sub">{{ $negocioEmail->valor }}</div>
    @endif
</body>

</html>
