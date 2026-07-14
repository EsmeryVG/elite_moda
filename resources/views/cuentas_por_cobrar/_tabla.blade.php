@forelse($cuentas as $cuenta)
    <tr>
        <td style="font-size:13px; font-family:monospace; font-weight:500;">{{ $cuenta->codigo }}</td>
        <td style="font-size:12px; color:var(--text-muted);">{{ $cuenta->fecha_emision->format('d/m/Y') }}</td>
        <td style="font-size:12px; color:var(--text-muted);">{{ $cuenta->fecha_vencimiento->format('d/m/Y') }}</td>
        <td style="font-size:13px;">{{ $cuenta->cliente?->nombre }} {{ $cuenta->cliente?->apellido }}</td>
        <td style="font-size:12px; color:var(--text-muted); font-family:monospace;">
            @if ($cuenta->venta)
                <a href="{{ route('ventas.show', $cuenta->venta) }}">{{ $cuenta->venta->codigo }}</a>
            @else
                —
            @endif
        </td>
        <td class="text-end" style="font-size:13px;">RD$ {{ number_format($cuenta->monto_total, 2) }}</td>
        <td class="text-end fw-semibold" style="font-size:13px;">RD$ {{ number_format($cuenta->monto_pendiente, 2) }}</td>
        <td>
            @php
                $badge = match ($cuenta->estado) {
                    'pendiente' => 'badge-cpc-pendiente',
                    'parcial' => 'badge-cpc-parcial',
                    'pagada' => 'badge-cpc-pagada',
                    'vencida' => 'badge-cpc-vencida',
                    default => 'badge-cpc-pendiente',
                };
            @endphp
            <span class="{{ $badge }}">{{ ucfirst($cuenta->estado) }}</span>
        </td>
        <td class="text-end">
            <a href="{{ route('cuentas_por_cobrar.show', $cuenta) }}" class="btn btn-outline-info btn-sm"
                title="Ver detalle">
                <i class="bi bi-eye"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="text-center py-5" style="color:var(--text-muted);">
            <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
            No hay cuentas por cobrar registradas.
        </td>
    </tr>
@endforelse

@if ($cuentas->hasPages())
    <tr>
        <td colspan="9">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $cuentas->firstItem() }}–{{ $cuentas->lastItem() }} de {{ $cuentas->total() }}
                    cuentas
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $cuentas->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $cuentas->previousPageUrl() }}"><i
                                    class="bi bi-chevron-left"></i></a>
                        </li>
                        @foreach ($cuentas->getUrlRange(1, $cuentas->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $cuentas->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$cuentas->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $cuentas->nextPageUrl() }}"><i
                                    class="bi bi-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif
