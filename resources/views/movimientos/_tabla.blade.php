@php
    function badgeTipoMovimiento($tipo) {
        return match($tipo) {
            'entrada_compra'         => ['bg' => 'rgba(76,175,80,0.12)',  'color' => '#2e7d32', 'texto' => 'Entrada por compra', 'icono' => 'box-arrow-in-down'],
            'salida_venta'           => ['bg' => 'rgba(211,47,47,0.10)',  'color' => '#c62828', 'texto' => 'Salida por venta',   'icono' => 'box-arrow-up'],
            'entrada_devolucion'     => ['bg' => 'rgba(33,150,243,0.10)', 'color' => '#1565c0', 'texto' => 'Devolución',         'icono' => 'arrow-counterclockwise'],
            'ajuste_positivo'        => ['bg' => 'rgba(76,175,80,0.12)',  'color' => '#2e7d32', 'texto' => 'Ajuste (+)',         'icono' => 'plus-circle'],
            'ajuste_negativo'        => ['bg' => 'rgba(211,47,47,0.10)',  'color' => '#c62828', 'texto' => 'Ajuste (-)',         'icono' => 'dash-circle'],
            'transferencia_entrada'  => ['bg' => 'rgba(33,150,243,0.10)', 'color' => '#1565c0', 'texto' => 'Transferencia (+)',  'icono' => 'arrow-down-left'],
            'transferencia_salida'   => ['bg' => 'rgba(255,152,0,0.12)',  'color' => '#e65100', 'texto' => 'Transferencia (-)',  'icono' => 'arrow-up-right'],
            default                  => ['bg' => 'var(--bg-hover)',      'color' => 'var(--text-muted)', 'texto' => ucfirst($tipo), 'icono' => 'arrow-left-right'],
        };
    }

    $referenciaRutas = [
        'recepcion_mercancia' => 'recepciones.show',
        'ajuste_inventario'   => 'ajustes.show',
    ];
@endphp

@forelse($movimientos as $movimiento)
    <tr>
        <td style="font-size:12px; color:var(--text-muted);">
            {{ $movimiento->fecha->format('d/m/Y H:i') }}
        </td>
        <td>
            <div class="fw-semibold" style="font-size:13px;">
                {{ $movimiento->variante?->producto?->nombre }}
            </div>
            <div style="font-size:11px; color:var(--text-muted);">
                @foreach($movimiento->variante?->valores ?? [] as $valor)
                    {{ $valor->atributo?->nombre }}: {{ $valor->valor }}
                    @if(!$loop->last) · @endif
                @endforeach
            </div>
        </td>
        <td style="font-size:13px; color:var(--text-muted);">
            {{ $movimiento->almacen?->nombre }}
        </td>
        <td>
            @php $badge = badgeTipoMovimiento($movimiento->tipo); @endphp
            <span class="badge rounded-pill"
                  style="background:{{ $badge['bg'] }}; color:{{ $badge['color'] }};
                         font-size:11px; padding:3px 10px;">
                <i class="bi bi-{{ $badge['icono'] }} me-1"></i>
                {{ $badge['texto'] }}
            </span>
        </td>
        <td class="text-center">
            @php
                $esEntrada = in_array($movimiento->tipo, ['entrada_compra', 'entrada_devolucion', 'ajuste_positivo', 'transferencia_entrada']);
            @endphp
            <span style="font-weight:700; font-size:14px;
                         color:{{ $esEntrada ? '#2e7d32' : 'var(--accent)' }};">
                {{ $esEntrada ? '+' : '-' }}{{ $movimiento->cantidad }}
            </span>
        </td>
        <td style="font-size:12px; color:var(--text-muted);">
            @if($movimiento->referencia_tipo && isset($referenciaRutas[$movimiento->referencia_tipo]))
                <a href="{{ route($referenciaRutas[$movimiento->referencia_tipo], $movimiento->referencia_id) }}"
                   style="color:var(--text-secondary); text-decoration:none;">
                    {{ $movimiento->motivo ?? ucfirst(str_replace('_', ' ', $movimiento->referencia_tipo)) }}
                    <i class="bi bi-arrow-up-right-square ms-1" style="font-size:10px;"></i>
                </a>
            @else
                {{ $movimiento->motivo ?? '—' }}
            @endif
        </td>
        <td style="font-size:12px; color:var(--text-muted);">
            {{ $movimiento->usuario?->name ?? '—' }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-5" style="color:var(--text-muted);">
            @if(request('buscar') || request('tipo') || request('almacen'))
                <i class="bi bi-search" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No se encontraron movimientos con ese criterio.
                <br>
                <a href="{{ route('movimientos.index') }}" class="btn btn-secondary btn-sm mt-3">
                    Limpiar filtros
                </a>
            @else
                <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No hay movimientos registrados.
            @endif
        </td>
    </tr>
@endforelse

@if($movimientos->hasPages())
    <tr>
        <td colspan="7">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $movimientos->firstItem() }}–{{ $movimientos->lastItem() }}
                    de {{ $movimientos->total() }} movimientos
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $movimientos->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $movimientos->previousPageUrl() }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @foreach($movimientos->getUrlRange(1, $movimientos->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $movimientos->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$movimientos->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $movimientos->nextPageUrl() }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif