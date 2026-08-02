@extends('layouts.app')

@section('page_title', 'Editar — ' . $cliente->nombre_completo)
@section('page_subtitle', 'Modifica los datos del cliente')

@section('content')
    <div class="row g-4">

        <div class="col-lg-8">

            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <p class="prod-section-title">Información personal</p>

                    <form action="{{ route('clientes.update', $cliente) }}" method="POST" id="formCliente">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Código</label>
                                <input type="text" class="form-control" value="{{ $cliente->codigo }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    Nombre <span style="color:var(--accent);">*</span>
                                </label>
                                <input type="text" name="nombre"
                                    class="form-control @error('nombre') is-invalid @enderror"
                                    value="{{ old('nombre', $cliente->nombre) }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="apellido" class="form-control"
                                    value="{{ old('apellido', $cliente->apellido) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Cédula</label>
                                <input type="text" name="cedula"
                                    class="form-control @error('cedula') is-invalid @enderror"
                                    value="{{ old('cedula', $cliente->cedula) }}" placeholder="11 dígitos sin guiones"
                                    maxlength="11" inputmode="numeric">
                                @error('cedula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">RNC</label>
                                <input type="text" name="rnc" class="form-control @error('rnc') is-invalid @enderror"
                                    value="{{ old('rnc', $cliente->rnc) }}" placeholder="9 u 11 dígitos" maxlength="11"
                                    inputmode="numeric">
                                @error('rnc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono"
                                    class="form-control @error('telefono') is-invalid @enderror"
                                    value="{{ old('telefono', $cliente->telefono) }}" placeholder="10 dígitos sin guiones"
                                    maxlength="10" inputmode="numeric">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $cliente->email) }}" placeholder="correo@ejemplo.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Dirección</label>
                                <input type="text" name="direccion" class="form-control"
                                    value="{{ old('direccion', $cliente->direccion) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Grupo de cliente</label>
                                <select name="grupo_cliente_id" class="form-select">
                                    <option value="">Sin grupo</option>
                                    @foreach ($grupos as $grupo)
                                        <option value="{{ $grupo->id }}"
                                            {{ old('grupo_cliente_id', $cliente->grupo_cliente_id) == $grupo->id ? 'selected' : '' }}>
                                            {{ $grupo->nombre }}
                                            @if ($grupo->descuento_base > 0)
                                                ({{ $grupo->descuento_base }}% desc.)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                </div>
            </div>

            <div class="card page-card">
                <div class="card-body p-4">
                    <p class="prod-section-title">Configuración de crédito</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Límite de crédito</label>
                            <div class="input-group">
                                <span class="input-group-text"
                                    style="background:var(--bg-elevated);
                                         border-color:var(--border);
                                         color:var(--text-muted); font-size:13px;">
                                    RD$
                                </span>
                                <input type="number" name="limite_credito"
                                    class="form-control @error('limite_credito') is-invalid @enderror"
                                    value="{{ old('limite_credito', $cliente->limite_credito) }}" placeholder="0.00"
                                    step="0.01" min="0">
                                @error('limite_credito')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="credito_activo" class="form-check-input" id="creditoActivo"
                                    value="1" {{ old('credito_activo', $cliente->credito_activo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="creditoActivo">
                                    Crédito activo
                                </label>
                            </div>
                        </div>
                        @if ($cliente->balance_credito > 0)
                            <div class="col-12">
                                <div class="alert alert-warning rounded-3 mb-0" style="font-size:13px;">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Este cliente tiene una deuda pendiente de
                                    <strong>RD$ {{ number_format($cliente->balance_credito, 2) }}</strong>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
            </form>
        </div>

        <div class="col-lg-4 d-flex flex-column gap-4">

            <div class="card page-card">
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <button type="submit" form="formCliente" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Guardar cambios
                        </button>
                        <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-secondary">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>

            <div class="card page-card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Estado</h6>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div style="font-size:13px; font-weight:500;">
                                {{ $cliente->estado ? 'Activo' : 'Inactivo' }}
                            </div>
                            <div style="font-size:12px; color:var(--text-muted);">
                                {{ $cliente->estado ? 'Puede realizar compras.' : 'No disponible.' }}
                            </div>
                        </div>
                        <span
                            style="width:10px; height:10px; border-radius:50%;
                                 background:{{ $cliente->estado ? '#4caf50' : '#9e9e9e' }};
                                 display:inline-block;"></span>
                    </div>

                    @if ($cliente->estado)
                        <form action="{{ route('clientes.destroy', $cliente) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100 btn-sm"
                                onclick="return confirm('¿Desactivar {{ $cliente->nombre_completo }}?')">
                                <i class="bi bi-toggle-on me-1"></i> Desactivar
                            </button>
                        </form>
                    @else
                        <form action="{{ route('clientes.reactivar', $cliente) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-outline-success w-100 btn-sm"
                                onclick="return confirm('¿Reactivar {{ $cliente->nombre_completo }}?')">
                                <i class="bi bi-toggle-off me-1"></i> Reactivar
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>

    </div>
@endsection

@push('styles')
    @vite(['resources/css/clientes.css'])
@endpush
