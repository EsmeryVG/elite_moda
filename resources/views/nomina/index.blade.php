@extends('layouts.app')

@section('content')
    <div class="card page-card w-100 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="mb-1 fw-semibold">Nómina</h5>
                    <p class="text-muted mb-0" style="font-size:13px;">
                        Período actual: {{ $periodoInicio->format('d/m/Y') }} — {{ $periodoFin->format('d/m/Y') }}
                    </p>
                </div>

                @if ($yaExiste)
                    <span class="badge-nomina-pagada">
                        <i class="bi bi-check2 me-1"></i> Nómina de este período ya generada
                    </span>
                @else
                    <form action="{{ route('nomina.generar') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary"
                            onclick="return confirm('¿Generar la nómina de este período?')">
                            <i class="bi bi-cash-stack me-1"></i> Generar nómina del período actual
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="card page-card w-100">
        <div class="card-body p-4">
            <h6 class="fw-semibold mb-4">Historial de nóminas</h6>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Período</th>
                            <th style="width:110px;">Fecha de pago</th>
                            <th class="text-end" style="width:130px;">Total general</th>
                            <th style="width:100px;">Estado</th>
                            <th style="width:70px;" class="text-end">Ver</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nominas as $nomina)
                            <tr>
                                <td style="font-size:13px;">
                                    {{ $nomina->periodo_inicio->format('d/m/Y') }} —
                                    {{ $nomina->periodo_fin->format('d/m/Y') }}
                                </td>
                                <td style="font-size:12px; color:var(--text-muted);">
                                    {{ $nomina->fecha_pago->format('d/m/Y') }}
                                </td>
                                <td class="text-end fw-semibold" style="font-size:13px;">
                                    RD$ {{ number_format($nomina->total_general, 2) }}
                                </td>
                                <td>
                                    <span
                                        class="{{ $nomina->estado === 'pagada' ? 'badge-nomina-pagada' : 'badge-nomina-pendiente' }}">
                                        {{ ucfirst($nomina->estado) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('nomina.show', $nomina) }}" class="btn btn-outline-info btn-sm"
                                        title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5" style="color:var(--text-muted);">
                                    <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                                    No hay nóminas generadas todavía.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($nominas->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $nominas->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/nomina.css'])
@endpush
