@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
            Reporte de Materiales
        </h2>
        <div>
            <a href="{{ route('reportes.descargar', $plano->id) }}" class="btn btn-danger">
                <i class="bi bi-download"></i> Descargar PDF
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white">
            <div class="text-center">
                <h1 class="display-6" style="color: #d9534f;">CONSTRUCTORA ADASHA</h1>
                <h4>Reporte de Materiales</h4>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Proyecto:</strong> {{ $proyecto->nombre ?? 'Sin proyecto' }}</p>
                    <p><strong>Cliente:</strong> {{ $proyecto->cliente ?? 'N/A' }}</p>
                    <p><strong>Ubicación:</strong> {{ $proyecto->ubicacion ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Plano:</strong> {{ $plano->nombre }}</p>
                    @if($plano->longitud_total)
                        <p><strong>Longitud total:</strong> {{ number_format($plano->longitud_total, 2) }} m</p>
                    @endif
                    <p><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <h5 class="mb-3">Materiales Calculados</h5>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Material</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($calculos as $index => $calculo)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="material-name">{{ $calculo->material->nombre ?? 'N/A' }}</td>
                                <td>
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
                                <td colspan="4" class="text-center">No hay materiales calculados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted text-center">
            Reporte generado automáticamente por el sistema
        </div>
    </div>
</div>
@endsection

