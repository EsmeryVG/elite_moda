@extends('layouts.app')

@section('page_title', 'Nuevo Almacén')
@section('page_subtitle', 'Registra un nuevo almacén')

@section('content')
<div class="row g-4">

    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Información del almacén</p>

                <form action="{{ route('almacenes.store') }}"
                      method="POST" id="formAlmacen">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">
                            Nombre <span style="color:var(--accent);">*</span>
                        </label>
                        <input type="text" name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre') }}"
                               placeholder="Ej: Almacén Principal"
                               autofocus>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Tipo <span style="color:var(--accent);">*</span>
                        </label>
                        <select name="tipo"
                                class="form-select @error('tipo') is-invalid @enderror">
                            <option value="principal"
                                    {{ old('tipo', 'principal') === 'principal' ? 'selected' : '' }}>
                                Principal
                            </option>
                            <option value="secundario"
                                    {{ old('tipo') === 'secundario' ? 'selected' : '' }}>
                                Secundario
                            </option>
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sucursal</label>
                        <select name="sucursal_id"
                                class="form-select @error('sucursal_id') is-invalid @enderror">
                            <option value="">Sin sucursal asignada</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}"
                                        {{ old('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                                    {{ $sucursal->nombre }}
                                    @if($sucursal->es_principal) (Principal) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('sucursal_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion"
                               class="form-control"
                               value="{{ old('direccion') }}"
                               placeholder="Dirección del almacén">
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="text-muted mb-4" style="font-size:12.5px; line-height:1.6;">
                    El almacén quedará <strong>activo</strong> al crearse.
                </p>
                <div class="d-grid gap-2">
                    <button type="submit" form="formAlmacen" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Crear almacén
                    </button>
                    <a href="{{ route('almacenes.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
    @vite(['resources/css/almacenes.css'])
@endpush