@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
            Reporte de Materiales
        </h2>
        <div>
            <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Encabezado estilo PDF -->
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px; font-weight: bold;">A</div>
                    <div>
                        <h1 class="display-6" style="color: #d9534f; font-size: 24px; margin:0;">CONSTRUCTORA ADASHA</h1>
                        <h5 class="text-muted" style="margin:0;">Reporte de Materiales</h5>
                    </div>
                </div>
                <div class="text-end text-muted small">
                    <div>Fecha: <strong>{{ now()->format('d/m/Y H:i') }}</strong></div>
                    <div>Reporte #{{ $plano->id }}</div>
                </div>
            </div>

            <!-- Información del proyecto -->
            <div class="bg-light p-3 rounded mb-4 d-flex flex-wrap gap-3">
                <div class="flex-fill">
                    <strong class="text-uppercase text-muted small">Proyecto</strong>
                    <div class="fw-bold">{{ $proyecto->nombre ?? 'Sin proyecto' }}</div>
                </div>
                <div class="flex-fill">
                    <strong class="text-uppercase text-muted small">Cliente</strong>
                    <div class="fw-bold">{{ $proyecto->cliente ?? 'N/A' }}</div>
                </div>
                <div class="flex-fill">
                    <strong class="text-uppercase text-muted small">Ubicación</strong>
                    <div class="fw-bold">{{ $proyecto->ubicacion ?? 'N/A' }}</div>
                </div>
                <div class="flex-fill">
                    <strong class="text-uppercase text-muted small">Plano</strong>
                    <div class="fw-bold">{{ $plano->nombre }}</div>
                </div>
                @if($plano->longitud_total)
                <div class="flex-fill">
                    <strong class="text-uppercase text-muted small">Longitud total</strong>
                    <div class="fw-bold">{{ number_format($plano->longitud_total, 2, ',', '.') }} m</div>
                </div>
                @endif
            </div>

            <!-- Tabla -->
            <h5 class="border-bottom pb-2 mb-3">Materiales Calculados</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="8%">#</th>
                            <th width="35%">Material</th>
                            <th width="25%" class="text-end">Cantidad</th>
                            <th width="20%">Unidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($calculos as $index => $calculo)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $calculo->material->nombre ?? 'N/A' }}</td>
                            <td class="text-end fw-bold text-danger">
                                @if(str_contains($calculo->material->nombre ?? '', 'Ladrillo') || str_contains($calculo->material->nombre ?? '', 'Cemento'))
                                    {{ number_format($calculo->cantidad, 0, ',', '.') }}
                                @else
                                    {{ number_format($calculo->cantidad, 2, ',', '.') }}
                                @endif
                            </td>
                            <td>{{ $calculo->material->unidad ?? 'unidad' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-3">No hay materiales calculados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Total -->
            <div class="bg-light p-2 rounded text-end fw-bold mt-3">
                Total de materiales: <span class="text-danger">{{ $calculos->count() }}</span>
            </div>
        </div>
        <div class="card-footer text-muted text-center small">
            Reporte generado automáticamente por el sistema
        </div>
    </div>
</div>
@endsection