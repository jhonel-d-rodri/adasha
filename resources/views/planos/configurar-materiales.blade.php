@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-warning">
            <h3 class="mb-0"><i class="bi bi-calculator-fill"></i> Configurar obra gruesa</h3>
        </div>
        <div class="card-body">
            @php($sugeridas = $plano->medidasSugeridas())
            <p class="text-muted">
                @if($sugeridas)
                    Se proponen los datos leídos del rótulo del plano. Revise los valores antes de pulsar Calcular materiales.
                @else
                    No se pudo determinar la longitud por planta. Solicite una medida verificada antes de calcular.
                @endif
            </p>

            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('calculos.calcular', $plano->id) }}">
                @csrf
                <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Longitud de muros por planta (m)</label>
                            <input type="number" name="longitud_total" class="form-control" min="0.01" step="0.01" value="{{ old('longitud_total', $sugeridas['longitud_por_planta'] ?? null) }}" required>
                            <small class="text-muted">Use la suma de las longitudes reales de los muros de una planta.</small>
                        </div>
                    <div class="col-md-4">
                        <label class="form-label">Cantidad de plantas</label>
                        <select name="plantas" class="form-select" required>
                            <option value="1" @selected(old('plantas', $sugeridas['plantas'] ?? $plano->plantas ?? 1) == 1)>Una planta</option>
                            <option value="2" @selected(old('plantas', $sugeridas['plantas'] ?? $plano->plantas ?? 1) == 2)>Dos plantas</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tipo de cubierta</label>
                        <select name="tipo_cubierta" id="tipo_cubierta" class="form-select" required>
                            <option value="liviana" @selected(old('tipo_cubierta', $plano->tipo_cubierta ?? 'liviana') === 'liviana')>Techo liviano</option>
                            <option value="losa" @selected(old('tipo_cubierta', $plano->tipo_cubierta ?? 'liviana') === 'losa')>Losa de hormigón</option>
                        </select>
                    </div>
                    <div class="col-md-4" id="area-losa">
                        <label class="form-label">Área construida (m²)</label>
                        <input type="number" name="superficie_construida" class="form-control" min="1" step="0.01" value="{{ old('superficie_construida', $plano->superficie_construida) }}">
                        <small class="text-muted">Necesaria para calcular la losa.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Área de puertas y ventanas (m²)</label>
                        <input type="number" name="area_vanos" class="form-control" min="0" step="0.01" value="{{ old('area_vanos', $sugeridas['area_vanos'] ?? $plano->area_vanos ?? 0) }}">
                        <small class="text-muted">Suma las áreas de todos los vanos para descontarlos de los muros.</small>
                    </div>
                </div>

                <hr>
                <h5>Elementos de obra gruesa</h5>
                <div class="row g-3 mb-3">
                    @foreach ([
                        'cimentacion' => 'Cimiento de piedra',
                        'columnas' => 'Columnas de hormigón armado',
                        'vigas' => 'Vigas de hormigón armado',
                    ] as $campo => $etiqueta)
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input type="hidden" name="{{ $campo }}" value="0">
                                <input class="form-check-input" type="checkbox" name="{{ $campo }}" value="1" id="{{ $campo }}" @checked(old($campo, $plano->{$campo} ?? true))>
                                <label class="form-check-label" for="{{ $campo }}">{{ $etiqueta }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="alert alert-info">
                    Las cantidades son referenciales y siguen dosificaciones usuales de obra gruesa. Para construir, un profesional debe revisar el plano y el diseño estructural.
                </div>

                <button class="btn btn-success"><i class="bi bi-calculator"></i> Calcular materiales</button>
                <a href="{{ route('planos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const cubierta = document.getElementById('tipo_cubierta');
    const areaLosa = document.getElementById('area-losa');
    const actualizarArea = () => areaLosa.style.opacity = cubierta.value === 'losa' ? '1' : '0.55';
    cubierta.addEventListener('change', actualizarArea);
    actualizarArea();
</script>
@endpush
