<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
        }

        h1 {
            font-size: 16px;
            margin-bottom: 2px;
        }

        .subtitulo {
            color: #666;
            font-size: 11px;
            margin-bottom: 16px;
        }

        .kpis {
            width: 100%;
            margin-bottom: 16px;
        }

        .kpis td {
            width: 25%;
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .kpi-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }

        .kpi-valor {
            font-size: 14px;
            font-weight: bold;
            margin-top: 2px;
        }

        table.datos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        table.datos th {
            background: #f0f0f0;
            padding: 5px 8px;
            text-align: left;
            font-size: 10px;
            border-bottom: 1px solid #ccc;
        }

        table.datos td {
            padding: 5px 8px;
            font-size: 10px;
            border-bottom: 1px solid #eee;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        h3 {
            font-size: 12px;
            margin-bottom: 6px;
            margin-top: 0;
        }
    </style>
</head>

<body>
    <h1>Reporte de Ventas</h1>
    <div class="subtitulo">Elite Moda — Período: {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}</div>

    <table class="kpis">
        <tr>
            <td>
                <div class="kpi-label">Total vendido</div>
                <div class="kpi-valor">RD$ {{ number_format($totalVendido, 2) }}</div>
            </td>
            <td>
                <div class="kpi-label">Cant. ventas</div>
                <div class="kpi-valor">{{ $cantidadVentas }}</div>
            </td>
            <td>
                <div class="kpi-label">Venta promedio</div>
                <div class="kpi-valor">RD$ {{ number_format($ticketPromedio, 2) }}</div>
            </td>
            <td>
                <div class="kpi-label">ITBIS cobrado</div>
                <div class="kpi-valor">RD$ {{ number_format($totalItbis, 2) }}</div>
            </td>
        </tr>
    </table>

    <h3>Ventas por día</h3>
    <table class="datos">
        <thead>
            <tr>
                <th>Fecha</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventasPorDia as $fila)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($fila->dia)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $fila->cantidad }}</td>
                    <td class="text-end">RD$ {{ number_format($fila->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Sin ventas en el período.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Por método de pago</h3>
    <table class="datos">
        <thead>
            <tr>
                <th>Método</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($porMetodoPago as $fila)
                <tr>
                    <td>{{ $fila->metodo }}</td>
                    <td class="text-center">{{ $fila->cantidad }}</td>
                    <td class="text-end">RD$ {{ number_format($fila->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Sin pagos en el período.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Top 10 productos</h3>
    <table class="datos">
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProductos as $fila)
                <tr>
                    <td>{{ $fila->variante?->producto?->nombre ?? '—' }}</td>
                    <td class="text-center">{{ $fila->cantidad_vendida }}</td>
                    <td class="text-end">RD$ {{ number_format($fila->total_vendido, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Sin datos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Por vendedor</h3>
    <table class="datos">
        <thead>
            <tr>
                <th>Vendedor</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($porVendedor as $fila)
                <tr>
                    <td>{{ $fila->empleado?->nombre_completo ?? '—' }}</td>
                    <td class="text-center">{{ $fila->cantidad }}</td>
                    <td class="text-end">RD$ {{ number_format($fila->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Sin ventas asignadas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
