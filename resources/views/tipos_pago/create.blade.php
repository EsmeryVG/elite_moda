@extends('layouts.app')

@section('page_title', 'Nuevo Tipo de Pago')
@section('page_subtitle', 'Registra un nuevo método de pago')

@section('content')
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Información</p>

                <form action="{{ route('tipos_pago.store') }}" method="POST" id="formTipoPago">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">
                            Nombre <span style="color:var(--accent);">*</span>
                        </label>
                        <input type="text" name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre') }}"
                               placeholder="Ej: Efectivo, Tarjeta, Transferencia"
                               autofocus>
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
                        <i class="bi bi-check-circle me-1"></i> Crear tipo
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