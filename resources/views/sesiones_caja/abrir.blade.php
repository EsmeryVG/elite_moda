@extends('layouts.app')

@section('page_title', 'Abrir Sesión de Caja')
@section('page_subtitle', 'Selecciona tu caja e indica el fondo inicial')

@section('content')
    <div class="row g-4 justify-content-center">
        <div class="col-lg-6">
            <form action="{{ route('sesiones_caja.abrir.store') }}" method="POST">
                @csrf

                <div class="card page-card mb-4">
                    <div class="card-body p-4">
                        <p class="prod-section-title mb-3">Selecciona tu caja</p>

                        @error('caja_id')
                            <div class="alert alert-danger rounded-3 mb-3" style="font-size:13px;">
                                {{ $message }}
                            </div>
                        @enderror

                        @forelse($cajasDisponibles as $caja)
                            <label class="caja-select-card d-flex align-items-start gap-2 mb-0">
                                <input type="radio" name="caja_id" value="{{ $caja->id }}" class="form-check-input"
                                    required {{ old('caja_id') == $caja->id ? 'checked' : '' }}>
                                <div>
                                    <div style="font-size:14px; font-weight:600;">{{ $caja->nombre }}</div>
                                    <div style="font-size:12px; color:var(--text-muted);">
                                        {{ $caja->sucursal?->nombre ?? 'Sin sucursal asignada' }}
                                    </div>
                                </div>
                            </label>
                        @empty
                            <p class="text-muted text-center py-4" style="font-size:13px;">
                                No hay cajas disponibles en este momento — todas están en uso o inactivas.
                            </p>
                        @endforelse
                    </div>
                </div>

                <div class="card page-card">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label">
                                Fondo de caja inicial <span style="color:var(--accent);">*</span>
                            </label>
                            <input type="number" name="monto_apertura" step="0.01" min="{{ $montoMinimo }}"
                                class="form-control @error('monto_apertura') is-invalid @enderror"
                                placeholder="Mínimo RD$ {{ number_format($montoMinimo, 2) }}"
                                value="{{ old('monto_apertura') }}">
                            @error('monto_apertura')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text" style="font-size:12px;">
                                Monto mínimo requerido: RD$ {{ number_format($montoMinimo, 2) }}
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-unlock me-1"></i> Abrir sesión de caja
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/caja.css'])
@endpush

@push('scripts')
    @vite(['resources/js/caja.js'])
@endpush
