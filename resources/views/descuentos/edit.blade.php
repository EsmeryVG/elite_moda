@extends('layouts.app')

@section('page_title', 'Editar — ' . $descuento->nombre)
@section('page_subtitle', 'Modifica el descuento')

@section('content')
<form action="{{ route('descuentos.update', $descuento) }}" method="POST" id="formDescuento">
@csrf @method('PUT')

<input type="hidden" name="aplica_a" id="aplicaAInput" value="{{ $descuento->aplica_a }}">

<div class="row g-4">

    <div class="col-lg-7">
        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <p class="prod-section-title">¿A qué aplica este descuento?</p>

                <div class="tipo-aplicacion-toggle mb-4">
                    <button type="button" class="tipo-aplicacion-btn {{ $descuento->aplica_a === 'producto' ? 'active' : '' }}" data-aplica-a="producto">
                        <i class="bi bi-box me-1"></i> Producto
                    </button>
                    <button type="button" class="tipo-aplicacion-btn {{ $descuento->aplica_a === 'cliente' ? 'active' : '' }}" data-aplica-a="cliente">
                        <i class="bi bi-person me-1"></i> Cliente
                    </button>
                    <button type="button" class="tipo-aplicacion-btn {{ $descuento->aplica_a === 'grupo_cliente' ? 'active' : '' }}" data-aplica-a="grupo_cliente">
                        <i class="bi bi-people me-1"></i> Grupo de cliente
                    </button>
                </div>

                <div id="campo-producto" class="campo-aplicacion mb-3"
                     style="display:{{ $descuento->aplica_a === 'producto' ? 'block' : 'none' }};">
                    <label class="form-label">Variantes</label>
                    <select id="selectVariantes" name="variante_ids[]" multiple
                            placeholder="Buscar productos o variantes...">
                        @foreach($descuento->variantes as $variante)
                            <option value="{{ $variante->id }}" selected>
                                {{ $variante->producto?->nombre }} — {{ $variante->codigo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="campo-cliente" class="campo-aplicacion mb-3"
                     style="display:{{ $descuento->aplica_a === 'cliente' ? 'block' : 'none' }};">
                    <label class="form-label">Cliente</label>
                    <select name="cliente_id" class="form-select">
                        <option value="">Selecciona un cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}"
                                    {{ $descuento->cliente_id == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }} {{ $cliente->apellido }}
                                — {{ $cliente->cedula ?? $cliente->rnc }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="campo-grupo_cliente" class="campo-aplicacion mb-3"
                     style="display:{{ $descuento->aplica_a === 'grupo_cliente' ? 'block' : 'none' }};">
                    <label class="form-label">Grupos de cliente</label>
                    @foreach($grupos as $grupo)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="grupo_ids[]" value="{{ $grupo->id }}"
                                   id="grupo-{{ $grupo->id }}"
                                   {{ $descuento->gruposCliente->pluck('id')->contains($grupo->id) ? 'checked' : '' }}>
                            <label class="form-check-label" for="grupo-{{ $grupo->id }}"
                                   style="font-size:13px;">
                                {{ $grupo->nombre }}
                            </label>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>

        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Detalle del descuento</p>

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control"
                           value="{{ old('nombre', $descuento->nombre) }}" required>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select">
                            <option value="porcentaje" {{ $descuento->tipo === 'porcentaje' ? 'selected' : '' }}>
                                Porcentaje (%)
                            </option>
                            <option value="monto_fijo" {{ $descuento->tipo === 'monto_fijo' ? 'selected' : '' }}>
                                Monto fijo (RD$)
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Valor</label>
                        <input type="number" name="valor" class="form-control"
                               value="{{ old('valor', $descuento->valor) }}"
                               min="0" step="0.01" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control"
                               value="{{ old('fecha_inicio', $descuento->fecha_inicio?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha fin</label>
                        <input type="date" name="fecha_fin" class="form-control"
                               value="{{ old('fecha_fin', $descuento->fecha_fin?->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox"
                           name="requiere_autorizacion" value="1" id="requiereAutorizacion"
                           {{ $descuento->requiere_autorizacion ? 'checked' : '' }}>
                    <label class="form-check-label" for="requiereAutorizacion" style="font-size:13px;">
                        Requiere autorización de administrador
                    </label>
                </div>

            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card page-card">
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Guardar cambios
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