@extends('layouts.app')

@section('content')
    <div class="card page-card w-100">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-1 fw-semibold">Cuentas por Cobrar</h5>
                    <p class="text-muted mb-0" style="font-size:13px;">
                        {{ $cuentas->total() }} cuentas registradas
                    </p>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">

                <div style="position:relative; flex:1; max-width:280px;">
                    <i class="bi bi-search"
                        style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                          color:var(--text-muted); font-size:13px; pointer-events:none;"></i>
                    <input type="text" id="buscadorCuentasPorCobrar" class="form-control"
                        placeholder="Buscar por código o cliente..." style="padding-left:36px;"
                        value="{{ request('busqueda') }}" autocomplete="off">
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm em-filtro {{ !request('estado') ? 'active' : '' }}"
                        data-estado="">Todas</button>
                    <button class="btn btn-sm em-filtro {{ request('estado') === 'pendiente' ? 'active' : '' }}"
                        data-estado="pendiente">Pendiente</button>
                    <button class="btn btn-sm em-filtro {{ request('estado') === 'parcial' ? 'active' : '' }}"
                        data-estado="parcial">Parcial</button>
                    <button class="btn btn-sm em-filtro {{ request('estado') === 'pagada' ? 'active' : '' }}"
                        data-estado="pagada">Pagada</button>
                    <button class="btn btn-sm em-filtro {{ request('estado') === 'vencida' ? 'active' : '' }}"
                        data-estado="vencida">Vencida</button>
                </div>

            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width:110px;">Código</th>
                            <th style="width:100px;">Emisión</th>
                            <th style="width:100px;">Vence</th>
                            <th>Cliente</th>
                            <th>Venta</th>
                            <th style="text-align:right; width:110px;">Total</th>
                            <th style="text-align:right; width:110px;">Pendiente</th>
                            <th style="width:100px;">Estado</th>
                            <th style="width:70px;" class="text-end">Ver</th>
                        </tr>
                    </thead>
                    <tbody id="tablaContainer" data-url="{{ route('cuentas_por_cobrar.tabla') }}">
                        @include('cuentas_por_cobrar._tabla')
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/cuentas_por_cobrar.css'])
@endpush

@push('scripts')
    @vite(['resources/js/cuentas_por_cobrar.js'])
@endpush
