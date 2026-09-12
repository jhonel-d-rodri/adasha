@extends('layouts.app')

@section('content')

<div class="dashboard-page">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="page-title mb-1">
                Dashboard
            </h2>

            <p class="text-muted small mb-0">
                Bienvenido de vuelta,
                {{ Auth::user()->name ?? 'Administrador' }}
            </p>
        </div>

        <div class="d-flex gap-2">

            <button
                type="button"
                class="btn btn-outline-primary btn-sm"
                onclick="window.location.reload()">

                <i class="bi bi-arrow-clockwise"></i>
                Actualizar

            </button>

        </div>

    </div>


    <!-- =====================================================
         TARJETAS DE ESTADÍSTICAS
    ====================================================== -->

    <div class="row g-4 mb-4">

        <!-- PROYECTOS -->
        <div class="col-md-3 col-sm-6">

            <div class="card dashboard-card stats-card stats-card-primary">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <h6 class="card-subtitle text-muted mb-1">
                                Total Proyectos
                            </h6>

                            <h2 class="card-title mb-0">
                                {{ $proyectosCount ?? 0 }}
                            </h2>

                            <div class="stats-trend mt-2">

                                <span class="badge bg-primary">
                                    <i class="bi bi-folder"></i>
                                    Registrados
                                </span>

                            </div>

                        </div>

                        <div class="stats-icon">

                            <i class="bi bi-folder-fill"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- PLANOS -->
        <div class="col-md-3 col-sm-6">

            <div class="card dashboard-card stats-card stats-card-info">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <h6 class="card-subtitle text-muted mb-1">
                                Planos Registrados
                            </h6>

                            <h2 class="card-title mb-0">
                                {{ $planosCount ?? 0 }}
                            </h2>

                            <div class="stats-trend mt-2">

                                <span class="badge bg-info text-white">
                                    <i class="bi bi-file-earmark-image"></i>
                                    Planos
                                </span>

                            </div>

                        </div>

                        <div class="stats-icon">

                            <i class="bi bi-file-earmark-image-fill"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- MATERIALES -->
        <div class="col-md-3 col-sm-6">

            <div class="card dashboard-card stats-card stats-card-warning">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <h6 class="card-subtitle text-muted mb-1">
                                Materiales Registrados
                            </h6>

                            <h2 class="card-title mb-0">
                                {{ $materialesCount ?? 0 }}
                            </h2>

                            <div class="stats-trend mt-2">

                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-box-seam"></i>
                                    Inventario
                                </span>

                            </div>

                        </div>

                        <div class="stats-icon">

                            <i class="bi bi-box-seam-fill"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- REPORTES -->
        <div class="col-md-3 col-sm-6">

            <div class="card dashboard-card stats-card stats-card-success">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <h6 class="card-subtitle text-muted mb-1">
                                Reportes Generados
                            </h6>

                            <h2 class="card-title mb-0">
                                {{ $reportesCount ?? 0 }}
                            </h2>

                            <div class="stats-trend mt-2">

                                <span class="badge bg-success">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                    Reportes
                                </span>

                            </div>

                        </div>

                        <div class="stats-icon">

                            <i class="bi bi-file-earmark-pdf-fill"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         GRÁFICOS Y PROYECTOS RECIENTES
    ====================================================== -->

    <div class="row g-4">

        <!-- GRÁFICO -->
        <div class="col-lg-8">

            <div class="card dashboard-card">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <h6 class="mb-0">

                        <i class="bi bi-graph-up me-2"></i>

                        Actividad Reciente

                    </h6>

                </div>

                <div class="card-body">

                    <div style="height: 280px;">

                        <canvas id="activityChart"></canvas>

                    </div>

                </div>

            </div>

        </div>


        <!-- PROYECTOS RECIENTES -->
        <div class="col-lg-4">

            <div class="card dashboard-card">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <h6 class="mb-0">

                        <i class="bi bi-clock-history me-2"></i>

                        Proyectos Recientes

                    </h6>

                    <a
                        href="{{ route('proyectos.index') }}"
                        class="btn btn-sm btn-link">

                        Ver todos

                    </a>

                </div>


                <div class="card-body p-0">

                    <div class="list-group list-group-flush">

                        @forelse($proyectosRecientes ?? [] as $proyecto)

                            <a
                                href="{{ route('proyectos.show', $proyecto->id) }}"
                                class="list-group-item list-group-item-action">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>

                                        <i class="bi bi-folder-fill me-2 text-primary"></i>

                                        <span class="fw-semibold">
                                            {{ $proyecto->nombre }}
                                        </span>

                                    </div>

                                    <i class="bi bi-chevron-right text-muted"></i>

                                </div>

                                @if(!empty($proyecto->estado))

                                    <small class="text-muted ms-4">

                                        Estado:
                                        {{ ucfirst(str_replace('_', ' ', $proyecto->estado)) }}

                                    </small>

                                @endif

                            </a>

                        @empty

                            <div class="list-group-item text-center text-muted py-4">

                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                                No hay proyectos recientes

                            </div>

                        @endforelse

                    </div>

                </div>

            </div>

        </div>


        <!-- =====================================================
             INVENTARIO DE MATERIALES
        ====================================================== -->

        <div class="col-12">

            <div class="card dashboard-card">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <h6 class="mb-0">

                        <i class="bi bi-box-seam me-2"></i>

                        Inventario de Materiales

                    </h6>

                    <a
                        href="{{ route('materials.index') }}"
                        class="btn btn-sm btn-primary">

                        <i class="bi bi-plus"></i>

                        Agregar

                    </a>

                </div>


                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover mb-0">

                            <thead>

                                <tr>

                                    <th>Material</th>

                                    <th>Unidad</th>

                                    <th>Stock</th>

                                    <th>Estado</th>

                                    <th>Acciones</th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($materialesRecientes ?? [] as $material)

                                    @php

                                        $stock = (float) ($material->stock ?? 0);

                                        if ($stock <= 0) {

                                            $estado = 'Sin stock';
                                            $badgeClass = 'danger';

                                        } elseif ($stock <= 10) {

                                            $estado = 'Stock bajo';
                                            $badgeClass = 'warning';

                                        } else {

                                            $estado = 'Disponible';
                                            $badgeClass = 'success';

                                        }

                                    @endphp


                                    <tr>

                                        <!-- MATERIAL -->
                                        <td>

                                            <div class="d-flex align-items-center">

                                                <div class="material-icon me-2">

                                                    <i class="bi bi-box"></i>

                                                </div>

                                                <div>

                                                    <div class="fw-semibold">

                                                        {{ $material->nombre }}

                                                    </div>

                                                    @if(!empty($material->descripcion))

                                                        <small class="text-muted">

                                                            {{ Str::limit($material->descripcion, 40) }}

                                                        </small>

                                                    @endif

                                                </div>

                                            </div>

                                        </td>


                                        <!-- UNIDAD -->
                                        <td>

                                            {{ $material->unidad }}

                                        </td>


                                        <!-- STOCK -->
                                        <td>

                                            <span class="fw-semibold">

                                                {{ number_format($stock, 2, ',', '.') }}

                                            </span>

                                        </td>


                                        <!-- ESTADO -->
                                        <td>

                                            <span class="badge bg-{{ $badgeClass }}-subtle text-{{ $badgeClass }} rounded-pill">

                                                <i class="bi bi-circle-fill me-1 small"></i>

                                                {{ $estado }}

                                            </span>

                                        </td>


                                        <!-- ACCIONES -->
                                        <td>

                                            <div class="btn-group btn-group-sm">

                                                <a
                                                    href="{{ route('materials.edit', $material->id) }}"
                                                    class="btn btn-outline-primary"
                                                    title="Editar">

                                                    <i class="bi bi-pencil"></i>

                                                </a>

                                                <form
                                                    action="{{ route('materials.destroy', $material->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('¿Está seguro de eliminar este material?');">

                                                    @csrf

                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="btn btn-outline-danger"
                                                        title="Eliminar">

                                                        <i class="bi bi-trash"></i>

                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td
                                            colspan="5"
                                            class="text-center py-5">

                                            <i class="bi bi-box-seam fs-1 d-block mb-3 text-muted"></i>

                                            <span class="text-muted">

                                                No hay materiales registrados.

                                            </span>

                                            <br>

                                            <a
                                                href="{{ route('materials.index') }}"
                                                class="btn btn-primary btn-sm mt-3">

                                                <i class="bi bi-plus"></i>

                                                Registrar material

                                            </a>

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- =========================================================
     GRÁFICO
