@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
            Reportes Disponibles
        </h2>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">
                <i class="bi bi-list-ul"></i>
                Planos con cálculos de materiales
            </h5>
        </div>
        <div class="card-body">
            @if($planos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Proyecto</th>
                                <th>Cliente</th>
                                <th>Plano</th>
                                <th>Longitud</th>
                                <th>Fecha de cálculo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($planos as $index => $plano)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $plano->proyecto->nombre ?? 'Sin proyecto' }}</td>
                                    <td>{{ $plano->proyecto->cliente ?? 'N/A' }}</td>
                                    <td>
                                        <i class="bi bi-file-earmark-image-fill text-info"></i>
                                        {{ $plano->nombre }}
                                    </td>
                                    <td>
                                        @if($plano->longitud_total)
                                            {{ number_format($plano->longitud_total, 2) }} m
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $plano->updated_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('reportes.materiales', $plano->id) }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-eye-fill"></i> Ver Reporte
                                        </a>
                                        <a href="{{ route('reportes.descargar', $plano->id) }}" class="btn btn-success btn-sm">
                                            <i class="bi bi-download"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-file-earmark-x text-muted" style="font-size: 48px;"></i>
                    <p class="mt-3">No hay reportes disponibles.</p>
                    <p class="text-muted">Primero debes analizar y calcular los materiales de un plano.</p>
                    <a href="{{ route('planos.index') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Ir a Planos
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection