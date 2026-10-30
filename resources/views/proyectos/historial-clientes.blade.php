@extends('layouts.app')

@section('content')
<div class="container-fluid client-history-page">
    <div class="history-hero mb-4">
        <div>
            <span class="history-eyebrow"><i class="bi bi-people-fill"></i> Relaciones comerciales</span>
            <h2 class="page-title mb-1">Historial de clientes</h2>
            <p class="mb-0 text-muted">{{ auth()->user()->isAdministrador() ? 'Todos los clientes y proyectos registrados en el sistema.' : 'Consulta los clientes asociados a tus proyectos.' }}</p>
        </div>
        <a href="{{ route('proyectos.index') }}" class="btn btn-primary"><i class="bi bi-folder-plus me-2"></i>Gestionar proyectos</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3"><div class="history-stat"><i class="bi bi-people history-stat-icon"></i><div><small>Clientes registrados</small><strong>{{ $historial->pluck('cliente')->filter()->unique()->count() }}</strong></div></div></div>
        <div class="col-sm-6 col-lg-3"><div class="history-stat"><i class="bi bi-folder2-open history-stat-icon"></i><div><small>Proyectos</small><strong>{{ $historial->count() }}</strong></div></div></div>
        <div class="col-sm-6 col-lg-3"><div class="history-stat"><i class="bi bi-hourglass-split history-stat-icon"></i><div><small>En proceso</small><strong>{{ $historial->filter(fn ($p) => $p->estado_actual === 'en_proceso')->count() }}</strong></div></div></div>
        <div class="col-sm-6 col-lg-3"><div class="history-stat"><i class="bi bi-check2-circle history-stat-icon"></i><div><small>Finalizados</small><strong>{{ $historial->filter(fn ($p) => $p->estado_actual === 'finalizado')->count() }}</strong></div></div></div>
    </div>

    <div class="card history-card">
        <div class="card-header history-card-header"><div><h5 class="mb-1">Registro de atenciones</h5><small>Busca rápidamente por cliente, proyecto o responsable.</small></div><label class="history-search"><i class="bi bi-search"></i><input id="history-search" type="search" placeholder="Buscar en el historial"></label></div>
        <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle mb-0 history-table"><thead><tr><th>Cliente</th><th>Proyecto</th><th>Fecha</th><th>Responsable</th><th>Estado</th><th></th></tr></thead><tbody>
        @forelse($historial as $registro)
            @php($estado = ['registrado' => ['Comienzo','success','play-circle'], 'en_proceso' => ['En proceso','warning','hourglass-split'], 'finalizado' => ['Finalizado','danger','check-circle-fill']][$registro->estado_actual] ?? [ucfirst(str_replace('_',' ',$registro->estado_actual)),'secondary','circle'])
            <tr class="history-row"><td><div class="client-cell"><span class="client-initial">{{ strtoupper(mb_substr($registro->cliente ?: 'S', 0, 1)) }}</span><div><strong>{{ $registro->cliente ?: 'Sin cliente' }}</strong><small>{{ $registro->ubicacion ?: 'Sin ubicación registrada' }}</small></div></div></td><td><i class="bi bi-building text-muted me-2"></i>{{ $registro->nombre ?: '—' }}</td><td>{{ optional($registro->created_at)->format('d/m/Y') ?? '—' }}</td><td>{{ $registro->usuario?->name ?? 'Sistema' }}</td><td><span class="status-pill {{ $estado[1] }}"><i class="bi bi-{{ $estado[2] }}"></i>{{ $estado[0] }}</span></td><td class="text-end"><a class="btn btn-outline-primary btn-sm" href="{{ route('proyectos.show', $registro) }}" title="Ver proyecto"><i class="bi bi-arrow-up-right"></i></a></td></tr>
        @empty
            <tr><td colspan="6"><div class="history-empty"><i class="bi bi-people"></i><strong>Aún no hay clientes en el historial.</strong><span>Los clientes aparecerán al registrar un proyecto.</span></div></td></tr>
        @endforelse
        </tbody></table></div><div id="history-no-results" class="history-empty d-none"><i class="bi bi-search"></i><strong>No se encontraron coincidencias.</strong></div></div>
    </div>
</div>
@endsection

@push('scripts')
<script>document.getElementById('history-search')?.addEventListener('input', function(){let visible=0, term=this.value.toLowerCase().trim(); document.querySelectorAll('.history-row').forEach(row=>{let match=row.textContent.toLowerCase().includes(term); row.classList.toggle('d-none',!match); visible+=match}); document.getElementById('history-no-results')?.classList.toggle('d-none',visible>0||!term)});</script>
@endpush
