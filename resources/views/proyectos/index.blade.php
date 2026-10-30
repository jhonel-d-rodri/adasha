@extends('layouts.app')

@section('content')

{{-- ========================================================= --}}
{{-- LEAFLET --}}
{{-- ========================================================= --}}

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""
>


<style>
    #mapaProyecto {
    width: 100%;
    height: 520px;
    border-radius: 12px;
    border: 1px solid #dee2e6;
    z-index: 1;
    overflow: hidden;
}

    .geolocalizacion-panel {
        min-height: 620px;
    }

    .geolocalizacion-wrapper {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

/* Control de navegación */
.control-navegacion {
    position: relative;
    z-index: 1000 !important;
    pointer-events: auto !important;
    background: #ffffff;
    border: 2px solid #0d6efd;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.35);
    padding: 6px;
}

/* Mantiene los controles personalizados separados del borde inferior. */
.leaflet-bottom.leaflet-right .control-navegacion {
    margin: 0 12px 12px 0;
}

.control-navegacion button {
    width: 42px;
    height: 42px;
    border: 1px solid #b8c7db;
    background: #f8fbff;
    color: #0d6efd;
    cursor: pointer;
    font-size: 21px;
    font-weight: bold;
    display: block;
    margin: 2px;
    border-radius: 5px;
    pointer-events: auto !important;
}

.control-navegacion button:hover {
    background: #0d6efd;
    color: white;
}

.control-navegacion button:focus-visible {
    outline: 3px solid rgba(13, 110, 253, 0.35);
    outline-offset: 1px;
}

#mapaProyecto .leaflet-control-container,
#mapaProyecto .leaflet-bottom,
#mapaProyecto .leaflet-right {
    pointer-events: auto !important;
}

.control-navegacion .fila {
    display: flex;
    justify-content: center;
}

    .ubicacion-coordenadas {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
    }

    .btn-buscar-ubicacion {
        height: 38px;
    }
</style>


<div class="container-fluid projects-page">

    {{-- ========================================================= --}}
    {{-- ENCABEZADO --}}
    {{-- ========================================================= --}}

    <div class="app-page-header mb-4">

        <div><span class="app-eyebrow"><i class="bi bi-building"></i> Administración de obra</span><h2 class="page-title mb-1">
            <i class="bi bi-folder-fill text-primary"></i>
            Gestión de Proyectos
        </h2><p class="text-muted mb-0">Organiza proyectos, clientes, ubicación y planos asociados.</p></div>

        <button
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#nuevoProyecto">

            <i class="bi bi-plus-circle"></i>
            Nuevo Proyecto

        </button>

    </div>


    {{-- ========================================================= --}}
    {{-- MENSAJES --}}
    {{-- ========================================================= --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle-fill"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-exclamation-triangle-fill"></i>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- ERRORES DE VALIDACIÓN --}}
    {{-- ========================================================= --}}

    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- LISTA DE PROYECTOS --}}
    {{-- ========================================================= --}}

    <div class="card projects-card">

        <div class="card-header projects-card-header">

            <h5 class="mb-0">

                <i class="bi bi-list-ul"></i>

                Lista de Proyectos

            </h5>

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle projects-table">

                    <thead class="table-dark">

                        <tr>

                            <th>ID</th>

                            <th>Proyecto</th>

                            <th>Cliente</th>

                            <th>Ubicación</th>

                            <th>Inicio</th>

                            <th>Fin</th>

                            <th>Estado</th>

                            <th width="170">Acciones</th>

                        </tr>

                    </thead>


                    <tbody>

                    @forelse($proyectos as $proyecto)

                        <tr>

                            <td>
                                {{ $proyecto->id }}
                            </td>

                            <td>
                                {{ $proyecto->nombre }}
                            </td>

                            <td>
                                {{ $proyecto->cliente }}
                            </td>

                            <td>

                                <i class="bi bi-geo-alt-fill text-danger"></i>

                                {{ $proyecto->ubicacion }}

                                @if($proyecto->latitud && $proyecto->longitud)

                                    <br>

                                    <small class="text-muted">

                                        {{ number_format($proyecto->latitud, 7) }},
                                        {{ number_format($proyecto->longitud, 7) }}

                                    </small>

                                @endif

                            </td>

                            <td>
                                {{ $proyecto->fecha_inicio ?? '—' }}
                            </td>

                            <td>
                                {{ $proyecto->fecha_finalizacion ?? '—' }}
                            </td>


                            <td>

                                @if($proyecto->estado_actual == 'registrado')

                                    <span class="badge bg-success">
                                        Comienzo
                                    </span>

                                @elseif($proyecto->estado_actual == 'en_proceso')

                                    <span class="badge bg-warning text-dark">
                                        En proceso
                                    </span>

                                @elseif($proyecto->estado_actual == 'finalizado')

                                    <span class="badge bg-danger">
                                        Finalizó
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        {{ $proyecto->estado_actual }}
                                    </span>

                                @endif

                            </td>


                            <td>

                                <a
                                    href="{{ route('proyectos.edit', $proyecto->id) }}"
                                    class="btn btn-warning btn-sm">

                                    <i class="bi bi-pencil-fill"></i>

                                </a>


                                <form
                                    action="{{ route('proyectos.destroy', $proyecto->id) }}"
                                    method="POST"
                                    style="display:inline;">

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Desea eliminar este proyecto?')">

                                        <i class="bi bi-trash-fill"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="text-center">

                                No existen proyectos registrados.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- MODAL NUEVO PROYECTO --}}
{{-- ========================================================= --}}

