@extends('layouts.app')

@section('content')
<h2 class="mb-4">Nuevo Material</h2>

<div class="card">
    <div class="card-body">
        <form action="{{ route('materiales.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control"
                       value="{{ old('nombre') }}" required>
                @error('nombre') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Unidad de medida</label>
                <input type="text" name="unidad_medida" class="form-control"
                       placeholder="Ej: bolsa, m2, m3, kg, unidad"
                       value="{{ old('unidad_medida') }}" required>
                @error('unidad_medida') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Precio referencial (Bs.)</label>
                <input type="number" step="0.01" min="0" name="precio_referencial"
                       class="form-control" value="{{ old('precio_referencial') }}" required>
                @error('precio_referencial') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Categoría</label>
                <input type="text" name="categoria" class="form-control"
                       placeholder="Ej: cimentación, muros, techado"
                       value="{{ old('categoria') }}">
                @error('categoria') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="{{ route('materiales.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection