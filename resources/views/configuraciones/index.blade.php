@extends('layouts.app')

@section('page_title', 'Configuración')
@section('page_subtitle', 'Parámetros generales del sistema')

@section('content')
    <form action="{{ route('configuraciones.update') }}" method="POST" id="formConfiguracion">
        @csrf

        <div class="row g-4">

            <div class="col-lg-8">

                {{-- Datos del negocio --}}
                <div class="card page-card mb-4">
                    <div class="card-body p-4">
                        <p class="prod-section-title">Datos del negocio</p>

                        <div class="mb-3">
                            <label class="form-label">
                                Nombre del negocio <span style="color:var(--accent);">*</span>
                            </label>
                            <input type="text" name="negocio_nombre"
                                class="form-control @error('negocio_nombre') is-invalid @enderror"
                                value="{{ old('negocio_nombre', $configs['negocio_nombre']?->valor ?? '') }}"
                                placeholder="Ej: Elite Moda">
                            @error('negocio_nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">RNC</label>
                            <input type="text" name="negocio_rnc"
                                class="form-control @error('negocio_rnc') is-invalid @enderror"
                                value="{{ old('negocio_rnc', $configs['negocio_rnc']?->valor ?? '') }}"
                                placeholder="Ej: 1-23-45678-9">
                            @error('negocio_rnc')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Se imprime en facturas de clientes con RNC (Crédito Fiscal B01).
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="negocio_email"
                                class="form-control @error('negocio_email') is-invalid @enderror"
                                value="{{ old('negocio_email', $configs['negocio_email']?->valor ?? '') }}"
                                placeholder="Ej: info@negocio.com">
                            @error('negocio_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Facturación --}}
                <div class="card page-card mb-4">
                    <div class="card-body p-4">
                        <p class="prod-section-title">Facturación</p>

                        <div class="mb-3">
                            <label class="form-label">Mensaje al pie de la factura</label>
                            <input type="text" name="factura_mensaje_pie" class="form-control"
                                value="{{ old('factura_mensaje_pie', $configs['factura_mensaje_pie']?->valor ?? '') }}"
                                placeholder="Ej: ¡Gracias por su compra!">
                            <small class="text-muted">
                                Aparece al final del recibo impreso.
                            </small>
                        </div>

                    </div>
                </div>

                {{-- Impuestos --}}
                <div class="card page-card">
                    <div class="card-body p-4">
                        <p class="prod-section-title">Impuestos</p>

                        <div class="mb-3">
                            <label class="form-label">
                                Porcentaje de ITBIS <span style="color:var(--accent);">*</span>
                            </label>
                            <div class="input-group" style="max-width:200px;">
                                <input type="number" name="itbis_porcentaje"
                                    class="form-control @error('itbis_porcentaje') is-invalid @enderror"
                                    value="{{ old('itbis_porcentaje', $configs['itbis_porcentaje']?->valor ?? '18') }}"
                                    min="0" max="100" step="0.01">
                                <span class="input-group-text"
                                    style="background:var(--bg-elevated); border-color:var(--border);
                                     color:var(--text-muted);">%</span>
                            </div>
                            @error('itbis_porcentaje')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Cambiarlo no afecta transacciones pasadas.
                            </small>
                        </div>

                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                <div class="card page-card">
                    <div class="card-body p-4">
                        <p class="text-muted mb-4" style="font-size:12.5px; line-height:1.6;">
                            <i class="bi bi-info-circle me-1"></i>
                            Los cambios aplican inmediatamente a todas las operaciones nuevas.
                            Las ventas y compras anteriores no se ven afectadas.
                        </p>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Guardar cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
@endsection

@push('styles')
    @vite(['resources/css/usuarios.css'])
@endpush
