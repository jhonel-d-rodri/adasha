@extends('layouts.app')

@section('content')
<div class="container-fluid reports-page">
    <div class="app-page-header mb-4">
        <div><span class="app-eyebrow"><i class="bi bi-file-earmark-bar-graph"></i> Documentación</span><h2 class="page-title mb-1">
            <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
            Reportes de materiales
        </h2><p class="text-muted mb-0">Visualiza y descarga los cálculos disponibles por plano.</p></div>
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

    <div class="card reports-card">
        <div class="card-header reports-card-header">
            <h5 class="mb-0">
                <i class="bi bi-list-ul"></i>
                Planos con resultados calculados
            </h5>
        </div>
        <div class="card-body">
            @if($planos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 reports-table">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Proyecto</th>
                                <th>Cliente</th>
                                <th>Plano</th>
                                <th>Longitud</th>
                                <th>Fecha</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($planos as $index => $plano)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <span class="fw-semibold">{{ $plano->proyecto->nombre ?? 'Sin proyecto' }}</span>
                                    </td>
                                    <td>{{ $plano->proyecto->cliente ?? 'N/A' }}</td>
                                    <td>
                                        <span class="d-inline-flex align-items-center gap-2">
                                            <i class="bi bi-file-earmark-image-fill text-info"></i>
                                            {{ $plano->nombre }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($plano->longitud_total)
                                            <span class="badge bg-light text-dark border">
                                                {{ number_format($plano->longitud_total, 2) }} m
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $plano->updated_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                                            <a href="{{ route('reportes.materiales', $plano->id) }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye-fill"></i> Ver
                                            </a>
                                            <a href="{{ route('reportes.descargar', $plano->id) }}" class="btn btn-success btn-sm">
                                                <i class="bi bi-download"></i> PDF
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-x text-muted" style="font-size: 52px;"></i>
                    <p class="mt-3 mb-2 fw-semibold">No hay reportes disponibles.</p>
                    <p class="text-muted">Primero debes analizar y calcular los materiales de un plano.</p>
                    <a href="{{ route('planos.index') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-circle"></i> Ir a Planos
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
