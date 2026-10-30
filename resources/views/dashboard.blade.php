@extends('layouts.app')

@section('content')

<div class="dashboard-page">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="page-title mb-1">
                Panel de control
            </h2>

            <p class="text-muted small mb-0">
                Bienvenido de vuelta,
                {{ Auth::user()->name ?? 'Administrador' }}
            </p>

        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('proyectos.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-folder-plus me-1"></i> Gestionar proyectos
            </a>

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

        <div class="col-xl-3 col-sm-6">

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

        <div class="col-xl-3 col-sm-6">

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

        <div class="col-xl-3 col-sm-6">

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

        <div class="col-xl-3 col-sm-6">

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

                    @if(collect($actividad ?? [])->contains(fn ($valor) => $valor > 0))
                    <p class="text-muted small mb-2">
                        Semana {{ $periodoActividad }}. Nuevos proyectos, planos y registros de cálculo de materiales por día.
                    </p>
                    <div style="height: 280px;">

                        <canvas id="activityChart"></canvas>

                    </div>
                    @else
                    <div class="dashboard-empty">
                        <i class="bi bi-graph-up-arrow" aria-hidden="true"></i>
                        <strong>Sin actividad para mostrar</strong>
                        <span>No se registraron proyectos, planos ni cálculos de materiales en la semana {{ $periodoActividad }}.</span>
                    </div>
                    @endif

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

                                        @switch($proyecto->estado_actual)
                                            @case('registrado') Comienzo @break
                                            @case('en_proceso') En proceso @break
                                            @case('finalizado') Finalizó @break
                                            @default {{ ucfirst(str_replace('_', ' ', $proyecto->estado_actual)) }}
                                        @endswitch

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

    </div>

</div>


<!-- =========================================================
     GRÁFICO
========================================================= -->

@push('scripts')

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

    gradient.addColorStop(
        0,
        'rgba(37, 99, 235, 0.20)'
    );

    gradient.addColorStop(
        1,
        'rgba(37, 99, 235, 0)'
    );


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

                label: 'Registros nuevos',

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


/* TARJETAS */

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
