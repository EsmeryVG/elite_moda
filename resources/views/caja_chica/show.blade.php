@extends('layouts.app')

@section('page_title', 'Caja Chica')
@section('page_subtitle', $cajaChica->sucursal?->nombre ?? 'Fondo operativo')

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <div class="cajachica-saldo-box">
                        <div class="cajachica-saldo-label">Saldo disponible</div>
                        <div
                            class="cajachica-saldo-monto {{ $cajaChica->monto_disponible < $cajaChica->monto_base ? 'bajo' : '' }}">
                            RD$ {{ number_format($cajaChica->monto_disponible, 2) }}
                        </div>
                        <div class="cajachica-saldo-base">
                            Base: RD$ {{ number_format($cajaChica->monto_base, 2) }}
                        </div>
                    </div>

                    @permiso('caja_chica.gestionar')
                        @if ($cajaChica->puedeReponerNormal())
                            <form action="{{ route('caja_chica.reponer') }}" method="POST" class="mt-3">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100 btn-sm">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Reestablecer saldo
                                </button>
                            </form>
                        @else
                            <p class="text-muted text-center mt-3 mb-0" style="font-size:12px;">
                                Ya se realizó la reposición normal disponible por ahora.
                            </p>
                        @endif

                        <button type="button" class="btn btn-outline-danger w-100 btn-sm mt-2" data-bs-toggle="modal"
                            data-bs-target="#modalExtraordinaria">
                            <i class="bi bi-exclamation-triangle me-1"></i> Reposición extraordinaria
                        </button>
                    @endpermiso
                </div>
            </div>

            <div class="card page-card">
                <div class="card-body p-4">
                    <p class="prod-section-title mb-3">Registrar gasto</p>
                    @permiso('caja_chica.gestionar')
                        <form action="{{ route('caja_chica.gasto') }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <input type="text" name="nombre"
                                    class="form-control form-control-sm @error('nombre') is-invalid @enderror"
                                    placeholder="Concepto del gasto" value="{{ old('nombre') }}">
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <select id="selectCategoriaGastoCC" name="categoria_gasto_id"
                                    class="@error('categoria_gasto_id') is-invalid @enderror">
                                    @foreach ($categorias ?? [] as $categoria)
                                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('categoria_gasto_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">RD$</span>
                                    <input type="number" name="monto" step="0.01" min="0.01"
                                        class="form-control @error('monto') is-invalid @enderror" value="{{ old('monto') }}">
                                </div>
                                @error('monto')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-sm">
                                <i class="bi bi-dash-circle me-1"></i> Registrar gasto
                            </button>
                        </form>
                    @else
                        <p class="text-muted text-center" style="font-size:12px;">
                            No tienes permiso para registrar gastos de caja chica.
                        </p>
                    @endpermiso
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card page-card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Movimientos recientes</h6>
                    @forelse($movimientos as $mov)
                        <div class="cc-movimiento-item">
                            <div>
                                <div style="font-weight:500;">{{ $mov->concepto }}</div>
                                <div style="font-size:11.5px; color:var(--text-muted);">
                                    {{ $mov->usuario?->name ?? '—' }} · {{ $mov->fecha->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="cc-movimiento-monto {{ $mov->tipo }}">
                                {{ $mov->tipo === 'ingreso' ? '+' : '-' }}RD$ {{ number_format($mov->monto, 2) }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4" style="font-size:13px;">
                            No hay movimientos registrados todavía.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Modal reposición extraordinaria --}}
    @permiso('caja_chica.gestionar')
        <div class="modal fade" id="modalExtraordinaria" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('caja_chica.reponer_extraordinaria') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h6 class="modal-title fw-semibold">Reposición extraordinaria</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Monto a reponer <span style="color:var(--accent);">*</span></label>
                                <input type="number" name="monto" step="0.01" min="0.01" class="form-control"
                                    required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Motivo <span style="color:var(--accent);">*</span></label>
                                <textarea name="observaciones" class="form-control" rows="2" required
                                    placeholder="Explica por qué se necesita fuera del ciclo normal"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Confirmar reposición</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endpermiso
@endsection

@push('styles')
    @vite(['resources/css/gastos.css'])
@endpush

@push('scripts')
    @vite(['resources/js/gastos.js'])
@endpush
