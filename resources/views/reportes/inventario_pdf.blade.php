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
            margin-bottom: 12px;
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
            font-size: 13px;
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
    <h1>Reporte de Inventario</h1>
    <div class="subtitulo">Elite Moda — Generado el {{ now()->format('d/m/Y H:i') }}</div>

    <table class="kpis">
        <tr>
            <td>
                <div class="kpi-label">Valor (costo)</div>
                <div class="kpi-valor">RD$ {{ number_format($valorTotalCosto, 2) }}</div>
            </td>
            <td>
                <div class="kpi-label">Valor (venta)</div>
                <div class="kpi-valor">RD$ {{ number_format($valorTotalVenta, 2) }}</div>
            </td>
            <td>
                <div class="kpi-label">Utilidad potencial</div>
                <div class="kpi-valor">RD$ {{ number_format($utilidadPotencial, 2) }}</div>
            </td>
            <td>
                <div class="kpi-label">SKUs activos</div>
                <div class="kpi-valor">{{ $cantidadSkusActivos }}</div>
            </td>
        </tr>
    </table>
    <table class="kpis">
        <tr>
            <td>
                <div class="kpi-label">Stock crítico</div>
                <div class="kpi-valor">{{ $stockCritico }}</div>
            </td>
            <td>
                <div class="kpi-label">Agotados</div>
                <div class="kpi-valor">{{ $stockAgotado }}</div>
            </td>
            <td colspan="2"></td>
        </tr>
    </table>

    <h3>Valorización por almacén</h3>
    <table class="datos">
        <colgroup>
            <col style="width:40%;">
            <col style="width:15%;">
            <col style="width:22.5%;">
            <col style="width:22.5%;">
        </colgroup>
        <thead>
            <tr>
                <th>Almacén</th>
                <th class="text-center">Unidades</th>
                <th class="text-end">Costo</th>
                <th class="text-end">Venta</th>
            </tr>
        </thead>
        <tbody>
            @forelse($valorizacionPorAlmacen as $fila)
                <tr>
                    <td>{{ $fila->almacen }}</td>
                    <td class="text-center">{{ $fila->unidades }}</td>
                    <td class="text-end">RD$ {{ number_format($fila->valor_costo, 2) }}</td>
                    <td class="text-end">RD$ {{ number_format($fila->valor_venta, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Sin datos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Valorización por categoría</h3>
    <table class="datos">
        <colgroup>
            <col style="width:40%;">
            <col style="width:15%;">
            <col style="width:22.5%;">
            <col style="width:22.5%;">
        </colgroup>
        <thead>
            <tr>
                <th>Categoría</th>
                <th class="text-center">Unidades</th>
                <th class="text-end">Costo</th>
                <th class="text-end">Venta</th>
            </tr>
        </thead>
        <tbody>
            @forelse($valorizacionPorCategoria as $fila)
                <tr>
                    <td>{{ $fila->categoria }}</td>
                    <td class="text-center">{{ $fila->unidades }}</td>
                    <td class="text-end">RD$ {{ number_format($fila->valor_costo, 2) }}</td>
                    <td class="text-end">RD$ {{ number_format($fila->valor_venta, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Sin datos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Top 10 productos por valor en inventario</h3>
    <table class="datos">
        <colgroup>
            <col style="width:55%;">
            <col style="width:20%;">
            <col style="width:25%;">
        </colgroup>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-center">Unidades</th>
                <th class="text-end">Valor (costo)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topValorInventario as $fila)
                <tr>
                    <td>{{ $fila->variante?->producto?->nombre ?? '—' }}</td>
                    <td class="text-center">{{ $fila->unidades }}</td>
                    <td class="text-end">RD$ {{ number_format($fila->valor_costo, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Sin datos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Stock crítico y agotado</h3>
    <table class="datos">
        <colgroup>
            <col style="width:35%;">
            <col style="width:25%;">
            <col style="width:13%;">
            <col style="width:12%;">
            <col style="width:15%;">
        </colgroup>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Almacén</th>
                <th class="text-center">Disp.</th>
                <th class="text-center">Mín.</th>
                <th>Nivel</th>
            </tr>
        </thead>
        <tbody>
            @forelse($listadoCritico as $fila)
                <tr>
                    <td>{{ $fila->variante?->producto?->nombre ?? '—' }}</td>
                    <td>{{ $fila->almacen?->nombre ?? '—' }}</td>
                    <td class="text-center">{{ $fila->cantidad_disponible }}</td>
                    <td class="text-center">{{ $fila->stock_minimo }}</td>
                    <td>{{ ucfirst($fila->nivel) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Sin productos en estado crítico.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Sin movimiento en {{ $diasSinMovimiento }}+ días</h3>
    <table class="datos">
        <colgroup>
            <col style="width:65%;">
            <col style="width:35%;">
        </colgroup>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Código</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sinMovimiento as $fila)
                <tr>
                    <td>{{ $fila->producto?->nombre ?? '—' }}</td>
                    <td>{{ $fila->codigo }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Todo el catálogo tiene movimiento reciente.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
