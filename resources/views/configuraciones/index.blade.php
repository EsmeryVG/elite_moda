@extends('layouts.app')

@section('page_title', 'Configuración')
@section('page_subtitle', 'Parámetros generales del sistema')

@section('content')
    <form action="{{ route('configuraciones.update') }}" method="POST" id="formConfiguracion" style="padding-bottom:90px;">
        @csrf

        {{-- Pestañas --}}
        <div class="d-flex gap-2 flex-wrap mb-4">
            <button type="button" class="btn btn-sm em-filtro tab-config active" data-tab="general">General</button>
            <button type="button" class="btn btn-sm em-filtro tab-config" data-tab="ventas">Ventas y Facturación</button>
            <button type="button" class="btn btn-sm em-filtro tab-config" data-tab="caja">Caja</button>
            <button type="button" class="btn btn-sm em-filtro tab-config" data-tab="personal">Personal</button>
        </div>

        <div class="row g-4">
            <div class="col-12">

                {{-- ═══════════════ TAB: General ═══════════════ --}}
                <div class="config-tab-panel" data-panel="general">

                    {{-- Datos del negocio --}}
                    <div class="card page-card mb-4">
                        <div class="card-body p-4">
                            <p class="prod-section-title">Datos del negocio</p>

                            <div class="row">
                                <div class="col-md-6 mb-3">
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

                                <div class="col-md-6 mb-3">
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

                                <div class="col-md-6 mb-3">
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
                    </div>

                    {{-- Impuestos --}}
                    <div class="card page-card mb-4">
                        <div class="card-body p-4">
                            <p class="prod-section-title">Impuestos</p>

                            <div class="mb-0" style="max-width:220px;">
                                <label class="form-label">
                                    Porcentaje de ITBIS <span style="color:var(--accent);">*</span>
                                </label>
                                <div class="input-group">
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

                {{-- ═══════════════ TAB: Ventas y Facturación ═══════════════ --}}
                <div class="config-tab-panel" data-panel="ventas" style="display:none;">

                    {{-- Facturación --}}
                    <div class="card page-card mb-4">
                        <div class="card-body p-4">
                            <p class="prod-section-title">Facturación</p>

                            <div class="mb-0">
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

                    {{-- Devoluciones --}}
                    <div class="card page-card mb-4">
                        <div class="card-body p-4">
                            <p class="prod-section-title">Devoluciones</p>

                            <div class="row">
                                <div class="col-md-6 mb-0">
                                    <label class="form-label">
                                        Límite de días para devolución <span style="color:var(--accent);">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="devolucion_dias_limite"
                                            class="form-control @error('devolucion_dias_limite') is-invalid @enderror"
                                            value="{{ old('devolucion_dias_limite', $configs['devolucion_dias_limite']?->valor ?? '30') }}"
                                            min="1" step="1">
                                        <span class="input-group-text"
                                            style="background:var(--bg-elevated); border-color:var(--border);
                                             color:var(--text-muted);">días</span>
                                    </div>
                                    @error('devolucion_dias_limite')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Pasado este límite, requiere autorización de admin y no incluye ITBIS.
                                    </small>
                                </div>

                                <div class="col-md-6 mb-0">
                                    <label class="form-label">
                                        Vigencia de Nota de Crédito <span style="color:var(--accent);">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="nota_credito_dias_vencimiento"
                                            class="form-control @error('nota_credito_dias_vencimiento') is-invalid @enderror"
                                            value="{{ old('nota_credito_dias_vencimiento', $configs['nota_credito_dias_vencimiento']?->valor ?? '90') }}"
                                            min="1" step="1">
                                        <span class="input-group-text"
                                            style="background:var(--bg-elevated); border-color:var(--border);
                                             color:var(--text-muted);">días</span>
                                    </div>
                                    @error('nota_credito_dias_vencimiento')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Crédito --}}
                    <div class="card page-card mb-4">
                        <div class="card-body p-4">
                            <p class="prod-section-title">Crédito</p>

                            <div class="mb-0" style="max-width:220px;">
                                <label class="form-label">
                                    Días de plazo <span style="color:var(--accent);">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="credito_dias_vencimiento"
                                        class="form-control @error('credito_dias_vencimiento') is-invalid @enderror"
                                        value="{{ old('credito_dias_vencimiento', $configs['credito_dias_vencimiento']?->valor ?? '30') }}"
                                        min="1" step="1">
                                    <span class="input-group-text"
                                        style="background:var(--bg-elevated); border-color:var(--border); color:var(--text-muted);">días</span>
                                </div>
                                @error('credito_dias_vencimiento')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    Plazo por defecto para nuevas cuentas por cobrar.
                                </small>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- ═══════════════ TAB: Caja ═══════════════ --}}
                <div class="config-tab-panel" data-panel="caja" style="display:none;">

                    {{-- Caja --}}
                    <div class="card page-card mb-4">
                        <div class="card-body p-4">
                            <p class="prod-section-title">Caja</p>

                            <div class="mb-3" style="max-width:220px;">
                                <label class="form-label">
                                    Fondo mínimo de apertura <span style="color:var(--accent);">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"
                                        style="background:var(--bg-elevated); border-color:var(--border);
                                         color:var(--text-muted);">RD$</span>
                                    <input type="number" name="caja_monto_minimo_apertura"
                                        class="form-control @error('caja_monto_minimo_apertura') is-invalid @enderror"
                                        value="{{ old('caja_monto_minimo_apertura', $configs['caja_monto_minimo_apertura']?->valor ?? '500') }}"
                                        min="0" step="0.01">
                                </div>
                                @error('caja_monto_minimo_apertura')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    Exigido al admin al abrir una sesión de caja.
                                </small>
                            </div>

                        </div>
                    </div>

                    {{-- Horario --}}
                    <div class="card page-card mb-4">
                        <div class="card-body p-4">
                            <p class="prod-section-title">Horario de la tienda</p>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Horario de apertura <span style="color:var(--accent);">*</span>
                                    </label>
                                    <input type="time" name="horario_apertura"
                                        class="form-control @error('horario_apertura') is-invalid @enderror"
                                        value="{{ old('horario_apertura', $configs['horario_apertura']?->valor ?? '09:00') }}">
                                    @error('horario_apertura')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Horario de cierre <span style="color:var(--accent);">*</span>
                                    </label>
                                    <input type="time" name="horario_cierre"
                                        class="form-control @error('horario_cierre') is-invalid @enderror"
                                        value="{{ old('horario_cierre', $configs['horario_cierre']?->valor ?? '19:00') }}">
                                    @error('horario_cierre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <small class="text-muted">
                                El horario de cierre activa el recordatorio de cierre de caja en el punto de venta.
                            </small>

                        </div>
                    </div>

                    {{-- Caja Chica --}}
                    <div class="card page-card mb-4">
                        <div class="card-body p-4">
                            <p class="prod-section-title">Caja Chica</p>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Monto base <span style="color:var(--accent);">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"
                                            style="background:var(--bg-elevated); border-color:var(--border); color:var(--text-muted);">RD$</span>
                                        <input type="number" name="caja_chica_monto_base"
                                            class="form-control @error('caja_chica_monto_base') is-invalid @enderror"
                                            value="{{ old('caja_chica_monto_base', $configs['caja_chica_monto_base']?->valor ?? '2000') }}"
                                            min="0" step="0.01">
                                    </div>
                                    @error('caja_chica_monto_base')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Nivel al que se repone la caja chica en una reposición normal.
                                    </small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Días entre reposiciones <span style="color:var(--accent);">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="caja_chica_dias_reposicion"
                                            class="form-control @error('caja_chica_dias_reposicion') is-invalid @enderror"
                                            value="{{ old('caja_chica_dias_reposicion', $configs['caja_chica_dias_reposicion']?->valor ?? '1') }}"
                                            min="1" step="1">
                                        <span class="input-group-text"
                                            style="background:var(--bg-elevated); border-color:var(--border); color:var(--text-muted);">días</span>
                                    </div>
                                    @error('caja_chica_dias_reposicion')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Cada cuántos días de calendario se permite una reposición normal.
                                    </small>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- ═══════════════ TAB: Personal ═══════════════ --}}
                <div class="config-tab-panel" data-panel="personal" style="display:none;">

                    {{-- Nómina --}}
                    <div class="card page-card mb-4">
                        <div class="card-body p-4">
                            <p class="prod-section-title">Nómina</p>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ciclo de pago <span
                                            style="color:var(--accent);">*</span></label>
                                    <select name="nomina_ciclo"
                                        class="form-select @error('nomina_ciclo') is-invalid @enderror">
                                        <option value="mensual"
                                            {{ old('nomina_ciclo', $configs['nomina_ciclo']?->valor ?? 'mensual') === 'mensual' ? 'selected' : '' }}>
                                            Mensual</option>
                                        <option value="quincenal"
                                            {{ old('nomina_ciclo', $configs['nomina_ciclo']?->valor ?? '') === 'quincenal' ? 'selected' : '' }}>
                                            Quincenal</option>
                                    </select>
                                    @error('nomina_ciclo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Días de pago <span
                                            style="color:var(--accent);">*</span></label>
                                    <input type="text" name="nomina_dias_pago"
                                        class="form-control @error('nomina_dias_pago') is-invalid @enderror"
                                        value="{{ old('nomina_dias_pago', $configs['nomina_dias_pago']?->valor ?? '30') }}"
                                        placeholder="Ej: 30 o 15,30">
                                    @error('nomina_dias_pago')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Días del mes separados por coma si es quincenal.</small>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>

        {{-- Barra de guardado fija --}}
        <div class="config-save-bar">
            <p class="mb-0" style="font-size:12.5px; color:var(--text-muted);">
                <i class="bi bi-info-circle me-1"></i>
                Los cambios aplican inmediatamente a operaciones nuevas.
            </p>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Guardar cambios
            </button>
        </div>

    </form>
@endsection

@push('styles')
    @vite(['resources/css/usuarios.css'])
@endpush

@push('styles')
    <style>
        .config-save-bar {
            position: fixed;
            bottom: 0;
            left: var(--sidebar-w);
            right: 0;
            background: #ffffff;
            border-top: 1px solid var(--border);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 150;
            transition: left var(--transition);
        }

        .em-layout.sidebar-collapsed .config-save-bar {
            left: var(--sidebar-w-col);
        }

        @media (max-width: 1024px) {
            .config-save-bar {
                left: 0;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.querySelectorAll('.tab-config').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-config').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const tab = this.dataset.tab;
                document.querySelectorAll('.config-tab-panel').forEach(panel => {
                    panel.style.display = panel.dataset.panel === tab ? 'block' : 'none';
                });
            });
        });

        // Si hay errores de validación, abre automáticamente la pestaña que los contiene
        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                const primerError = document.querySelector('.is-invalid');
                if (primerError) {
                    const panel = primerError.closest('.config-tab-panel');
                    if (panel) {
                        const tabName = panel.dataset.panel;
                        document.querySelector(`.tab-config[data-tab="${tabName}"]`)?.click();
                    }
                }
            });
        @endif
    </script>
@endpush
