@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow">

        <div class="card-header bg-warning text-dark">

            <h4 class="mb-0">

                <i class="bi bi-pencil-fill"></i>

                Editar Material

            </h4>

        </div>

        <div class="card-body">

            <form action="{{ route('materiales.update',$material->id) }}" method="POST">

                @csrf

                @method('PUT')

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Nombre

                        </label>

                        <input
                            type="text"
                            name="nombre"
                            class="form-control"
                            value="{{ $material->nombre }}"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Unidad

                        </label>

                        <select
                            name="unidad"
                            class="form-select"
                            required>

                            <option value="Bolsa" {{ $material->unidad=='Bolsa' ? 'selected' : '' }}>Bolsa</option>

                            <option value="m³" {{ $material->unidad=='m³' ? 'selected' : '' }}>m³</option>

                            <option value="Unidad" {{ $material->unidad=='Unidad' ? 'selected' : '' }}>Unidad</option>

                            <option value="Barra" {{ $material->unidad=='Barra' ? 'selected' : '' }}>Barra</option>

                            <option value="Kg" {{ $material->unidad=='Kg' ? 'selected' : '' }}>Kg</option>

                        </select>

                    </div>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Stock

                    </label>

                    <input
                        type="number"
                        step="0.01"
                        name="stock"
                        class="form-control"
                        value="{{ $material->stock }}"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Descripción

                    </label>

                    <textarea
                        name="descripcion"
                        rows="4"
                        class="form-control">{{ $material->descripcion }}</textarea>

                </div>

                <div class="d-flex justify-content-between">

                    <a href="{{ route('materiales.index') }}"
                       class="btn btn-secondary">

                        <i class="bi bi-arrow-left-circle"></i>

                        Volver

                    </a>

                    <button
                        type="submit"
                        class="btn btn-success">

                        <i class="bi bi-save"></i>

                        Actualizar Material

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection