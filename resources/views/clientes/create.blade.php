@extends('layouts.app')

@section('page_title', 'Nuevo Cliente')
@section('page_subtitle', 'Registra un nuevo cliente')

@section('content')
    <div class="row g-4">

        <div class="col-lg-8">
            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <p class="prod-section-title">Información personal</p>

                    <form action="{{ route('clientes.store') }}" method="POST" id="formCliente">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    Nombre <span style="color:var(--accent);">*</span>
                                </label>
                                <input type="text" name="nombre"
                                    class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}"
                                    placeholder="Nombre del cliente" autofocus>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="apellido" class="form-control" value="{{ old('apellido') }}"
                                    placeholder="Apellido del cliente">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Cédula</label>
                                <input type="text" name="cedula"
                                    class="form-control @error('cedula') is-invalid @enderror" value="{{ old('cedula') }}"
                                    placeholder="11 dígitos sin guiones" maxlength="11" inputmode="numeric">
                                @error('cedula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">11 dígitos sin guiones ni espacios.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">RNC</label>
                                <input type="text" name="rnc" class="form-control @error('rnc') is-invalid @enderror"
                                    value="{{ old('rnc') }}" placeholder="9 u 11 dígitos" maxlength="11"
                                    inputmode="numeric">
                                @error('rnc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Solo para clientes empresa.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono"
                                    class="form-control @error('telefono') is-invalid @enderror"
                                    value="{{ old('telefono') }}" placeholder="10 dígitos sin guiones" maxlength="10"
                                    inputmode="numeric">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                    placeholder="correo@ejemplo.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Dirección</label>
                                <input type="text" name="direccion" class="form-control" value="{{ old('direccion') }}"
                                    placeholder="Dirección del cliente">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Grupo de cliente</label>
                                <select name="grupo_cliente_id" class="form-select">
                                    <option value="">Sin grupo</option>
                                    @foreach ($grupos as $grupo)
                                        <option value="{{ $grupo->id }}"
                                            {{ old('grupo_cliente_id') == $grupo->id ? 'selected' : '' }}>
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
                                    value="{{ old('limite_credito', 0) }}" placeholder="0.00" step="0.01"
                                    min="0">
                                @error('limite_credito')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="credito_activo" class="form-check-input" id="creditoActivo"
                                    value="1" {{ old('credito_activo') ? 'checked' : '' }}>
                                <label class="form-check-label" for="creditoActivo">
                                    Activar crédito para este cliente
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card page-card">
                <div class="card-body p-4">
                    <p class="text-muted mb-4" style="font-size:12.5px; line-height:1.6;">
                        Se asignará el código <strong>CLI-001</strong> automáticamente
                        y quedará <strong>activo</strong>.
                    </p>
                    <div class="d-grid gap-2">
                        <button type="submit" form="formCliente" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Crear cliente
                        </button>
                        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    @vite(['resources/css/clientes.css'])
@endpush
