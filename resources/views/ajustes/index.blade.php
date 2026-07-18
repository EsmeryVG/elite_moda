@extends('layouts.app')

@section('content')
    <div class="card page-card w-100">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1 fw-semibold">Ajustes de inventario</h5>
                    <p class="text-muted mb-0" style="font-size:13px;">
                        {{ $ajustes->total() }} ajustes registrados
                    </p>
                </div>
                @permiso('inventario.gestionar')
                    <a href="{{ route('ajustes.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Nuevo ajuste
                    </a>
                @endpermiso
            </div>

            <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">

                <div style="position:relative; flex:1; max-width:280px;">
                    <i class="bi bi-search"
                        style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                          color:var(--text-muted); font-size:13px; pointer-events:none;"></i>
                    <input type="text" id="buscadorAjustes" class="form-control" placeholder="Buscar por motivo..."
                        style="padding-left:36px;" value="{{ request('buscar') }}" autocomplete="off">
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm em-filtro {{ !request('estado') ? 'active' : '' }}"
                        data-estado="">Todos</button>
                    <button class="btn btn-sm em-filtro {{ request('estado') === 'pendiente' ? 'active' : '' }}"
                        data-estado="pendiente">Pendiente</button>
                    <button class="btn btn-sm em-filtro {{ request('estado') === 'aprobado' ? 'active' : '' }}"
                        data-estado="aprobado">Aprobado</button>
                    <button class="btn btn-sm em-filtro {{ request('estado') === 'rechazado' ? 'active' : '' }}"
                        data-estado="rechazado">Rechazado</button>
                </div>

            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width:100px;">Fecha</th>
                            <th>Almacén</th>
                            <th style="width:130px;">Tipo</th>
                            <th>Motivo</th>
                            <th style="width:130px;">Registrado por</th>
                            <th style="width:100px;">Estado</th>
                            <th style="width:80px;" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaContainer" data-url="{{ route('ajustes.index') }}">
                        @include('ajustes._tabla')
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/ajustes.css'])
@endpush

@push('scripts')
    @vite(['resources/js/ajustes.js'])
@endpush
