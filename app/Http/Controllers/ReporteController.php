<?php

namespace App\Http\Controllers;

use App\Models\Plano;
use App\Models\CalculoMaterial;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    /**
     * Garantiza que un usuario solo consulte reportes de sus proyectos.
     */
    private function verificarAccesoPlano(Plano $plano): void
    {
        $plano->loadMissing('proyecto');
        $usuario = auth()->user();

        if ($usuario->role === 'admin') {
            return;
        }

        if ($plano->proyecto && $plano->proyecto->user_id === $usuario->id) {
            return;
        }

        abort(403, 'No tienes permiso para acceder al reporte de este plano.');
    }

    /**
     * Mostrar lista de planos con cálculos disponibles.
     */
    public function index()
    {
        // Obtener todos los planos que tengan al menos un cálculo
        $usuario = auth()->user();

        $query = Plano::whereHas('calculos')->with('proyecto');

        if ($usuario->role !== 'admin') {
            $query->whereHas('proyecto', function ($query) use ($usuario) {
                $query->where('user_id', $usuario->id);
            });
        }

        $planos = $query->latest()->get();

        return view('reportes.index', compact('planos'));
    }

    /**
     * Mostrar el reporte en pantalla (con layout de la app).
     */
    public function materiales(Plano $plano)
    {
        $this->verificarAccesoPlano($plano);

        $proyecto = $plano->proyecto;

        $calculos = CalculoMaterial::where('plano_id', $plano->id)
            ->with('material')
            ->latest('id')
            ->get();

        if ($calculos->isEmpty()) {
            return redirect()
                ->route('reportes.index')
                ->with('error', 'No existen cálculos de materiales para este plano.');
        }

        return view('reportes.materiales', compact('plano', 'proyecto', 'calculos'));
    }

    /**
     * Descargar el reporte en PDF (sin layout).
     */
    public function descargarPdf(Plano $plano)
    {
        $this->verificarAccesoPlano($plano);

        $proyecto = $plano->proyecto;

        $calculos = CalculoMaterial::where('plano_id', $plano->id)
            ->with('material')
            ->latest('id')
            ->get();

        if ($calculos->isEmpty()) {
            return redirect()
                ->route('reportes.index')
                ->with('error', 'No existen cálculos para generar el PDF.');
        }

        $pdf = Pdf::loadView('reportes.pdf', compact('plano', 'proyecto', 'calculos'));

        $nombreCliente = preg_replace('/[^A-Za-z0-9\-_]+/', '_', trim((string) ($proyecto->cliente ?? 'cliente')));
        $nombreCliente = trim($nombreCliente, '_');

        return $pdf->download('reporte_materiales_' . ($nombreCliente ?: 'cliente') . '_' . $plano->id . '.pdf');
    }
}
