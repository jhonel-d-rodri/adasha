@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted small mb-1">Proyecto</p>
            <h2 class="page-title mb-0">{{ $proyecto->nombre }}</h2>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('proyectos.index') }}"><i class="bi bi-arrow-left me-2"></i>Volver</a>
            <a class="btn btn-primary" href="{{ route('proyectos.edit', $proyecto) }}"><i class="bi bi-pencil-square me-2"></i>Editar</a>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-4"><div class="card h-100"><div class="card-header">Información del proyecto</div><div class="card-body"><dl class="mb-0 row"><dt class="col-5">Cliente</dt><dd class="col-7">{{ $proyecto->cliente ?: '—' }}</dd><dt class="col-5">Ubicación</dt><dd class="col-7">{{ $proyecto->ubicacion ?: '—' }}</dd><dt class="col-5">Inicio</dt><dd class="col-7">{{ $proyecto->fecha_inicio?->format('d/m/Y') ?? '—' }}</dd><dt class="col-5">Finalización</dt><dd class="col-7">{{ $proyecto->fecha_finalizacion?->format('d/m/Y') ?? '—' }}</dd><dt class="col-5">Estado</dt><dd class="col-7"><span class="badge bg-primary">{{ str_replace('_', ' ', $proyecto->estado_actual) }}</span></dd></dl></div></div></div>
        <div class="col-lg-8"><div class="card"><div class="card-header d-flex justify-content-between"><span>Planos del proyecto</span><span class="badge bg-primary">{{ $proyecto->planos->count() }}</span></div><div class="list-group list-group-flush">@forelse($proyecto->planos as $plano)<a href="{{ route('planos.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"><span><i class="bi bi-file-earmark-image text-primary me-2"></i>{{ $plano->nombre }}</span><span class="text-muted small">{{ $plano->estaAnalizado() ? number_format($plano->longitud_total, 2).' m analizados' : 'Pendiente de análisis' }}</span></a>@empty<div class="p-5 text-center text-muted">Este proyecto aún no tiene planos.</div>@endforelse</div></div></div>
    </div>
</div>
@endsection
