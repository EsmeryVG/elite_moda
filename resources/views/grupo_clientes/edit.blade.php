@extends('layouts.app')

@section('page_title', 'Editar — ' . $grupo_cliente->nombre)
@section('page_subtitle', 'Modifica los datos del grupo')

@section('content')
<div class="row g-4">

    <div class="col-lg-7">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Información del grupo</p>

                <form action="{{ route('grupo_clientes.update', $grupo_cliente) }}"
                      method="POST" id="formGrupo">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">
                            Nombre <span style="color:var(--accent);">*</span>
                        </label>
                        <input type="text" name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $grupo_cliente->nombre) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descuento base (%)</label>
                        <div class="input-group">
                            <input type="number" name="descuento_base"
                                   class="form-control @error('descuento_base') is-invalid @enderror"
                                   value="{{ old('descuento_base', $grupo_cliente->descuento_base) }}"
                                   placeholder="0" step="0.01" min="0" max="100">
                            <span class="input-group-text"
                                  style="background:var(--bg-elevated);
                                         border-color:var(--border);
                                         color:var(--text-muted);">%</span>
                        </div>
                        @error('descuento_base')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <input type="text" name="descripcion"
                               class="form-control"
                               value="{{ old('descripcion', $grupo_cliente->descripcion) }}"
                               placeholder="Descripción opcional del grupo">
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5 d-flex flex-column gap-4">

        <div class="card page-card">
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <button type="submit" form="formGrupo" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Guardar cambios
                    </button>
                    <a href="{{ route('grupo_clientes.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>

        <div class="card page-card">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-2">Clientes en este grupo</h6>
                <p style="font-size:28px; font-weight:700; color:var(--text-primary); margin:0;">
                    {{ $grupo_cliente->clientes()->count() }}
                </p>
                <p style="font-size:12px; color:var(--text-muted); margin:0;">
                    clientes asociados
                </p>
            </div>
        </div>

    </div>

</div>
@endsection

@push('styles')
    @vite(['resources/css/clientes.css'])
@endpush