@extends('layouts.app')

@section('page_title', $cliente->nombre_completo)
@section('page_subtitle', 'Ficha del cliente')

@section('content')
<div class="row g-4">

    {{-- Info del cliente --}}
    <div class="col-lg-4">
        <div class="card page-card mb-4">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h6 class="fw-semibold mb-1">Información general</h6>
                        <p class="text-muted mb-0" style="font-size:12px;">
                            Datos registrados del cliente.
                        </p>
                    </div>
                    @if($cliente->estado)
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

                <div class="mb-3">
                    <span class="field-label">Código</span>
                    <div class="field-readonly"
                         style="font-family:monospace; font-size:13px;">
                        {{ $cliente->codigo }}
                    </div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Nombre completo</span>
                    <div class="field-readonly">{{ $cliente->nombre_completo }}</div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Cédula</span>
                    <div class="field-readonly">{{ $cliente->cedula ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <span class="field-label">RNC</span>
                    <div class="field-readonly">{{ $cliente->rnc ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Teléfono</span>
                    <div class="field-readonly">{{ $cliente->telefono ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Email</span>
                    <div class="field-readonly">{{ $cliente->email ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Dirección</span>
                    <div class="field-readonly">{{ $cliente->direccion ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Grupo</span>
                    <div class="field-readonly">
                        {{ $cliente->grupo?->nombre ?? '—' }}
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('clientes.edit', $cliente) }}"
                       class="btn btn-primary flex-fill">
                        <i class="bi bi-pencil-square me-1"></i> Editar
                    </a>
                    <a href="{{ route('clientes.index') }}"
                       class="btn btn-secondary">
                        Volver
                    </a>
                </div>

            </div>
        </div>

        {{-- Crédito --}}
        <div class="card page-card">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3">Crédito</h6>

                <div class="mb-2 d-flex justify-content-between">
                    <span style="font-size:13px; color:var(--text-muted);">Estado</span>
                    @if($cliente->credito_activo)
                        <span class="credito-activo">
                            <i class="bi bi-check-circle me-1"></i> Activo
                        </span>
                    @else
                        <span class="credito-inactivo">Inactivo</span>
                    @endif
                </div>

                <div class="mb-2 d-flex justify-content-between">
                    <span style="font-size:13px; color:var(--text-muted);">Límite</span>
                    <span style="font-size:13px; font-weight:500;">
                        RD$ {{ number_format($cliente->limite_credito, 2) }}
                    </span>
                </div>

                <div class="d-flex justify-content-between">
                    <span style="font-size:13px; color:var(--text-muted);">Balance deuda</span>
                    <span class="{{ $cliente->balance_credito > 0 ? 'balance-deuda' : '' }}"
                          style="font-size:13px;">
                        RD$ {{ number_format($cliente->balance_credito, 2) }}
                    </span>
                </div>

            </div>
        </div>
    </div>

    {{-- Historial --}}
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">

                <h6 class="fw-semibold mb-4">Historial de compras</h6>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cliente->ventas as $venta)
                                <tr>
                                    <td style="font-family:monospace; font-size:12px;
                                               color:var(--text-muted);">
                                        {{ $venta->codigo }}
                                    </td>
                                    <td style="font-size:13px;">
                                        {{ $venta->created_at->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill"
                                              style="font-size:11px; padding:3px 10px;
                                                     background:var(--bg-hover);
                                                     color:var(--text-secondary);">
                                            {{ ucfirst($venta->estado) }}
                                        </span>
                                    </td>
                                    <td class="text-end" style="font-size:13px;">
                                        RD$ {{ number_format($venta->total, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4"
                                        style="color:var(--text-muted);">
                                        No hay compras registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
    @vite(['resources/css/clientes.css'])
@endpush