<div
    class="modal fade"
    id="nuevoProyecto"
    tabindex="-1"
    aria-labelledby="nuevoProyectoLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content">


            {{-- ================================================= --}}
            {{-- FORMULARIO --}}
            {{-- ================================================= --}}

            <form
                action="{{ route('proyectos.store') }}"
                method="POST"
                enctype="multipart/form-data">

                @csrf
                <style>
                    /* =========================================================
   BARRA DE DESPLAZAMIENTO GENERAL
   ========================================================= */

/* Chrome, Edge y Brave */
::-webkit-scrollbar {
    width: 10px;
}

::-webkit-scrollbar-track {
    background: #e9eef3;
}

::-webkit-scrollbar-thumb {
    background: #08b6d9;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #079bb8;
}


/* Firefox */
html {
    scrollbar-width: thin;
    scrollbar-color: #08b6d9 #e9eef3;
}
                #nuevoProyecto .modal-body {
    max-height: 75vh;
    overflow-y: auto;
    overflow-x: auto;
    scrollbar-width: thin;
    scrollbar-color: #08b6d9 #e9eef3;
}

/* Barra de desplazamiento vertical y horizontal */
#nuevoProyecto .modal-body::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}

#nuevoProyecto .modal-body::-webkit-scrollbar-track {
    background: #e9eef3;
    border-radius: 10px;
}

#nuevoProyecto .modal-body::-webkit-scrollbar-thumb {
    background: #08b6d9;
    border-radius: 10px;
}

