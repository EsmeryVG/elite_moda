@extends('layouts.app')

@section('page_title', $comprobante->prefijo_ncf)
@section('page_subtitle', $comprobante->tipo_comprobante)

@section('content')
<div class="row g-4">

    <div class="col-lg-4">
        <div class="card page-card">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-4">Información del comprobante</h6>

                <div class="mb-3">
                    <span class="field-label">Prefijo NCF</span>
                    <div class="field-readonly" style="font-family:monospace; font-size:16px;">
                        {{ $comprobante->prefijo_ncf }}
                    </div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Tipo</span>
                    <div class="field-readonly">{{ $comprobante->tipo_comprobante }}</div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Rango autorizado</span>
                    <div class="field-readonly">
                        {{ $comprobante->rango_inicio }} — {{ $comprobante->rango_fin }}
                    </div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Número actual</span>
                    <div class="field-readonly" style="font-family:monospace;">
                        {{ $comprobante->siguienteNumero() }}
                    </div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Disponibles</span>
                    <div class="field-readonly">
                        {{ $comprobante->cantidad_disponible }} comprobantes restantes
                    </div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Vencimiento</span>
                    <div class="field-readonly">
                        {{ $comprobante->fecha_vencimiento->format('d/m/Y') }}
                    </div>
                </div>

                <a href="{{ route('comprobantes.index') }}" class="btn btn-secondary w-100 mt-3">
                    Volver
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-4">Ventas emitidas con este comprobante</h6>

                @forelse($comprobante->ventas as $venta)
                    <div class="mb-2 p-3"
                         style="border:1px solid var(--border); border-radius:var(--radius-md);">
                        <span style="font-family:monospace; font-size:12px; color:var(--text-muted);">
                            {{ $venta->ncf }}
                        </span>
                        <span class="ms-2" style="font-size:13px;">
                            RD$ {{ number_format($venta->total, 2) }}
                        </span>
                    </div>
                @empty
                    <p class="text-center py-3" style="color:var(--text-muted); font-size:13px;">
                        Aún no se ha emitido ninguna venta con este comprobante.
                    </p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
    @vite(['resources/css/comprobantes.css'])
@endpush