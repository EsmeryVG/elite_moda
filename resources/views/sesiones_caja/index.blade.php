@extends('layouts.app')

@section('content')
    <div class="card page-card w-100">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1 fw-semibold">Sesiones de Caja</h5>
                    <p class="text-muted mb-0" style="font-size:13px;">
                        Historial de aperturas y cierres de caja
                    </p>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap mb-4">
                <button class="btn btn-sm em-filtro {{ !request('estado') ? 'active' : '' }}" data-estado="">Todas</button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'abierta' ? 'active' : '' }}"
                    data-estado="abierta">Abiertas</button>
                <button class="btn btn-sm em-filtro {{ request('estado') === 'cerrada' ? 'active' : '' }}"
                    data-estado="cerrada">Cerradas</button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Caja</th>
                            <th>Responsable</th>
                            <th style="width:150px;">Apertura</th>
                            <th style="width:150px;">Cierre</th>
                            <th style="text-align:right; width:110px;">Esperado</th>
                            <th style="text-align:right; width:110px;">Diferencia</th>
                            <th style="width:100px;">Estado</th>
                            <th style="width:80px;" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaContainer" data-url="{{ route('sesiones_caja.tabla') }}">
                        @include('sesiones_caja._tabla')
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
