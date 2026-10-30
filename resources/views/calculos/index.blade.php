@extends('layouts.app')

@section('content')

<div class="container-fluid calculations-page">

    {{-- Título --}}
    <div class="app-page-header mb-4">

        <div><span class="app-eyebrow"><i class="bi bi-boxes"></i> Presupuesto orientativo</span><h2 class="page-title mb-1">
            <i class="bi bi-calculator-fill text-primary"></i>
            Cálculo de Materiales
        </h2><p class="text-muted mb-0">Resultados generados a partir del plano y la configuración de obra gruesa.</p></div>

        <a href="{{ route('dashboard') }}"
           class="btn btn-outline-secondary btn-sm">

            <i class="bi bi-arrow-left"></i>
            Volver

        </a>

    </div>

    {{-- Mensaje de éxito --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle-fill"></i>

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif

    {{-- Mensaje de error --}}
    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-exclamation-triangle-fill"></i>

            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif

    {{-- Información --}}
    <div class="card calculation-info-card mb-4">

        <div class="card-header calculation-info-header">

            <h5 class="mb-0">

                <i class="bi bi-info-circle-fill"></i>

                Cálculo automático de materiales

            </h5>

        </div>

        <div class="card-body">

            <p class="mb-0">

                <i class="bi bi-cpu-fill text-primary"></i>

                Los materiales se calculan automáticamente a partir
                del análisis del plano arquitectónico realizado con
                Python y OpenCV.

            </p>

        </div>

    </div>

    {{-- Resultados --}}
    <div class="card calculations-card">

        <div class="card-header calculations-card-header">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0">

                    <i class="bi bi-list-check"></i>

                    Resultados de Materiales

                    <span class="badge bg-light text-dark ms-2">
                        {{ $calculos->count() }}
                    </span>

                </h5>

                <div>

                    {{-- Botón Ver Reporte --}}
                    @if($calculos->count() > 0)

                        @php
                            $planoId = $calculos->first()->plano_id;
                        @endphp

                        

                    @endif

                </div>

            </div>

        </div>

        <div class="card-body">

            @if($calculos->count() > 0)

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle material-results-table">

                        <thead class="table-dark">

                            <tr>

                                <th>#</th>

                                <th>Proyecto</th>

                                <th>Plano</th>

                                <th>Material</th>

                                <th>Cantidad</th>

                                <th>Unidad</th>

                                <th>Acciones</th>

                            </tr>

                        </thead>

                        <tbody>

                        @forelse($calculos as $calculo)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    <i class="bi bi-folder-fill text-primary"></i>
                                    {{ $calculo->proyecto->nombre ?? 'Sin proyecto' }}
                                </td>

                                <td>
                                    <i class="bi bi-file-earmark-image-fill text-info"></i>
                                    {{ $calculo->plano->nombre ?? 'Sin plano' }}
                                </td>

                                <td>
                                    <span class="material-name">
                                        {{ $calculo->material->nombre ?? 'Sin material' }}
                                    </span>
                                </td>

                                <td>
                                    <strong>
                                        {{ number_format($calculo->cantidad, 2) }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $calculo->material->unidad ?? '-' }}
                                </td>

                                <td>
                                    <form
                                        action="{{ route('calculos.destroy', $calculo->id) }}"
                                        method="POST"
                                        style="display:inline;">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('¿Desea eliminar este cálculo?')">

                                            <i class="bi bi-trash-fill"></i>

                                        </button>

                                    </form>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    No existen cálculos registrados.
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- Resumen --}}
                <div class="alert alert-info mt-4">

                    <i class="bi bi-info-circle-fill"></i>

                    <strong>Resultado:</strong>

                    Se muestran los materiales calculados para el plano
                    analizado.

                    <br>

                    <strong>Total de materiales:</strong>

                    {{ $calculos->count() }}

                </div>

                {{-- Botón para ir a planos --}}
                <div class="text-center mt-3">
                    <a href="{{ route('planos.index') }}"
                       class="btn btn-primary btn-sm">

                        <i class="bi bi-file-earmark-image-fill"></i>

                        Ir a Planos

                    </a>
                </div>

            @else

                {{-- Sin resultados --}}
                <div class="text-center py-5">

                    <i class="bi bi-calculator text-muted"
                       style="font-size:60px;">
                    </i>

                    <h5 class="mt-3">

                        No existen cálculos registrados.

                    </h5>

                    <p class="text-muted">

                        Primero debe analizar un plano y después
                        seleccionar <strong>Calcular materiales</strong>.

                    </p>

                    <a href="{{ route('planos.index') }}"
                       class="btn btn-primary">

                        <i class="bi bi-file-earmark-image-fill"></i>

                        Ir a Planos

                    </a>

                </div>

            @endif

        </div>

    </div>

</div>

{{-- Cerrar mensajes automáticamente --}}
@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    setTimeout(function () {

        document.querySelectorAll('.alert').forEach(function (alert) {

            const closeBtn = alert.querySelector('.btn-close');

            if (closeBtn) {

                closeBtn.click();

            }

        });

    }, 5000);

});

</script>

@endpush

@endsection

