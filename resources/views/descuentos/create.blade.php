@extends('layouts.app')

@section('page_title', 'Nuevo Descuento')
@section('page_subtitle', 'Registra un nuevo descuento por producto, cliente o grupo')

@section('content')
<form action="{{ route('descuentos.store') }}" method="POST" id="formDescuento">
@csrf

<input type="hidden" name="aplica_a" id="aplicaAInput" value="producto">

<div class="row g-4">

    <div class="col-lg-7">
        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <p class="prod-section-title">¿A qué aplica este descuento?</p>

                <div class="tipo-aplicacion-toggle mb-4">
                    <button type="button" class="tipo-aplicacion-btn active" data-aplica-a="producto">
                        <i class="bi bi-box me-1"></i> Producto
                    </button>
                    <button type="button" class="tipo-aplicacion-btn" data-aplica-a="cliente">
                        <i class="bi bi-person me-1"></i> Cliente
                    </button>
                    <button type="button" class="tipo-aplicacion-btn" data-aplica-a="grupo_cliente">
                        <i class="bi bi-people me-1"></i> Grupo de cliente
                    </button>
                </div>

                {{-- Campo: Producto --}}
                <div id="campo-producto" class="campo-aplicacion mb-3">
                    <label class="form-label">
                        Variantes <span style="color:var(--accent);">*</span>
                    </label>
                    <select id="selectVariantes" name="variante_ids[]" multiple
                            placeholder="Buscar productos o variantes...">
                    </select>
                    @error('variante_ids')
                        <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Campo: Cliente --}}
                <div id="campo-cliente" class="campo-aplicacion mb-3" style="display:none;">
                    <label class="form-label">
                        Cliente <span style="color:var(--accent);">*</span>
                    </label>
                    <select name="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror">
                        <option value="">Selecciona un cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}"
                                    {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }} {{ $cliente->apellido }}
                                — {{ $cliente->cedula ?? $cliente->rnc }}
                            </option>
                        @endforeach
                    </select>
                    @error('cliente_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Campo: Grupo de cliente --}}
                <div id="campo-grupo_cliente" class="campo-aplicacion mb-3" style="display:none;">
                    <label class="form-label">
                        Grupos de cliente <span style="color:var(--accent);">*</span>
                    </label>
                    @foreach($grupos as $grupo)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="grupo_ids[]" value="{{ $grupo->id }}"
                                   id="grupo-{{ $grupo->id }}"
                                   {{ in_array($grupo->id, old('grupo_ids', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="grupo-{{ $grupo->id }}"
                                   style="font-size:13px;">
                                {{ $grupo->nombre }}
                            </label>
                        </div>
                    @endforeach
                    @error('grupo_ids')
                        <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Detalle del descuento</p>

                <div class="mb-3">
                    <label class="form-label">
                        Nombre <span style="color:var(--accent);">*</span>
                    </label>
                    <input type="text" name="nombre"
                           class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}"
                           placeholder="Ej: Promo Día de las Madres">
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            Tipo <span style="color:var(--accent);">*</span>
                        </label>
                        <select name="tipo" class="form-select">
                            <option value="porcentaje" {{ old('tipo', 'porcentaje') === 'porcentaje' ? 'selected' : '' }}>
                                Porcentaje (%)
                            </option>
                            <option value="monto_fijo" {{ old('tipo') === 'monto_fijo' ? 'selected' : '' }}>
                                Monto fijo (RD$)
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            Valor <span style="color:var(--accent);">*</span>
                        </label>
                        <input type="number" name="valor"
                               class="form-control @error('valor') is-invalid @enderror"
                               value="{{ old('valor') }}"
                               min="0" step="0.01" placeholder="Ej: 20">
                        @error('valor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control"
                               value="{{ old('fecha_inicio') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha fin</label>
                        <input type="date" name="fecha_fin" class="form-control"
                               value="{{ old('fecha_fin') }}">
                    </div>
                </div>

                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox"
                           name="requiere_autorizacion" value="1" id="requiereAutorizacion"
                           {{ old('requiere_autorizacion') ? 'checked' : '' }}>
                    <label class="form-check-label" for="requiereAutorizacion" style="font-size:13px;">
                        Requiere autorización de administrador para aplicarse
                    </label>
                </div>

            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="text-muted mb-4" style="font-size:12.5px; line-height:1.6;">
                    <i class="bi bi-info-circle me-1"></i>
                    Si dejas las fechas vacías, el descuento estará vigente indefinidamente
                    mientras esté activo.
                </p>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Crear descuento
                    </button>
                    <a href="{{ route('descuentos.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
</form>
@endsection

@push('styles')
    @vite(['resources/css/descuentos.css'])
@endpush

@push('scripts')
    @vite(['resources/js/descuentos.js'])
@endpush