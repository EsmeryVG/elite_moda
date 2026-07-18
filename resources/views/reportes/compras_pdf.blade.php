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
            table-layout: fixed;
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
            table-layout: fixed;
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
            word-wrap: break-word;
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
    <h1>Reporte de Compras</h1>
    <div class="subtitulo">Elite Moda — Período: {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}</div>

    <table class="kpis">
        <tr>
            <td>
                <div class="kpi-label">Total comprado</div>
                <div class="kpi-valor">RD$ {{ number_format($totalComprado, 2) }}</div>
            </td>
            <td>
                <div class="kpi-label">Cant. órdenes</div>
                <div class="kpi-valor">{{ $cantidadOrdenes }}</div>
            </td>
            <td>
                <div class="kpi-label">Orden promedio</div>
                <div class="kpi-valor">RD$ {{ number_format($ordenPromedio, 2) }}</div>
            </td>
            <td>
                <div class="kpi-label">Retrasadas (hoy)</div>
                <div class="kpi-valor">{{ $ordenesRetrasadas }}</div>
            </td>
        </tr>
    </table>

    <h3>Compras por proveedor</h3>
    <table class="datos">
        <colgroup>
            <col style="width:50%;">
            <col style="width:20%;">
            <col style="width:30%;">
        </colgroup>
        <thead>
            <tr>
                <th>Proveedor</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($porProveedor as $fila)
                <tr>
                    <td>{{ $fila->proveedor }}</td>
                    <td class="text-center">{{ $fila->cantidad }}</td>
                    <td class="text-end">RD$ {{ number_format($fila->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Sin compras en el período.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Órdenes por estado</h3>
    <table class="datos">
        <colgroup>
            <col style="width:50%;">
            <col style="width:20%;">
            <col style="width:30%;">
        </colgroup>
        <thead>
            <tr>
                <th>Estado</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($porEstado as $fila)
                <tr>
                    <td>{{ ucfirst($fila->estado) }}</td>
                    <td class="text-center">{{ $fila->cantidad }}</td>
                    <td class="text-end">RD$ {{ number_format($fila->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Sin órdenes en el período.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Top 10 productos comprados</h3>
    <table class="datos">
        <colgroup>
            <col style="width:55%;">
            <col style="width:20%;">
            <col style="width:25%;">
        </colgroup>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Total gastado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProductosComprados as $fila)
                <tr>
                    <td>{{ $fila->variante?->producto?->nombre ?? '—' }}</td>
                    <td class="text-center">{{ $fila->cantidad_solicitada }}</td>
                    <td class="text-end">RD$ {{ number_format($fila->total_gastado, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Sin datos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Órdenes retrasadas</h3>
    <table class="datos">
        <colgroup>
            <col style="width:20%;">
            <col style="width:35%;">
            <col style="width:20%;">
            <col style="width:25%;">
        </colgroup>
        <thead>
            <tr>
                <th>Código</th>
                <th>Proveedor</th>
                <th class="text-center">Días</th>
                <th>Fecha esperada</th>
            </tr>
        </thead>
        <tbody>
            @forelse($listadoRetrasadas as $fila)
                <tr>
                    <td>{{ $fila->codigo }}</td>
                    <td>{{ $fila->proveedor?->nombre ?? '—' }}</td>
                    <td class="text-center">{{ $fila->dias_retraso }}</td>
                    <td>{{ $fila->fecha_esperada?->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No hay órdenes retrasadas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
