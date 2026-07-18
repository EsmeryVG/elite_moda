@extends('layouts.app')
@section('content')
    <div class="card page-card w-100">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1 fw-semibold">Comprobantes fiscales</h5>
                    <p class="text-muted mb-0" style="font-size:13px;">
                        {{ $comprobantes->count() }} comprobantes registrados
                    </p>
                </div>
                <a href="{{ route('comprobantes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo comprobante
                </a>
            </div>
            <div class="row g-3">
                @forelse($comprobantes as $comprobante)
                    <div class="col-md-6">
                        <div class="comprobante-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="comprobante-prefijo">{{ $comprobante->prefijo_ncf }}</div>
                                    <div style="font-size:13px; color:var(--text-secondary);">
                                        {{ $comprobante->tipo_comprobante }}
                                    </div>
                                </div>
                                @if ($comprobante->estado)
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
                            </div>
                            <div style="font-size:12px; color:var(--text-muted); margin-bottom:4px;">
                                Próximo: <strong style="color:var(--text-primary); font-family:monospace;">
                                    {{ $comprobante->siguienteNumero() }}
                                </strong>
                            </div>
                            <div style="font-size:12px; color:var(--text-muted);">
                                {{ $comprobante->cantidad_disponible }} de
                                {{ $comprobante->rango_fin - $comprobante->rango_inicio + 1 }} disponibles
                            </div>
                            @php
                                $nivel =
                                    $comprobante->porcentaje_uso >= 90
                                        ? 'nivel-alto'
                                        : ($comprobante->porcentaje_uso >= 70
                                            ? 'nivel-medio'
                                            : 'nivel-ok');
                            @endphp
                            <div class="barra-progreso">
                                <div class="barra-progreso-fill {{ $nivel }}"
                                    style="width: {{ $comprobante->porcentaje_uso }}%;"></div>
                            </div>
                            <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">
                                Vence: {{ $comprobante->fecha_vencimiento->format('d/m/Y') }}
                            </div>
                            <div class="d-flex gap-2 mt-3">
                                <a href="{{ route('comprobantes.show', $comprobante) }}"
                                    class="btn btn-outline-info btn-sm flex-fill">
                                    <i class="bi bi-eye me-1"></i> Ver
                                </a>
                                <a href="{{ route('comprobantes.edit', $comprobante) }}"
                                    class="btn btn-outline-warning btn-sm flex-fill">
                                    <i class="bi bi-pencil-square me-1"></i> Editar
                                </a>
                                @if ($comprobante->estado)
                                    <form action="{{ route('comprobantes.desactivar', $comprobante) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('¿Desactivar este comprobante?')">
                                            <i class="bi bi-toggle-on"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('comprobantes.reactivar', $comprobante) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-outline-success btn-sm"
                                            onclick="return confirm('¿Reactivar este comprobante?')">
                                            <i class="bi bi-toggle-off"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center py-5" style="color:var(--text-muted);">
                            <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                            No hay comprobantes fiscales registrados.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
@push('styles')
    @vite(['resources/css/comprobantes.css'])
@endpush
@push('scripts')
    @vite(['resources/js/comprobantes.js'])
@endpush