========================================================= -->

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@php
    $datosActividad = $actividad ?? [0, 0, 0, 0, 0, 0, 0];
@endphp

<script>

document.addEventListener('DOMContentLoaded', function () {

    const canvas = document.getElementById('activityChart');

    if (!canvas) {
        return;
    }

    const ctx = canvas.getContext('2d');

    const gradient = ctx.createLinearGradient(0, 0, 0, 280);

    gradient.addColorStop(0, 'rgba(37, 99, 235, 0.20)');
    gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

    new Chart(ctx, {

        type: 'line',

        data: {

            labels: [
                'Lun',
                'Mar',
                'Mié',
                'Jue',
                'Vie',
                'Sáb',
                'Dom'
            ],

            datasets: [{

                label: 'Actividad',

                data: @json($datosActividad),

                borderColor: '#2563EB',

                backgroundColor: gradient,

                borderWidth: 3,

                fill: true,

                tension: 0.4,

                pointBackgroundColor: '#2563EB',

                pointBorderColor: '#fff',

                pointBorderWidth: 2,

                pointRadius: 4,

                pointHoverRadius: 6

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {
                    display: false
                }

            },

            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {
                        precision: 0
                    },

                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }

                },

                x: {

                    grid: {
                        display: false
                    }

                }

            },

            interaction: {

                intersect: false,

                mode: 'index'

            }

        }

    });

});