#nuevoProyecto .modal-body::-webkit-scrollbar-thumb:hover {
    background: #079bb8;
}
</style>


                {{-- ================================================= --}}
                {{-- HEADER --}}
                {{-- ================================================= --}}

                <div class="modal-header bg-primary text-white">

                    <h5
                        class="modal-title"
                        id="nuevoProyectoLabel">

                        <i class="bi bi-folder-plus"></i>

                        Nuevo Proyecto

                    </h5>


                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                {{-- ================================================= --}}
                {{-- BODY --}}
                {{-- ================================================= --}}

                <div class="modal-body">


                    {{-- ================================================= --}}
                    {{-- DATOS DEL PROYECTO --}}
                    {{-- ================================================= --}}

                    <h6 class="text-primary mb-3">

                        <i class="bi bi-info-circle"></i>

                        Datos del Proyecto

                    </h6>


                    {{-- PROYECTO / CLIENTE --}}

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Proyecto
                            </label>

                            <input
                                type="text"
                                name="nombre"
                                class="form-control"
                                placeholder="Ej.: Casa de 2 pisos"
                                value="{{ old('nombre') }}"
                                required>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Cliente
                            </label>

                            <input
                                type="text"
                                name="cliente"
                                class="form-control"
                                value="{{ old('cliente') }}"
                                required>

                        </div>

                    </div>


                    {{-- UBICACIÓN / FECHA --}}

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                <i class="bi bi-geo-alt-fill text-danger"></i>

                                Ubicación

                            </label>


                            <input
                                type="text"
                                name="ubicacion"
                                id="ubicacion"
                                class="form-control"
                                value="{{ old('ubicacion') }}"
                                placeholder="Ej.: Zona Sur, La Paz, Bolivia"
                                required>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Fecha Inicio
                            </label>

                            <input
                                type="date"
                                name="fecha_inicio"
                                class="form-control"
                                value="{{ old('fecha_inicio') }}"
                                required>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Fecha de finalización
                            </label>

                            <input
                                type="date"
                                name="fecha_finalizacion"
                                class="form-control"
                                value="{{ old('fecha_finalizacion') }}"
                                placeholder="Opcional">

                            <small class="text-muted">
                                Si se indica, el sistema cambiará automáticamente a Finalizó cuando llegue esa fecha.
                            </small>

                        </div>


                        {{-- DESCRIPCIÓN --}}

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Descripción
                            </label>

                            <textarea
                                name="descripcion"
                                class="form-control"
                                rows="3"
                                placeholder="Descripción del proyecto...">{{ old('descripcion') }}</textarea>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- ESTADO Y DESCRIPCIÓN LADO A LADO --}}
                    {{-- ================================================= --}}

                    <div class="row">

                        <div class="col-12 mb-3">

                            <div class="alert alert-light border">
                                <i class="bi bi-info-circle"></i>
                                El estado se calcula automáticamente: inicia como Comienzo, pasa a En proceso al llegar a la mitad del tiempo estimado y cambia a Finalizó cuando llegue la fecha de cierre.
                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- GEOLOCALIZACIÓN --}}
                    {{-- ================================================= --}}

                    <hr>

                    <h6 class="text-primary mb-3">

                        <i class="bi bi-geo-alt-fill"></i>

                        Ubicación geográfica del proyecto

                    </h6>


                    <div class="geolocalizacion-panel">

                        <div class="alert alert-info">

                            <i class="bi bi-info-circle"></i>

                            Escriba la dirección del proyecto y presione
                            <strong>Buscar ubicación</strong>.

                            El sistema buscará automáticamente las coordenadas
                            mediante el servicio de geocodificación.

                        </div>

                        <div class="geolocalizacion-wrapper">

                            {{-- BOTÓN BUSCAR --}}

                            <div class="row">

                        <div class="col-md-9 mb-3">

                            <label class="form-label">
                                Dirección para geocodificación
                            </label>

                            <input
                                type="text"
                                id="ubicacionBusqueda"
                                class="form-control"
                                placeholder="Ej.: Av. América, Cochabamba, Bolivia">

                        </div>


                        <div class="col-md-3 mb-3 d-flex align-items-end">

                            <button
                                type="button"
                                id="btnBuscarUbicacion"
                                class="btn btn-primary w-100 btn-buscar-ubicacion">

                                <i class="bi bi-search"></i>

                                Buscar ubicación

                            </button>

                        </div>

                    </div>


                            {{-- COORDENADAS --}}

                            <div class="row ubicacion-coordenadas mb-3">

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Latitud
                                    </label>

                                    <input
                                        type="text"
                                        name="latitud"
                                        id="latitud"
                                        class="form-control"
                                        value="{{ old('latitud') }}"
                                        readonly
                                        placeholder="Se obtiene automáticamente">

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Longitud
                                    </label>

                                    <input
                                        type="text"
                                        name="longitud"
                                        id="longitud"
                                        class="form-control"
                                        value="{{ old('longitud') }}"
                                        readonly
                                        placeholder="Se obtiene automáticamente">

                                </div>

                            </div>


                            {{-- MAPA --}}

                            <div class="mb-4">

                                <label class="form-label fw-bold">

                                    <i class="bi bi-map"></i>

                                    Mapa de ubicación

                                </label>


                                <div id="mapaProyecto"></div>


                                <small class="text-muted d-block mt-2">

                                    <i class="bi bi-info-circle"></i>

                                    Después de buscar la dirección, el marcador
                                    mostrará la ubicación encontrada.

                                </small>

                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- REGISTRAR PLANO --}}
                    {{-- ================================================= --}}

                    <hr>

                    <h6 class="text-primary mb-3">

                        <i class="bi bi-file-earmark-image-fill"></i>

                        Registrar Plano

                    </h6>


                    <div class="alert alert-info">

                        <i class="bi bi-info-circle"></i>

                        Seleccione el plano que pertenece a este proyecto.
                        El plano se registrará automáticamente después de
                        guardar el proyecto.

                    </div>


                    <div class="row">

                        {{-- NOMBRE PLANO --}}

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Nombre del Plano

                                <span class="text-danger">*</span>

                            </label>

                            <input
                                type="text"
                                name="nombre_plano"
                                class="form-control"
                                placeholder="Ej.: Planta Baja"
                                value="{{ old('nombre_plano') }}"
                                required>

                        </div>


                        {{-- ARCHIVO --}}

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Seleccione el Plano

                                <span class="text-danger">*</span>

                            </label>

                            <input
                                type="file"
                                name="archivo"
                                id="archivoPlano"
                                class="form-control"
                                accept=".jpg,.jpeg,.png"
                                required>

                            <small class="text-muted">

                                <i class="bi bi-info-circle"></i>

                                Formatos permitidos:
                                JPG, JPEG y PNG.
                                Tamaño máximo: 5 MB.

                            </small>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- CARACTERÍSTICAS PARA MATERIALES --}}
                    {{-- ================================================= --}}

                    <hr>

                    <h6 class="text-success mb-3">

                        <i class="bi bi-rulers"></i>

                        Características para el cálculo de materiales

                    </h6>


                    <div class="alert alert-info">

                        <i class="bi bi-info-circle"></i>

                        Estos datos serán utilizados para calcular
                        automáticamente los materiales de obra gruesa.

                    </div>


                    <div class="row">

                        {{-- ALTURA --}}

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Altura del muro (m)
                            </label>

                            <input
                                type="number"
                                name="altura_muro"
                                class="form-control"
                                step="0.01"
                                min="0"
                                placeholder="Ej.: 2.40"
                                value="{{ old('altura_muro') }}"
                                required>

                            <small class="text-muted">
                                Altura promedio de los muros.
                            </small>

                        </div>


                        {{-- ESPESOR --}}

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Espesor del muro (m)
                            </label>

                            <input
                                type="number"
                                name="espesor_muro"
                                id="espesorMuro"
                                class="form-control"
                                step="0.001"
                                min="0"
                                placeholder="Ej.: 0.15"
                                value="{{ old('espesor_muro') }}"
                                required>

                            <small class="text-muted">
                                Se completa con el ancho del ladrillo seleccionado, sin revoque.
                                Puede modificarlo si el muro tiene otra disposición.
                                Puede ingresar metros (0.15) o centímetros (15).
                                Se guardará siempre en metros.
                            </small>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- DIMENSIONES DEL LADRILLO --}}
                    {{-- ================================================= --}}

                    <div class="dimensiones-section">

                        <h6 class="mt-2 mb-3 fw-bold">

                            <i class="bi bi-bricks me-2 text-primary"></i>

                            Dimensiones de ladrillo

                        </h6>


                        <div class="row g-3">


                            {{-- TIPO --}}

                            <div class="col-12 mb-2">

                                <label
                                    for="tipoLadrillo"
                                    class="form-label fw-semibold">

                                    <i class="bi bi-list-ul me-1"></i>

                                    Seleccionar tipo de ladrillo

                                </label>


                                <select
                                    id="tipoLadrillo"
                                    class="form-select"
                                    required>

                                    <option value="">
                                        -- Elegir un tipo --
                                    </option>

                                    <option value="gambote">
                                        Gambote / adobito (20 x 10 x 4.5 cm)
                                    </option>

                                    <option value="seis_huecos">
                                        Ladrillo cerámico de 6 huecos (24 x 10 x 15 cm)
                                    </option>

                                    <option value="dieciocho_huecos">
                                        Ladrillo cerámico de 18 huecos (24 x 14 x 10 cm)
                                    </option>

                                    <option value="visto_18_huecos">
                                        Ladrillo visto de 18 huecos (24 x 12 x 7 cm)
                                    </option>

                                    <option value="bloque_hormigon">
                                        Bloque de hormigón (40 x 15 x 20 cm)
                                    </option>

                                </select>

                                <div class="row g-2 mt-2" role="group" aria-label="Seleccionar ladrillo por imagen">
                                    @foreach ([
                                        'gambote' => ['Gambote / adobito', 'gamboteadobito.png'],
                                        'seis_huecos' => ['Cerámico de 6 huecos', 'ladrilloceramicoode6huecos.png'],
                                        'dieciocho_huecos' => ['Cerámico de 18 huecos', 'ladrilloceramicode18huecos.png'],
                                        'visto_18_huecos' => ['Visto de 18 huecos', 'ladrillovistode18huecos.png'],
                                        'bloque_hormigon' => ['Bloque de hormigón', 'bloquedehormigon.png'],
                                    ] as $tipo => [$nombre, $imagen])
                                        <div class="col-6 col-md-4">
                                            <button type="button"
                                                class="btn btn-outline-secondary w-100 h-100 p-2 ladrillo-opcion"
                                                data-tipo-ladrillo="{{ $tipo }}"
                                                aria-pressed="false">
                                                <img src="{{ asset('images/' . $imagen) }}"
                                                    alt="{{ $nombre }}"
                                                    class="d-block w-100 rounded mb-2 bg-white"
                                                    style="height: 120px; object-fit: contain;"
                                                    loading="lazy">
                                                <span class="d-block small fw-semibold">{{ $nombre }}</span>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>


                                <small class="text-muted">

                                    Medidas referenciales de uso habitual en Bolivia, en orden:
                                    largo x espesor x alto. Verifique siempre la ficha del fabricante.

                                </small>

                            </div>


                            {{-- LARGO --}}

                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Largo (m)
                                </label>

                                <input
                                    type="number"
                                    name="ladrillo_largo"
                                    id="largoLadrillo"
                                    class="form-control"
                                    step="0.001"
                                    readonly
                                    required>

                            </div>


                            {{-- ANCHO --}}

                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Espesor (m)
                                </label>

                                <input
                                    type="number"
                                    name="ladrillo_ancho"
                                    id="anchoLadrillo"
                                    class="form-control"
                                    step="0.001"
                                    readonly
                                    required>

                            </div>


                            {{-- ALTO --}}

                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Alto (m)
                                </label>

                                <input
                                    type="number"
                                    name="ladrillo_alto"
                                    id="altoLadrillo"
                                    class="form-control"
                                    step="0.001"
                                    readonly
                                    required>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- FOOTER --}}
                {{-- ================================================= --}}

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Cancelar

                    </button>


                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-save"></i>

                        Guardar Proyecto y Plano

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ========================================================= --}}

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin="">
</script>


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {


    // =========================================================
    // SELECCIÓN AUTOMÁTICA DE LADRILLO
    // =========================================================

    const tipoSelect = document.getElementById('tipoLadrillo');

    const largoInput = document.getElementById('largoLadrillo');

    const anchoInput = document.getElementById('anchoLadrillo');

    const altoInput = document.getElementById('altoLadrillo');

    const espesorMuroInput = document.getElementById('espesorMuro');


    const dimensiones = {

        gambote: {
            largo: 0.20,
            ancho: 0.10,
            alto: 0.045
        },

        seis_huecos: {
            largo: 0.24,
            ancho: 0.10,
            alto: 0.15
        },

        dieciocho_huecos: {
            largo: 0.24,
            ancho: 0.14,
            alto: 0.10
        },

        visto_18_huecos: {
            largo: 0.24,
            ancho: 0.12,
            alto: 0.07
        },

        bloque_hormigon: {
            largo: 0.40,
            ancho: 0.15,
            alto: 0.20
        }

    };


    function actualizarDimensiones(tipo, actualizarEspesor = true) {

        document.querySelectorAll('[data-tipo-ladrillo]').forEach(function (opcion) {
            const seleccionado = opcion.dataset.tipoLadrillo === tipo;
            opcion.classList.toggle('btn-outline-secondary', !seleccionado);
            opcion.classList.toggle('btn-outline-primary', seleccionado);
            opcion.classList.toggle('active', seleccionado);
            opcion.setAttribute('aria-pressed', String(seleccionado));
        });

        if (tipo && dimensiones[tipo]) {

            const dim = dimensiones[tipo];

            largoInput.value = dim.largo;

            anchoInput.value = dim.ancho;

            if (espesorMuroInput && actualizarEspesor) {
                espesorMuroInput.value = dim.ancho;
            }

            altoInput.value = dim.alto;

        } else {

            largoInput.value = '';

            anchoInput.value = '';

            altoInput.value = '';

        }

    }


    if (tipoSelect) {

        document.querySelectorAll('[data-tipo-ladrillo]').forEach(function (opcion) {
            opcion.addEventListener('click', function () {
                tipoSelect.value = this.dataset.tipoLadrillo;
                tipoSelect.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });

        tipoSelect.addEventListener('change', function () {

            actualizarDimensiones(this.value);

        });


        // Común por defecto

        tipoSelect.value = 'seis_huecos';

        actualizarDimensiones('seis_huecos', !espesorMuroInput?.value);

    }


    // =========================================================
    // ARCHIVO DEL PLANO
    // =========================================================

    const archivo = document.getElementById('archivoPlano');


    if (archivo) {

        archivo.addEventListener('change', function () {

            if (this.files.length > 0) {

                console.log(
                    'Plano seleccionado:',
                    this.files[0].name
                );

            }

        });

    }


    // =========================================================
    // MAPA
    // =========================================================

    const mapaElemento =
        document.getElementById('mapaProyecto');


    const ubicacionBusqueda =
        document.getElementById('ubicacionBusqueda');


    const ubicacionInput =
        document.getElementById('ubicacion');


    const latitudInput =
        document.getElementById('latitud');


    const longitudInput =
        document.getElementById('longitud');


    const btnBuscarUbicacion =
        document.getElementById('btnBuscarUbicacion');


    let mapa = null;

    let marcador = null;


    // =========================================================
    // INICIALIZAR MAPA
    // =========================================================

    function inicializarMapa() {

        if (!mapaElemento) {
            return;
        }

        if (!mapa) {

            mapa = L.map('mapaProyecto', {
                dragging: true,
                scrollWheelZoom: true,
                doubleClickZoom: true,
                touchZoom: true,
                zoomControl: false
            }).setView(
                [-16.5000, -64.5000],
                5
            );

            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                {
                    attribution:
                        '&copy; OpenStreetMap contributors'
                }
            ).addTo(mapa);

            mapa.addControl(
                new ControlNavegacion()
            );

        }

        setTimeout(function () {
            if (mapa) {
                mapa.invalidateSize();
            }
        }, 200);


    }
    // =========================================================
// CONTROL DE NAVEGACIÓN DEL MAPA
// =========================================================

const ControlNavegacion = L.Control.extend({

    options: {
        position: 'bottomright'
    },

    onAdd: function () {

        const contenedor =
            L.DomUtil.create(
                'div',
                'control-navegacion leaflet-bar'
            );


        contenedor.innerHTML = `

            <div class="fila">

                <button
                    type="button"
                    class="btn-arriba"
                    title="Mover arriba">

                    ↑

                </button>

            </div>


            <div class="fila">

                <button
                    type="button"
                    class="btn-izquierda"
                    title="Mover izquierda">

                    ←

                </button>


                <button
                    type="button"
                    class="btn-centro"
                    title="Centrar mapa">

                    ⌖

                </button>


                <button
                    type="button"
                    class="btn-derecha"
                    title="Mover derecha">

                    →

                </button>

            </div>


            <div class="fila">

                <button
                    type="button"
                    class="btn-abajo"
                    title="Mover abajo">

                    ↓

                </button>

            </div>


            <div class="fila">

                <button
                    type="button"
                    class="btn-zoom-mas"
                    title="Acercar">

                    +

                </button>

            </div>


            <div class="fila">

                <button
                    type="button"
                    class="btn-zoom-menos"
                    title="Alejar">

                    −

                </button>

            </div>
        `;


        // Evitar que los botones interfieran con el modal

        L.DomEvent.disableClickPropagation(contenedor);

        L.DomEvent.disableScrollPropagation(contenedor);


        // =====================================================
        // DESPLAZAMIENTO
        // =====================================================

        contenedor
            .querySelector('.btn-arriba')
            .onclick = function () {

                mapa.panBy([0, -100]);

            };


        contenedor
            .querySelector('.btn-abajo')
            .onclick = function () {

                mapa.panBy([0, 100]);

            };


        contenedor
            .querySelector('.btn-izquierda')
            .onclick = function () {

                mapa.panBy([-100, 0]);

            };


        contenedor
            .querySelector('.btn-derecha')
            .onclick = function () {

                mapa.panBy([100, 0]);

            };


        // =====================================================
        // CENTRAR
        // =====================================================

        contenedor
            .querySelector('.btn-centro')
            .onclick = function () {

                if (marcador) {

                    mapa.setView(
                        marcador.getLatLng(),
                        17
                    );

                } else {

                    mapa.setView(
                        [-16.5000, -64.5000],
                        5
                    );

                }

            };


        // =====================================================
        // ZOOM +
        // =====================================================

        contenedor
            .querySelector('.btn-zoom-mas')
            .onclick = function () {

                mapa.zoomIn();

            };


        // =====================================================
        // ZOOM -
        // =====================================================

        contenedor
            .querySelector('.btn-zoom-menos')
            .onclick = function () {

                mapa.zoomOut();

            };


        return contenedor;
    }

});





    // =========================================================
    // CUANDO SE ABRE EL MODAL
    // =========================================================

    const modalProyecto =
        document.getElementById('nuevoProyecto');


    if (modalProyecto) {
        if (new URLSearchParams(window.location.search).get('crear') === '1') {
            setTimeout(() => bootstrap.Modal.getOrCreateInstance(modalProyecto).show(), 0);
        }

        modalProyecto.addEventListener(
            'shown.bs.modal',
            function () {

                inicializarMapa();

                setTimeout(function () {

                    if (mapa) {

                        mapa.invalidateSize();

                    }

                }, 200);

            }
        );

    }


    // =========================================================
    // BUSCAR UBICACIÓN
    // =========================================================

    if (btnBuscarUbicacion) {

        btnBuscarUbicacion.addEventListener(
            'click',
            async function () {


                const ubicacion =
                    ubicacionBusqueda.value.trim();


                if (!ubicacion) {

                    alert(
                        'Ingrese una dirección para buscar.'
                    );

                    ubicacionBusqueda.focus();

                    return;

                }


                const textoOriginal =
                    btnBuscarUbicacion.innerHTML;


                btnBuscarUbicacion.disabled = true;


                btnBuscarUbicacion.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1"></span> Buscando...';


                try {


                    const respuesta = await fetch(
                        "{{ route('proyectos.geocodificar') }}",
                        {

                            method: 'POST',

                            headers: {

                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    '{{ csrf_token() }}'

                            },

                            body: JSON.stringify({

                                ubicacion: ubicacion

                            })

                        }
                    );


                    const datos =
                        await respuesta.json();


                    if (
                        !respuesta.ok ||
                        !datos.success
                    ) {

                        throw new Error(
                            datos.message ||
                            'No se encontró la ubicación.'
                        );

                    }


                    // =================================================
                    // GUARDAR COORDENADAS EN EL FORMULARIO
                    // =================================================

                    if (!latitudInput || !longitudInput) {
                        throw new Error('No se encontraron los campos de latitud y longitud.');
                    }

                    latitudInput.value = Number(datos.latitud).toFixed(7);

                    longitudInput.value = Number(datos.longitud).toFixed(7);


                    // Dirección encontrada

                    if (datos.direccion) {

                        ubicacionInput.value =
                            datos.direccion;

                    }


                    // =================================================
                    // INICIALIZAR MAPA
                    // =================================================

                    inicializarMapa();


                    // =================================================
                    // CENTRAR MAPA
                    // =================================================

                    mapa.setView(
                        [
                            datos.latitud,
                            datos.longitud
                        ],
                        17
                    );


                    // =================================================
                    // ELIMINAR MARCADOR ANTERIOR
                    // =================================================

                    if (marcador) {

                        mapa.removeLayer(
                            marcador
                        );

                    }


                    // =================================================
                    // CREAR MARCADOR
                    // =================================================

                    marcador = L.marker(
                        [
                            datos.latitud,
                            datos.longitud
                        ],
                        {
                            draggable: true
                        }
                    ).addTo(mapa);


                    marcador
                        .bindPopup(
                            '<strong>Ubicación del proyecto</strong><br>' +
                            datos.direccion
                        )
                        .openPopup();


                    // =================================================
                    // PERMITIR MOVER EL MARCADOR
                    // =================================================

                    marcador.on(
                        'dragend',
                        function (evento) {

                            const posicion =
                                evento.target.getLatLng();


                            latitudInput.value =
                                posicion.lat.toFixed(7);


                            longitudInput.value =
                                posicion.lng.toFixed(7);


                        }
                    );


                    // Mensaje

                    console.log(
                        'Ubicación encontrada:',
                        datos
                    );


                } catch (error) {


                    console.error(error);


                    alert(
                        error.message ||
                        'No fue posible encontrar la ubicación.'
                    );


                } finally {


                    btnBuscarUbicacion.disabled =
                        false;


                    btnBuscarUbicacion.innerHTML =
                        textoOriginal;

                }

            }
        );

    }

});

</script>

@endpush

@endsection
