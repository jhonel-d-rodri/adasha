@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')

<div class="container-fluid">

    {{-- Título --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>
            <i class="bi bi-file-earmark-image-fill text-primary"></i>
            Gestión de Planos
        </h2>

        <div>
            <a
                href="{{ route('dashboard') }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left"></i>
                Volver
            </a>
        </div>

    </div>


    {{-- Mensaje de éxito --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle-fill"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- Mensaje de error --}}
    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-exclamation-triangle-fill"></i>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- Errores de validación --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ===================================================== --}}
    {{-- PLANOS REGISTRADOS --}}
    {{-- ===================================================== --}}

    <div class="card shadow">

        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">

            <h5 class="mb-0">

                <i class="bi bi-list-ul"></i>

                Planos Registrados

            </h5>

            <span class="badge bg-light text-dark">

                {{ $planos->count() }}

            </span>

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th style="width: 60px;">
                                #
                            </th>

                            <th style="width: 200px;">
                                Proyecto
                            </th>

                            <th>
                                Plano
                            </th>

                            <th style="width: 100px;">
                                Tipo
                            </th>

                            <th style="width: 150px;">
                                Fecha
                            </th>

                            <th style="width: 320px;">
                                Acciones
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @forelse($planos as $plano)

                        <tr>

                            {{-- ID --}}
                            <td class="text-center">

                                {{ $plano->id }}

                            </td>


                            {{-- PROYECTO --}}
                            <td>

                                <i class="bi bi-folder-fill text-primary"></i>

                                {{ $plano->proyecto->nombre ?? 'Sin proyecto' }}

                            </td>


                            {{-- PLANO --}}
                            <td>

                                <i class="bi bi-file-earmark-image-fill text-info"></i>

                                {{ $plano->nombre }}

                                @if($plano->estaAnalizado())

                                    <span class="badge bg-success ms-1">

                                        <i class="bi bi-rulers"></i>

                                        {{ number_format($plano->longitud_total, 2) }} m

                                    </span>

                                @endif

                                <br>

                                <small class="text-muted">

                                    {{ $plano->nombre_archivo }}

                                </small>

                            </td>


                            {{-- TIPO --}}
                            <td class="text-center">

                                @php

                                    $extension = strtolower(
                                        pathinfo(
                                            $plano->nombre_archivo,
                                            PATHINFO_EXTENSION
                                        )
                                    );

                                    $badgeClass = in_array(
                                        $extension,
                                        ['jpg', 'jpeg', 'png']
                                    )
                                    ? 'bg-primary'
                                    : 'bg-secondary';

                                @endphp

                                <span class="badge {{ $badgeClass }}">

                                    {{ strtoupper($extension) }}

                                </span>

                            </td>


                            {{-- FECHA --}}
                            <td>

                                <i class="bi bi-calendar3 text-muted"></i>

                                {{ $plano->created_at?->format('d/m/Y H:i') }}

                            </td>


                            {{-- ACCIONES --}}
                            <td>

                                <div class="d-flex flex-wrap gap-1">


                                    {{-- VER ARCHIVO --}}
                                    <a
                                        href="{{ route('planos.archivo', $plano) }}"
                                        target="_blank"
                                        class="btn btn-info btn-sm"
                                    >

                                        <i class="bi bi-eye-fill"></i>

                                        Ver

                                    </a>


                                    {{-- ANALIZAR --}}
                                    @if(
                                        in_array(
                                            $extension,
                                            ['jpg', 'jpeg', 'png']
                                        )
                                    )

                                        <a
                                            href="{{ route('planos.analizar', $plano->id) }}"
                                            class="btn btn-warning btn-sm"
                                        >

                                            <i class="bi bi-cpu-fill"></i>

                                            Analizar

                                        </a>


                                        {{-- CALCULAR MATERIALES --}}
                                        @if($plano->procesado)

                                            <a
                                                href="{{ route('planos.calcular-materiales', $plano->id) }}"
                                                class="btn btn-success btn-sm"
                                            >

                                                <i class="bi bi-calculator-fill"></i>

                                                Calcular

                                            </a>

                                        @else

                                            <button
                                                type="button"
                                                class="btn btn-secondary btn-sm"
                                                disabled
                                            >

                                                <i class="bi bi-calculator-fill"></i>

                                                Calcular Materiales

                                            </button>

                                        @endif

                                    @else

                                        <button
                                            type="button"
                                            class="btn btn-secondary btn-sm"
                                            disabled
                                        >

                                            <i class="bi bi-file-earmark-pdf-fill"></i>

                                            PDF

                                        </button>

                                    @endif


                                    {{-- ELIMINAR --}}
                                    <form
                                        action="{{ route('planos.destroy', $plano->id) }}"
                                        method="POST"
                                        style="display:inline;"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('¿Desea eliminar este plano?')"
                                        >

                                            <i class="bi bi-trash-fill"></i>

                                            Eliminar

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="text-center py-4"
                            >

                                <i
                                    class="bi bi-file-earmark-x text-muted"
                                    style="font-size:40px;"
                                ></i>

                                <p class="mt-2 mb-0 text-muted">

                                    No existen planos registrados.

                                </p>

                                <small class="text-muted">

                                    Sube un plano para comenzar a analizar.

                                </small>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>


                {{-- Paginación --}}
                @if(method_exists($planos, 'links'))

                    <div class="d-flex justify-content-end">

                        {{ $planos->links() }}

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>


{{-- Script --}}
@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    // Cerrar alertas después de 5 segundos

    setTimeout(function () {

        const alerts =
            document.querySelectorAll('.alert');

        alerts.forEach(function (alert) {

            const closeBtn =
                alert.querySelector('.btn-close');

            if (closeBtn) {

                closeBtn.click();

            }

        });

    }, 5000);

});

</script>

@endpush

@endsection