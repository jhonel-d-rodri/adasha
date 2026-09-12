@extends('layouts.app')

@section('content')

<div class="container">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">

            <h3 class="mb-0">
                <i class="bi bi-pencil-square"></i>
                Editar Proyecto
            </h3>

        </div>

        <div class="card-body">

            @if ($errors->any())

                <div class="alert alert-danger">

                    <ul class="mb-0">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <form
                action="{{ route('proyectos.update', $proyecto->id) }}"
                method="POST">

                @csrf

                @method('PUT')


                <h5 class="text-primary mb-3">
                    Datos del Proyecto
                </h5>


                <div class="mb-3">

                    <label class="form-label">
                        Nombre del proyecto
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        class="form-control"
                        value="{{ old('nombre', $proyecto->nombre) }}"
                        required>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Dirección
                    </label>

                    <input
                        type="text"
                        name="direccion"
                        class="form-control"
                        value="{{ old('direccion', $proyecto->direccion) }}">

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Tipo de vivienda
                    </label>

                    <select
                        name="tipo_vivienda"
                        class="form-select"
                        required>

                        <option value="unifamiliar"
                            {{ $proyecto->tipo_vivienda == 'unifamiliar' ? 'selected' : '' }}>
                            Unifamiliar
                        </option>

                        <option value="multifamiliar"
                            {{ $proyecto->tipo_vivienda == 'multifamiliar' ? 'selected' : '' }}>
                            Multifamiliar
                        </option>

                    </select>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Superficie del terreno (m²)
                    </label>

                    <input
                        type="number"
                        name="superficie_terreno"
                        class="form-control"
                        step="0.01"
                        min="0"
                        value="{{ old('superficie_terreno', $proyecto->superficie_terreno) }}">

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Estado
                    </label>

                    <select
                        name="estado"
                        class="form-select"
                        required>

                        <option value="registrado"
                            {{ $proyecto->estado == 'registrado' ? 'selected' : '' }}>
                            Registrado
                        </option>

                        <option value="en_proceso"
                            {{ $proyecto->estado == 'en_proceso' ? 'selected' : '' }}>
                            En proceso
                        </option>

                        <option value="analizado"
                            {{ $proyecto->estado == 'analizado' ? 'selected' : '' }}>
                            Analizado
                        </option>

                        <option value="finalizado"
                            {{ $proyecto->estado == 'finalizado' ? 'selected' : '' }}>
                            Finalizado
                        </option>

                    </select>

                </div>


                <div class="mb-4">

                    <label class="form-label">
                        Descripción
                    </label>

                    <textarea
                        name="descripcion"
                        class="form-control"
                        rows="3">{{ old('descripcion', $proyecto->descripcion) }}</textarea>

                </div>


                <hr>


                <h5 class="text-success mb-3">

                    <i class="bi bi-rulers"></i>

                    Características para el cálculo de materiales

                </h5>


                <div class="alert alert-info">

                    Estos datos se utilizarán para calcular automáticamente
                    los materiales de obra gruesa.

                </div>


                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Altura del muro (m)
                        </label>

                        <input
                            type="number"
                            name="altura_muro"
                            class="form-control"
                            step="0.01"
                            min="0.01"
                            value="{{ old('altura_muro', $proyecto->altura_muro) }}"
                            placeholder="Ej.: 2.40"
                            required>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Espesor del muro (m)
                        </label>

                        <input
                            type="number"
                            name="espesor_muro"
                            class="form-control"
                            step="0.001"
                            min="0.001"
                            value="{{ old('espesor_muro', $proyecto->espesor_muro) }}"
                            placeholder="Ej.: 0.15"
                            required>

                    </div>

                </div>


                <h6 class="mt-3 mb-3">
                    Dimensiones del Ladrillo Gambote
                </h6>


                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Largo (m)
                        </label>

                        <input
                            type="number"
                            name="ladrillo_largo"
                            class="form-control"
                            step="0.001"
                            min="0.001"
                            value="{{ old('ladrillo_largo', $proyecto->ladrillo_largo) }}"
                            placeholder="Ej.: 0.24"
                            required>

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Ancho (m)
                        </label>

                        <input
                            type="number"
                            name="ladrillo_ancho"
                            class="form-control"
                            step="0.001"
                            min="0.001"
                            value="{{ old('ladrillo_ancho', $proyecto->ladrillo_ancho) }}"
                            placeholder="Ej.: 0.12"
                            required>

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Alto (m)
                        </label>

                        <input
                            type="number"
                            name="ladrillo_alto"
                            class="form-control"
                            step="0.001"
                            min="0.001"
                            value="{{ old('ladrillo_alto', $proyecto->ladrillo_alto) }}"
                            placeholder="Ej.: 0.09"
                            required>

                    </div>

                </div>


                <div class="mt-4">

                    <button
                        type="submit"
                        class="btn btn-success">

                        <i class="bi bi-save"></i>
                        Guardar características

                    </button>


                    <a
                        href="{{ route('proyectos.index') }}"
                        class="btn btn-secondary">

                        Cancelar

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection