@php
    function badgeAplicaA($aplicaA) {
        return match($aplicaA) {
            'producto'      => '<span class="badge-aplica-producto">Producto</span>',
            'cliente'       => '<span class="badge-aplica-cliente">Cliente</span>',
            'grupo_cliente' => '<span class="badge-aplica-grupo">Grupo de cliente</span>',
            default         => '<span style="color:var(--text-muted); font-size:11px;">Sin definir</span>',
        };
    }
@endphp

@forelse($descuentos as $descuento)
    <tr>
        <td class="fw-semibold" style="font-size:13px;">
            {{ $descuento->nombre }}
        </td>
        <td style="font-size:13px;">
            {!! badgeAplicaA($descuento->aplica_a) !!}
            @if($descuento->aplica_a === 'cliente')
                <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                    {{ $descuento->cliente?->nombre_completo ?? $descuento->cliente?->nombre }}
                </div>
            @elseif($descuento->aplica_a === 'producto')
                <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                    {{ $descuento->variantes->count() }} variante(s)
                </div>
            @elseif($descuento->aplica_a === 'grupo_cliente')
                <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                    {{ $descuento->gruposCliente->pluck('nombre')->join(', ') }}
                </div>
            @endif
        </td>
        <td class="text-center" style="font-size:13px;">
            @if($descuento->tipo === 'porcentaje')
                {{ $descuento->valor }}%
            @else
                RD$ {{ number_format($descuento->valor, 2) }}
            @endif
        </td>
        <td style="font-size:12px; color:var(--text-muted);">
            @if($descuento->fecha_inicio || $descuento->fecha_fin)
                {{ $descuento->fecha_inicio?->format('d/m/Y') ?? '—' }} —
                {{ $descuento->fecha_fin?->format('d/m/Y') ?? '—' }}
            @else
                Sin vigencia definida
            @endif
        </td>
        <td>
            @if($descuento->requiere_autorizacion)
                <span style="color:#e65100; font-size:12px;">
                    <i class="bi bi-shield-lock me-1"></i>Sí
                </span>
            @else
                <span style="color:var(--text-muted); font-size:12px;">No</span>
            @endif
        </td>
        <td>
            @if($descuento->estado)
                <span class="badge rounded-pill"
                      style="background:rgba(76,175,80,0.12); color:#2e7d32;
                             font-size:11px; padding:4px 10px;">
                    Activo
                </span>
            @else
                <span class="badge rounded-pill"
                      style="background:rgba(158,158,158,0.15); color:#757575;
                             font-size:11px; padding:4px 10px;">
                    Inactivo
                </span>
            @endif
        </td>
        <td class="text-end">
            <a href="{{ route('descuentos.edit', $descuento) }}"
               class="btn btn-outline-warning btn-sm" title="Editar">
                <i class="bi bi-pencil-square"></i>
            </a>
            @if($descuento->estado)
                <form action="{{ route('descuentos.destroy', $descuento) }}"
                      method="POST" class="d-inline-block">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm"
                            title="Desactivar"
                            onclick="return confirm('¿Desactivar {{ $descuento->nombre }}?')">
                        <i class="bi bi-toggle-on"></i>
                    </button>
                </form>
            @else
                <form action="{{ route('descuentos.reactivar', $descuento) }}"
                      method="POST" class="d-inline-block">
                    @csrf @method('PATCH')
                    <button class="btn btn-outline-success btn-sm"
                            title="Reactivar"
                            onclick="return confirm('¿Reactivar {{ $descuento->nombre }}?')">
                        <i class="bi bi-toggle-off"></i>
                    </button>
                </form>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-5" style="color:var(--text-muted);">
            @if(request('buscar') || request('estado'))
                <i class="bi bi-search" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No se encontraron descuentos con ese criterio.
                <br>
                <a href="{{ route('descuentos.index') }}" class="btn btn-secondary btn-sm mt-3">
                    Limpiar filtros
                </a>
            @else
                <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No hay descuentos registrados.
            @endif
        </td>
    </tr>
@endforelse

@if($descuentos->hasPages())
    <tr>
        <td colspan="7">
            <div class="d-flex justify-content-between align-items-center py-3 px-1">
                <span style="font-size:13px; color:var(--text-muted);">
                    Mostrando {{ $descuentos->firstItem() }}–{{ $descuentos->lastItem() }}
                    de {{ $descuentos->total() }} descuentos
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 em-pagination">
                        <li class="page-item {{ $descuentos->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $descuentos->previousPageUrl() }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @foreach($descuentos->getUrlRange(1, $descuentos->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $descuentos->currentPage() ? 'active' : '' }}">
                                <a class="page-link ajax-page" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$descuentos->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link ajax-page" href="{{ $descuentos->nextPageUrl() }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </td>
    </tr>
@endif