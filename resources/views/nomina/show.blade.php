@extends('layouts.app')

@section('page_title', 'Nómina')
@section('page_subtitle', $nomina->periodo_inicio->format('d/m/Y') . ' — ' . $nomina->periodo_fin->format('d/m/Y'))

@section('content')
    <div class="row g-4">

        <div class="col-lg-4">
            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <h6 class="fw-semibold mb-0">Información general</h6>
                        <span class="{{ $nomina->estado === 'pagada' ? 'badge-nomina-pagada' : 'badge-nomina-pendiente' }}">
                            {{ ucfirst($nomina->estado) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <span class="field-label">Período</span>
                        <div class="field-readonly">
                            {{ $nomina->periodo_inicio->format('d/m/Y') }} — {{ $nomina->periodo_fin->format('d/m/Y') }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="field-label">Fecha de generación</span>
                        <div class="field-readonly">{{ $nomina->fecha_pago->format('d/m/Y') }}</div>
                    </div>
                    <div class="mb-0">
                        <span class="field-label">Generada por</span>
                        <div class="field-readonly">{{ $nomina->usuario?->name ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <div class="nomina-resumen-box text-center">
                        <div
                            style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">
                            Total general
                        </div>
                        <div style="font-size:28px; font-weight:700; margin-top:6px;">
                            RD$ {{ number_format($nomina->total_general, 2) }}
                        </div>
                        <div style="font-size:12px; color:var(--text-muted);">
                            {{ $nomina->detalles->count() }} empleados
                        </div>
                    </div>
                </div>
            </div>

            @if ($nomina->estado === 'pendiente' && Auth::user()->esAdministrador())
                <div class="card page-card">
                    <div class="card-body p-4">
                        <form action="{{ route('nomina.marcar_pagada', $nomina) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-primary w-100"
                                onclick="return confirm('¿Marcar toda la nómina como pagada? Esto marcará todas las comisiones asociadas como pagadas.')">
                                <i class="bi bi-check2-circle me-1"></i> Marcar nómina como pagada
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-8">
            <div class="card page-card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-4">Detalle por empleado</h6>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Empleado</th>
                                    <th class="text-end" style="width:110px;">Salario base</th>
                                    <th class="text-end" style="width:110px;">Comisiones</th>
                                    <th class="text-end" style="width:120px;">Total a pagar</th>
                                    <th style="width:60px;" class="text-end">Ver</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($nomina->detalles as $detalle)
                                    <tr>
                                        <td style="font-size:13px; font-weight:500;">
                                            {{ $detalle->empleado?->nombre_completo }}
                                        </td>
                                        <td class="text-end" style="font-size:13px;">
                                            RD$ {{ number_format($detalle->salario_base, 2) }}
                                        </td>
                                        <td class="text-end" style="font-size:13px; color:#2e7d32;">
                                            RD$ {{ number_format($detalle->total_comisiones, 2) }}
                                        </td>
                                        <td class="text-end fw-semibold" style="font-size:13px;">
                                            RD$ {{ number_format($detalle->total_pagar, 2) }}
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('nomina.detalle_empleado', $detalle) }}"
                                                class="btn btn-outline-info btn-sm" title="Ver detalle">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    @vite(['resources/css/nomina.css'])
@endpush
