@extends('layouts.app')

@section('page_title', $proveedor->nombre)
@section('page_subtitle', 'Detalle del proveedor')

@section('content')
<div class="row g-4">

    {{-- Info del proveedor --}}
    <div class="col-lg-4">
        <div class="card page-card">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h6 class="fw-semibold mb-1">Información general</h6>
                        <p class="text-muted mb-0" style="font-size:12px;">
                            Datos registrados del proveedor.
                        </p>
                    </div>
                    @if($proveedor->estado)
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
                        {{ $proveedor->codigo }}
                    </div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Nombre</span>
                    <div class="field-readonly">{{ $proveedor->nombre }}</div>
                </div>
                <div class="mb-3">
                    <span class="field-label">RNC</span>
                    <div class="field-readonly">{{ $proveedor->rnc ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Teléfono</span>
                    <div class="field-readonly">{{ $proveedor->telefono ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Email</span>
                    <div class="field-readonly">{{ $proveedor->email ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Contacto</span>
                    <div class="field-readonly">{{ $proveedor->contacto_nombre ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <span class="field-label">Dirección</span>
                    <div class="field-readonly">{{ $proveedor->direccion ?? '—' }}</div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('proveedores.edit', $proveedor) }}"
                       class="btn btn-primary flex-fill">
                        <i class="bi bi-pencil-square me-1"></i> Editar
                    </a>
                    <a href="{{ route('proveedores.index') }}"
                       class="btn btn-secondary">
                        Volver
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- Historial de órdenes --}}
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="fw-semibold mb-1">Órdenes de compra</h6>
                        <p class="text-muted mb-0" style="font-size:12px;">
                            Últimas órdenes registradas
                        </p>
                    </div>
                </div>

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
                            @forelse($proveedor->ordenesCompra as $orden)
                                <tr>
                                    <td style="font-family:monospace; font-size:12px;
                                               color:var(--text-muted);">
                                        {{ $orden->codigo }}
                                    </td>
                                    <td style="font-size:13px;">
                                        {{ $orden->created_at->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill"
                                              style="font-size:11px; padding:3px 10px;
                                                     background:var(--bg-hover);
                                                     color:var(--text-secondary);">
                                            {{ ucfirst($orden->estado) }}
                                        </span>
                                    </td>
                                    <td class="text-end" style="font-size:13px;">
                                        RD$ {{ number_format($orden->total, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4"
                                        style="color:var(--text-muted);">
                                        No hay órdenes de compra registradas.
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
    @vite(['resources/css/proveedores.css'])
@endpush