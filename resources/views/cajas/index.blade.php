@extends('layouts.app')

@section('content')
    <div class="card page-card w-100">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1 fw-semibold">Cajas</h5>
                    <p class="text-muted mb-0" style="font-size:13px;">
                        {{ $cajas->total() }} cajas registradas
                    </p>
                </div>
                <a href="{{ route('cajas.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Nueva caja
                </a>
            </div>

            <div class="d-flex gap-2 flex-wrap mb-4">
                <button class="btn btn-sm em-filtro {{ !request('estado') ? 'active' : '' }}" data-estado="">Todas</button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'activa' ? 'active' : '' }}"
                    data-estado="activa">Activas</button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'inactiva' ? 'active' : '' }}"
                    data-estado="inactiva">Inactivas</button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Sucursal</th>
                            <th>Almacén asignado</th>
                            <th style="width:100px;">Estado</th>
                            <th style="width:110px;" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaContainer" data-url="{{ route('cajas.index') }}">
                        @include('cajas._tabla')
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/caja.css'])
@endpush

@push('scripts')
    @vite(['resources/js/caja.js'])
@endpush
