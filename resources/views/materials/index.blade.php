@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-1"><i class="bi bi-box-seam-fill text-primary me-2"></i>Materiales</h2>
            <p class="text-muted mb-0">Catálogo de materiales disponibles para los cálculos.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><i class="bi bi-plus-circle me-2"></i>Registrar material</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('materials.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="nombre">Nombre</label>
                            <input class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="unidad">Unidad</label>
                                <input class="form-control" id="unidad" name="unidad" value="{{ old('unidad') }}" placeholder="kg, m³, bolsa" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="stock">Stock</label>
                                <input class="form-control" id="stock" type="number" min="0" step="0.01" name="stock" value="{{ old('stock', 0) }}" required>
                            </div>
                        </div>
                        <div class="row g-3 mt-0">
                            <div class="col-md-6">
                                <label class="form-label" for="stock_minimo">Stock mínimo</label>
                                <input class="form-control" id="stock_minimo" type="number" min="0" step="0.01" name="stock_minimo" value="{{ old('stock_minimo', 0) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="precio_unitario">Precio unitario (Bs)</label>
                                <input class="form-control" id="precio_unitario" type="number" min="0" step="0.01" name="precio_unitario" value="{{ old('precio_unitario', 0) }}">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label" for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                        </div>
                        <button class="btn btn-primary w-100 mt-4"><i class="bi bi-save me-2"></i>Guardar material</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-ul me-2"></i>Materiales registrados</span>
                    <span class="badge bg-primary">{{ $materials->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>Material</th><th>Unidad</th><th>Stock</th><th>Precio</th><th>Movimiento</th><th class="text-end">Acción</th></tr></thead>
                            <tbody>
                            @forelse($materials as $material)
                                <tr>
                                    <td><span class="fw-semibold">{{ $material->nombre }}</span><small class="d-block text-muted">{{ $material->descripcion ?: 'Sin descripción' }}</small></td>
                                    <td>{{ $material->unidad }}</td>
                                    <td><span class="badge {{ $material->stock <= $material->stock_minimo ? 'bg-danger' : 'bg-success' }}">{{ number_format($material->stock, 2) }}</span><small class="d-block text-muted">Mín.: {{ number_format($material->stock_minimo, 2) }}</small></td>
                                    <td>Bs {{ number_format($material->precio_unitario, 2) }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('materials.movimientos.store', $material) }}" class="d-flex gap-1">
                                            @csrf
                                            <select name="tipo" class="form-select form-select-sm" aria-label="Tipo de movimiento"><option value="entrada">Entrada</option><option value="salida">Salida</option></select>
                                            <input name="cantidad" class="form-control form-control-sm" type="number" min="0.01" step="0.01" placeholder="Cant." required>
                                            <button class="btn btn-outline-primary btn-sm" title="Registrar"><i class="bi bi-arrow-left-right"></i></button>
                                        </form>
                                    </td>
                                    <td class="text-end"><form method="POST" action="{{ route('materials.destroy', $material) }}" class="d-inline" onsubmit="return confirm('¿Eliminar este material?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm" title="Eliminar"><i class="bi bi-trash"></i></button></form></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-5">No hay materiales registrados.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
