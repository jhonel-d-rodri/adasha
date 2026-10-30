@extends('layouts.app')

@section('content')

<div class="container">

    <div class="card shadow">

        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">

            <h3 class="mb-0">
                <i class="bi bi-cpu-fill"></i>
                Resultado del Análisis
            </h3>

            <div>
                <a href="{{ route('planos.calcular-materiales', $plano->id) }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-calculator-fill"></i>
                    Configurar y calcular materiales
                </a>

                <a href="{{ route('planos.index') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left-circle"></i>
                    Volver
                </a>
            </div>

        </div>

        <div class="card-body">

            <h4 class="text-primary">
                <i class="bi bi-file-earmark-image-fill"></i>
                {{ $plano->nombre }}
            </h4>

            <hr>

            <div class="row">

                {{-- Estado --}}
                <div class="col-md-3">
                    <div class="card border-success shadow-sm mb-3">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Estado</h6>
                            <span class="badge bg-success fs-6">
                                {{ strtoupper($resultado['estado'] ?? 'ANALIZADO') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Ancho --}}
                <div class="col-md-3">
                    <div class="card border-primary shadow-sm mb-3">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Ancho</h6>
                            <h4 class="text-primary">
                                {{ $resultado['ancho'] ?? 0 }} px
                            </h4>
                        </div>
                    </div>
                </div>

                {{-- Alto --}}
                <div class="col-md-3">
                    <div class="card border-warning shadow-sm mb-3">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Alto</h6>
                            <h4 class="text-warning">
                                {{ $resultado['alto'] ?? 0 }} px
                            </h4>
                        </div>
                    </div>
                </div>

                {{-- Líneas detectadas --}}
                <div class="col-md-3">
                    <div class="card border-danger shadow-sm mb-3">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Líneas detectadas</h6>
                            <h4 class="text-danger">
                                {{ $resultado['muros'] ?? 0 }}
                            </h4>
                        </div>
                    </div>
                </div>

                {{-- Longitud total --}}
                <div class="col-md-3">
                    <div class="card border-info shadow-sm mb-3">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Longitud por planta</h6>
                            <h4 class="text-info">
                                @if($plano->medidasSugeridas())
                                    {{ number_format($plano->medidasSugeridas()['longitud_por_planta'], 2, ',', '.') }} m
                                @else
                                    No determinada
                                @endif
                            </h4>
                            @if($plano->medidasSugeridas())
                                <small>Leída del rótulo del plano. Revise antes de calcular.</small>
                            @else
                                <small>Se requiere una medida verificada.</small>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Escala detectada --}}
                <div class="col-md-3">
                    <div class="card border-secondary shadow-sm mb-3">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Escala detectada</h6>
                            <h4 class="text-secondary">
                                {{ $resultado['escala'] ?? 'N/A' }}
                            </h4>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Mensaje informativo --}}
            <div class="alert alert-info">
                <i class="bi bi-info-circle-fill"></i>
                <strong>Información:</strong>
                {{ $resultado['mensaje'] ?? 'Análisis completado correctamente.' }}
            </div>

            @if(!empty($resultado['cotas_detectadas']))
                <div class="alert alert-secondary">
                    <strong><i class="bi bi-rulers"></i> Cotas leídas por OCR:</strong>
                    @foreach($resultado['cotas_detectadas'] as $cota)
                        <span class="badge bg-dark ms-1">
                            {{ number_format($cota['valor'], 2, ',', '.') }} {{ $cota['unidad'] === 'sin unidad' ? '' : $cota['unidad'] }}
                        </span>
                    @endforeach
                    <div class="small mt-2">
                        Estas cotas son una lectura automática. Confirme la longitud total de muros antes de calcular.
                    </div>
                </div>
            @endif

            {{-- Longitud guardada en el plano --}}
            @if($plano->longitud_total && $plano->longitud_total > 0)
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <strong>Longitud guardada:</strong>
                    {{ number_format($plano->longitud_total, 2) }} metros
                    <span class="badge bg-success ms-2">
                        <i class="bi bi-check"></i> Analizado
                    </span>
                </div>
            @endif

            {{-- Imagen analizada --}}
            @if(isset($resultado['imagen_analizada']))
                <div class="text-center mt-3">
                    <h5 class="mb-3">
                        <i class="bi bi-image-fill"></i>
                        Plano analizado
                    </h5>
                    <img src="{{ route('planos.archivo', ['plano' => $plano, 'analizado' => 1]) }}" 
                         alt="Plano analizado" 
                         class="img-fluid border rounded"
                         style="max-height: 500px;">
                </div>
            @endif

        </div>

    </div>

</div>

@endsection
