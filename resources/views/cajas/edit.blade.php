@extends('layouts.app')

@section('page_title', 'Editar Caja')
@section('page_subtitle', $caja->nombre)

@section('content')
    <div class="row g-4 justify-content-center">
        <div class="col-lg-6">
            <form action="{{ route('cajas.update', $caja) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card page-card mb-4">
                    <div class="card-body p-4">

                        <div class="mb-3">
                            <label class="form-label">
                                Nombre <span style="color:var(--accent);">*</span>
                            </label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                value="{{ old('nombre', $caja->nombre) }}">
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
                                        {{ old('sucursal_id', $caja->sucursal_id) == $sucursal->id ? 'selected' : '' }}>
                                        {{ $sucursal->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sucursal_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($caja->almacen_id)
                            <div class="mb-0">
                                <span class="field-label">Almacén asignado actualmente</span>
                                <div class="field-readonly">{{ $caja->almacen?->nombre }}</div>
                            </div>
                            <p class="text-muted mt-2 mb-0" style="font-size:12px;">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Si cambias la sucursal, este almacén se reasignará automáticamente
                                en la próxima apertura de sesión.
                            </p>
                        @endif

                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Guardar cambios
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
