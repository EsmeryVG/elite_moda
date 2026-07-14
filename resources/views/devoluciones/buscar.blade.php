@extends('layouts.app')

@section('page_title', 'Buscar Factura para Devolución')
@section('page_subtitle', 'Cuando el cliente no trae la factura física')

@section('content')
    <div class="row g-4">

        <div class="col-lg-4">
            <div class="card page-card">
                <div class="card-body p-4">
                    <p class="prod-section-title mb-3">Búsqueda</p>

                    <form action="{{ route('devoluciones.buscar') }}" method="GET" id="formBuscarFactura">
                        <div class="mb-3">
                            <label class="form-label">Cliente <span style="color:var(--accent);">*</span></label>
                            <select id="selectClienteBusqueda" name="cliente_id" placeholder="Buscar cliente..." required>
                                @if (request('cliente_id'))
                                    @php $clienteSel = $clientes->firstWhere('id', (int) request('cliente_id')) @endphp
                                    @if ($clienteSel)
                                        <option value="{{ $clienteSel->id }}" selected>
                                            {{ $clienteSel->nombre }} {{ $clienteSel->apellido }}
                                        </option>
                                    @endif
                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Producto <span style="color:var(--accent);">*</span></label>
                            <select id="selectProductoBusqueda" name="variante_id"
                                placeholder="Primero selecciona un cliente..." required>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Buscar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card page-card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-4">Resultados</h6>

                    @if (request('cliente_id') && request('variante_id'))
                        @forelse($resultados as $resultado)
                            <div class="linea-devolucion-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div style="font-size:13px; font-weight:600;">
                                            {{ $resultado['venta']->codigo }}
                                        </div>
                                        <div style="font-size:12px; color:var(--text-muted);">
                                            {{ $resultado['venta']->fecha->format('d/m/Y') }}
                                            · {{ $resultado['detalle']->variante?->producto?->nombre }}
                                            · Disponible: {{ $resultado['cantidad_disponible'] }}
                                        </div>
                                    </div>
                                    <a href="{{ route('devoluciones.create', $resultado['venta']) }}"
                                        class="btn btn-primary btn-sm">
                                        Devolver de esta factura
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-5" style="font-size:13px;">
                                No se encontraron facturas de ese cliente con ese producto
                                dentro del plazo de devolución permitido.
                            </p>
                        @endforelse
                    @else
                        <p class="text-muted text-center py-5" style="font-size:13px;">
                            Selecciona un cliente y un producto para buscar.
                        </p>
                    @endif

                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    @vite(['resources/css/devoluciones.css'])
@endpush

@push('scripts')
    @vite(['resources/js/devoluciones.js'])
@endpush