</script>

@endpush


<!-- =========================================================
     ESTILOS
========================================================= -->

<style>

.dashboard-page .page-title {
    font-weight: 700;
    color: #0F172A;
}


.dashboard-page .stats-card {
    border: none;
    border-radius: 16px;
    transition: all 0.3s ease;
    overflow: hidden;
}


.dashboard-page .stats-card .card-body {
    padding: 24px;
}


.dashboard-page .stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
}


.dashboard-page .stats-card .card-subtitle {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}


.dashboard-page .stats-card .card-title {
    font-size: 36px;
    font-weight: 700;
    margin: 4px 0;
}


.dashboard-page .stats-card .stats-icon {

    width: 52px;
    height: 52px;

    border-radius: 12px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 24px;

    opacity: 0.8;
}


/* TARJETA PROYECTOS */

.dashboard-page .stats-card-primary {

    background: linear-gradient(
        135deg,
        #ffffff 0%,
        #f0f7ff 100%
    );

    border-left: 4px solid #2563EB;
}


.dashboard-page .stats-card-primary .stats-icon {

    background: rgba(37, 99, 235, 0.12);

    color: #2563EB;
}


/* TARJETA PLANOS */

.dashboard-page .stats-card-info {

    background: linear-gradient(
        135deg,
        #ffffff 0%,
        #ecfeff 100%
    );

    border-left: 4px solid #06B6D4;
}


.dashboard-page .stats-card-info .stats-icon {

    background: rgba(6, 182, 212, 0.12);

    color: #06B6D4;
}


/* TARJETA MATERIALES */

.dashboard-page .stats-card-warning {

    background: linear-gradient(
        135deg,
        #ffffff 0%,
        #fffbeb 100%
    );

    border-left: 4px solid #F59E0B;
}


.dashboard-page .stats-card-warning .stats-icon {

    background: rgba(245, 158, 11, 0.12);

    color: #F59E0B;
}


/* TARJETA REPORTES */

.dashboard-page .stats-card-success {

    background: linear-gradient(
        135deg,
        #ffffff 0%,
        #f0fdf4 100%
    );

    border-left: 4px solid #10B981;
}


.dashboard-page .stats-card-success .stats-icon {

    background: rgba(16, 185, 129, 0.12);

    color: #10B981;
}


/* BADGES */

.dashboard-page .stats-trend .badge {

    font-size: 11px;

    padding: 5px 9px;

    border-radius: 20px;
}


/* TABLA */

.dashboard-page .table th {

    font-weight: 600;

    font-size: 12px;

    text-transform: uppercase;

    letter-spacing: 0.3px;

    color: #64748B;

    border-bottom: 2px solid #E2E8F0;

    padding: 14px 16px;
}


.dashboard-page .table td {

    vertical-align: middle;

    padding: 14px 16px;
}


/* ICONO MATERIAL */

.dashboard-page .material-icon {

    width: 36px;

    height: 36px;

    background: #F1F5F9;

    border-radius: 8px;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #2563EB;
}


/* ESTADOS */

.dashboard-page .bg-success-subtle {

    background: #D1FAE5;

    color: #065F46;
}


.dashboard-page .bg-warning-subtle {

    background: #FEF3C7;

    color: #92400E;
}


.dashboard-page .bg-danger-subtle {

    background: #FEE2E2;

    color: #991B1B;
}


/* LISTA DE PROYECTOS */

.dashboard-page .list-group-item {

    padding: 14px 16px;

    border-left: none;

    border-right: none;
}


.dashboard-page .list-group-item:first-child {
    border-top: none;
}


.dashboard-page .list-group-item:hover {

    background-color: #F8FAFC;
}


/* RESPONSIVE */

@media (max-width: 768px) {

    .dashboard-page .stats-card .card-title {

        font-size: 28px;

    }

    .dashboard-page .stats-card .stats-icon {

        width: 40px;

        height: 40px;

        font-size: 18px;

    }

}

</style>

@endsection