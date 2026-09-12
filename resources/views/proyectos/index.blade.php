@extends('layouts.app')

@section('content')

{{-- ========================================================= --}}
{{-- LEAFLET --}}
{{-- ========================================================= --}}

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIINfQ3fW1q7s9dQJ9b7d8G8m5x5V6d0n7M="
    crossorigin=""
>


<style>
    #mapaProyecto {
    width: 100%;
    height: 350px;
    border-radius: 10px;
    border: 1px solid #dee2e6;
    z-index: 1;
    overflow: hidden;
}

/* Control de navegación */
.control-navegacion {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.25);
    padding: 5px;
}

.control-navegacion button {
    width: 36px;
    height: 36px;
    border: 1px solid #ddd;
    background: white;
    cursor: pointer;
    font-size: 18px;
    font-weight: bold;
    display: block;
    margin: 2px;
    border-radius: 5px;
}

.control-navegacion button:hover {
    background: #0d6efd;
    color: white;
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


<div class="container-fluid">

    {{-- ========================================================= --}}
    {{-- ENCABEZADO --}}
    {{-- ========================================================= --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>
            <i class="bi bi-folder-fill text-primary"></i>
            Gestión de Proyectos
        </h2>

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

    <div class="card shadow">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">

                <i class="bi bi-list-ul"></i>

                Lista de Proyectos

            </h5>

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th>ID</th>

                            <th>Proyecto</th>

                            <th>Cliente</th>

                            <th>Ubicación</th>

                            <th>Fecha</th>

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
                                {{ $proyecto->fecha_inicio }}
                            </td>


                            <td>

                                @if($proyecto->estado == 'registrado')

                                    <span class="badge bg-success">
                                        Activo
                                    </span>

                                @elseif($proyecto->estado == 'en_proceso')

                                    <span class="badge bg-warning text-dark">
                                        En proceso
                                    </span>

                                @elseif($proyecto->estado == 'finalizado')

                                    <span class="badge bg-danger">
                                        Finalizado
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        {{ $proyecto->estado }}
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


                    {{-- ================================================= --}}
                    {{-- ESTADO Y DESCRIPCIÓN LADO A LADO --}}
                    {{-- ================================================= --}}

                    <div class="row">

                        {{-- ESTADO --}}

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Estado
                            </label>

                            <select
                                name="estado"
                                class="form-select"
                                required>

                                <option
                                    value="Activo"
                                    {{ old('estado', 'Activo') == 'Activo' ? 'selected' : '' }}>

                                    Activo

                                </option>

                                <option
                                    value="En_proceso"
                                    {{ old('estado') == 'En_proceso' ? 'selected' : '' }}>

                                    En proceso

                                </option>

                                <option
                                    value="Finalizado"
                                    {{ old('estado') == 'Finalizado' ? 'selected' : '' }}>

                                    Finalizado

                                </option>

                            </select>

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
                    {{-- GEOLOCALIZACIÓN --}}
                    {{-- ================================================= --}}

                    <hr>

                    <h6 class="text-primary mb-3">

                        <i class="bi bi-geo-alt-fill"></i>

                        Ubicación geográfica del proyecto

                    </h6>


                    <div class="alert alert-info">

                        <i class="bi bi-info-circle"></i>

                        Escriba la dirección del proyecto y presione
                        <strong>Buscar ubicación</strong>.

                        El sistema buscará automáticamente las coordenadas
                        mediante el servicio de geocodificación.

                    </div>


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
                                accept=".jpg,.jpeg,.png,.pdf"
                                required>

                            <small class="text-muted">

                                <i class="bi bi-info-circle"></i>

                                Formatos permitidos:
                                JPG, JPEG, PNG y PDF.
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
                                class="form-control"
                                step="0.001"
                                min="0"
                                placeholder="Ej.: 0.15"
                                value="{{ old('espesor_muro') }}"
                                required>

                            <small class="text-muted">
                                Ejemplo: 0.15 m = 15 cm.
                            </small>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- DIMENSIONES DEL LADRILLO --}}
                    {{-- ================================================= --}}

                    <div class="dimensiones-section">

                        <h6 class="mt-2 mb-3 fw-bold">

                            <i class="bi bi-bricks me-2 text-primary"></i>

                            Dimensiones del Ladrillo Gambote

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

                                    <option value="comun">
                                        Ladrillo Común (0.24 x 0.12 x 0.09)
                                    </option>

                                    <option value="hueco">
                                        Ladrillo Hueco (0.30 x 0.15 x 0.10)
                                    </option>

                                    <option value="refractario">
                                        Ladrillo Refractario (0.23 x 0.11 x 0.08)
                                    </option>

                                    <option value="techo">
                                        Ladrillo de Techo (0.40 x 0.20 x 0.12)
                                    </option>

                                    <option value="visto">
                                        Ladrillo Visto (0.29 x 0.14 x 0.09)
                                    </option>

                                    <option value="bloque">
                                        Bloque de Hormigón (0.40 x 0.20 x 0.20)
                                    </option>

                                    <option value="kingkong">
                                        Ladrillo King Kong (0.25 x 0.14 x 0.10)
                                    </option>

                                    <option value="pandereta">
                                        Ladrillo Pandereta (0.30 x 0.15 x 0.08)
                                    </option>

                                </select>


                                <small class="text-muted">

                                    Seleccione un tipo y sus dimensiones
                                    se cargarán automáticamente.

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
                                    Ancho (m)
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


    const dimensiones = {

        comun: {
            largo: 0.24,
            ancho: 0.12,
            alto: 0.09
        },

        hueco: {
            largo: 0.30,
            ancho: 0.15,
            alto: 0.10
        },

        refractario: {
            largo: 0.23,
            ancho: 0.11,
            alto: 0.08
        },

        techo: {
            largo: 0.40,
            ancho: 0.20,
            alto: 0.12
        },

        visto: {
            largo: 0.29,
            ancho: 0.14,
            alto: 0.09
        },

        bloque: {
            largo: 0.40,
            ancho: 0.20,
            alto: 0.20
        },

        kingkong: {
            largo: 0.25,
            ancho: 0.14,
            alto: 0.10
        },

        pandereta: {
            largo: 0.30,
            ancho: 0.15,
            alto: 0.08
        }

    };


    function actualizarDimensiones(tipo) {

        if (tipo && dimensiones[tipo]) {

            const dim = dimensiones[tipo];

            largoInput.value = dim.largo;

            anchoInput.value = dim.ancho;

            altoInput.value = dim.alto;

        } else {

            largoInput.value = '';

            anchoInput.value = '';

            altoInput.value = '';

        }

    }


    if (tipoSelect) {

        tipoSelect.addEventListener('change', function () {

            actualizarDimensiones(this.value);

        });


        // Común por defecto

        tipoSelect.value = 'comun';

        actualizarDimensiones('comun');

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

        if (mapa) {

            mapa.invalidateSize();

            return;

        }


        // Centro inicial en Bolivia

        mapa = L.map('mapaProyecto', {

    // Evita que el ratón mueva accidentalmente el mapa
    dragging: false,

    // Permite hacer zoom con la rueda
    scrollWheelZoom: true,

    // Zoom con doble clic
    doubleClickZoom: true,

    // Zoom táctil
    touchZoom: true

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

        // Agregar el control de navegación solamente
        // después de haber inicializado el mapa.
        mapa.addControl(
            new ControlNavegacion()
        );

    }
    // =========================================================
// CONTROL DE NAVEGACIÓN DEL MAPA
// =========================================================

const ControlNavegacion = L.Control.extend({

    options: {
        position: 'topright'
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

                    latitudInput.value =
                        datos.latitud;


                    longitudInput.value =
                        datos.longitud;


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