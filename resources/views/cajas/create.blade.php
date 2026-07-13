@extends('layouts.app')

@section('page_title', 'Nueva Caja')
@section('page_subtitle', 'Registra una nueva caja física')

@section('content')
    <div class="row g-4 justify-content-center">
        <div class="col-lg-6">
            <form action="{{ route('cajas.store') }}" method="POST">
                @csrf

                <div class="card page-card mb-4">
                    <div class="card-body p-4">

                        <div class="mb-3">
                            <label class="form-label">
                                Nombre <span style="color:var(--accent);">*</span>
                            </label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                placeholder="Ej: Caja 1" value="{{ old('nombre') }}">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sucursal</label>
                            <select name="sucursal_id" class="form-select @error('sucursal_id') is-invalid @enderror">
                                <option value="">Sin asignar (usa sucursal principal)</option>
                                @foreach ($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}"
                                        {{ old('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                                        {{ $sucursal->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sucursal_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <p class="text-muted mb-0" style="font-size:12px;">
                            <i class="bi bi-info-circle me-1"></i>
                            El almacén de origen para las ventas de esta caja se asigna automáticamente
                            la primera vez que alguien abre una sesión en ella.
                        </p>

                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Crear caja
                    </button>
                    <a href="{{ route('cajas.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/caja.css'])
@endpush
