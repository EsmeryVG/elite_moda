@extends('layouts.app')

@section('page_title', 'Editar — ' . $tipo_pago->nombre)
@section('page_subtitle', 'Modifica el tipo de pago')

@section('content')
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Información</p>

                <form action="{{ route('tipos_pago.update', $tipo_pago) }}"
                      method="POST" id="formTipoPago">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">
                            Nombre <span style="color:var(--accent);">*</span>
                        </label>
                        <input type="text" name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $tipo_pago->nombre) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card page-card">
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <button type="submit" form="formTipoPago" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Guardar cambios
                    </button>
                    <a href="{{ route('tipos_pago.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/usuarios.css'])
@endpush