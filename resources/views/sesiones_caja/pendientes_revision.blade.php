@extends('layouts.app')

@section('content')
    <div class="card page-card w-100">
        <div class="card-body p-4">

            <div class="mb-4">
                <h5 class="mb-1 fw-semibold">Diferencias pendientes de revisión</h5>
                <p class="text-muted mb-0" style="font-size:13px;">
                    Sesiones cerradas con descuadre que aún no han sido revisadas
                </p>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Caja</th>
                            <th>Cerrada por</th>
                            <th>Fecha cierre</th>
                            <th class="text-end">Diferencia</th>
                            <th>Observación</th>
                            <th style="width:120px;" class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sesiones as $sesion)
                            <tr>
                                <td style="font-size:13px;">{{ $sesion->caja?->nombre }}</td>
                                <td style="font-size:13px;">{{ $sesion->usuarioCierre?->name ?? '—' }}</td>
                                <td style="font-size:12px; color:var(--text-muted);">
                                    {{ $sesion->fecha_cierre?->format('d/m/Y H:i') }}
                                </td>
                                <td class="text-end">
                                    <span
                                        style="color:{{ $sesion->diferencia > 0 ? '#2e7d32' : '#c62828' }}; font-weight:700;">
                                        {{ $sesion->diferencia > 0 ? '+' : '' }}RD$
                                        {{ number_format($sesion->diferencia, 2) }}
                                    </span>
                                </td>
                                <td style="font-size:12.5px; color:var(--text-muted);">
                                    {{ $sesion->observacion_diferencia }}
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('sesiones_caja.marcar_revisada', $sesion) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-outline-success btn-sm"
                                            onclick="return confirm('¿Marcar esta diferencia como revisada?')">
                                            <i class="bi bi-check2"></i> Revisar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5" style="color:var(--text-muted);">
                                    <i class="bi bi-check-circle"
                                        style="font-size:28px; display:block; margin-bottom:8px;"></i>
                                    No hay diferencias pendientes de revisión.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/caja.css'])
@endpush
