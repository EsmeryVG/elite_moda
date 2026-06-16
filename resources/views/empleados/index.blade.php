@extends('layouts.app')

@section('content')
<div class="card page-card w-100">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-1 fw-semibold">Empleados</h5>
                <p class="text-muted mb-0" style="font-size:13px;">
                    {{ $empleados->total() }} empleados registrados
                </p>
            </div>
            <a href="{{ route('empleados.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Nuevo empleado
            </a>
        </div>

        <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">
            <div style="position:relative; flex:1; max-width:300px;">
                <i class="bi bi-search"
                   style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                          color:var(--text-muted); font-size:13px; pointer-events:none;"></i>
                <input type="text" id="buscadorEmpleados" class="form-control"
                       placeholder="Buscar por nombre, cédula o cargo..."
                       style="padding-left:36px;"
                       value="{{ request('buscar') }}" autocomplete="off">
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-sm em-filtro {{ !request('estado') ? 'active' : '' }}"
                        data-estado="">Todos</button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'activos' ? 'active' : '' }}"
                        data-estado="activos">Activos</button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'inactivos' ? 'active' : '' }}"
                        data-estado="inactivos">Inactivos</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width:100px;">Código</th>
                        <th>Nombre</th>
                        <th style="width:130px;">Cargo</th>
                        <th style="width:130px;">Teléfono</th>
                        <th style="width:160px;">Usuario</th>
                        <th style="width:100px;">Estado</th>
                        <th style="width:100px;" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaContainer" data-url="{{ route('empleados.index') }}">
                    @include('empleados._tabla')
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/usuarios.css'])
@endpush

@push('scripts')
    @vite(['resources/js/empleados.js'])
@endpush