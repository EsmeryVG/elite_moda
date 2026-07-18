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
    <h1>Reporte de Crédito y Cobros</h1>
    <div class="subtitulo">Elite Moda — Cartera a hoy {{ now()->format('d/m/Y') }} — Cobros del
        {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}</div>

    <table class="kpis">
        <tr>
            <td>
                <div class="kpi-label">Cartera total</div>
                <div class="kpi-valor">RD$ {{ number_format($carteraTotal, 2) }}</div>
            </td>
            <td>
                <div class="kpi-label">Cartera vencida</div>
                <div class="kpi-valor">RD$ {{ number_format($carteraVencida, 2) }}</div>
            </td>
            <td>
                <div class="kpi-label">Cobrado en período</div>
                <div class="kpi-valor">RD$ {{ number_format($cobradoPeriodo, 2) }}</div>
            </td>
            <td>
                <div class="kpi-label">Cuentas activas</div>
                <div class="kpi-valor">{{ $cantidadCuentasActivas }}</div>
            </td>
        </tr>
    </table>

    <h3>Antigüedad de cartera vencida</h3>
    <table class="datos">
        <colgroup>
            <col style="width:40%;">
            <col style="width:30%;">
            <col style="width:30%;">
        </colgroup>
        <thead>
            <tr>
                <th>Tramo</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($antiguedad as $tramo => $info)
                <tr>
                    <td>{{ $tramo }} días</td>
                    <td class="text-center">{{ $info['cantidad'] }}</td>
                    <td class="text-end">RD$ {{ number_format($info['monto'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Cartera por cliente</h3>
    <table class="datos">
        <colgroup>
            <col style="width:35%;">
            <col style="width:15%;">
            <col style="width:20%;">
            <col style="width:15%;">
            <col style="width:15%;">
        </colgroup>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Código</th>
                <th class="text-end">Pendiente</th>
                <th>Vence</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($porCliente as $fila)
                <tr>
                    <td>{{ trim(($fila->cliente?->nombre ?? '') . ' ' . ($fila->cliente?->apellido ?? '')) ?: '—' }}
                    </td>
                    <td>{{ $fila->codigo }}</td>
                    <td class="text-end">RD$ {{ number_format($fila->monto_pendiente, 2) }}</td>
                    <td>{{ $fila->fecha_vencimiento?->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($fila->estado) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No hay cuentas activas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Historial de abonos del período</h3>
    <table class="datos">
        <colgroup>
            <col style="width:15%;">
            <col style="width:30%;">
            <col style="width:20%;">
            <col style="width:15%;">
            <col style="width:20%;">
        </colgroup>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Método</th>
                <th class="text-end">Monto</th>
                <th>Registrado por</th>
            </tr>
        </thead>
        <tbody>
            @forelse($historialAbonos as $fila)
                <tr>
                    <td>{{ $fila->fecha->format('d/m/Y') }}</td>
                    <td>{{ trim(($fila->cuenta?->cliente?->nombre ?? '') . ' ' . ($fila->cuenta?->cliente?->apellido ?? '')) ?: '—' }}
                    </td>
                    <td>{{ $fila->tipoPago?->nombre ?? '—' }}</td>
                    <td class="text-end">RD$ {{ number_format($fila->monto, 2) }}</td>
                    <td>{{ $fila->usuario?->name ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Sin abonos en el período.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
