@extends('layouts.app')

@section('page_title', 'Nuevo Grupo')
@section('page_subtitle', 'Crea un nuevo grupo de cliente')

@section('content')
<div class="row g-4">

    <div class="col-lg-7">
        <div class="card page-card">
            <div class="card-body p-4">
                <p class="prod-section-title">Información del grupo</p>

                <form action="{{ route('grupo_clientes.store') }}"
                      method="POST" id="formGrupo">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">
                            Nombre <span style="color:var(--accent);">*</span>
                        </label>
                        <input type="text" name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre') }}"
                               placeholder="Ej: VIP, Regular, Mayorista"
                               autofocus>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <input type="text" name="descripcion"
                               class="form-control"
                               value="{{ old('descripcion') }}"
                               placeholder="Descripción opcional del grupo">
                    </div>

                    <p class="text-muted mt-3" style="font-size:12px;">
                        <i class="bi bi-info-circle me-1"></i>
                        Los descuentos para este grupo se configuran desde el módulo
                        de <a href="{{ route('descuentos.index') }}">Descuentos</a>.
                    </p>

                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card page-card">
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <button type="submit" form="formGrupo" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Crear grupo
                    </button>
                    <a href="{{ route('grupo_clientes.index') }}" class="btn btn-secondary">
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