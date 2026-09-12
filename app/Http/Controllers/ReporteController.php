<?php

namespace App\Http\Controllers;

use App\Models\Plano;
use App\Models\CalculoMaterial;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    /**
     * Mostrar lista de planos con cálculos disponibles.
     */
    public function index()
    {
        // Obtener todos los planos que tengan al menos un cálculo
        $planos = Plano::whereHas('calculos')
            ->with('proyecto')
            ->latest()
            ->get();

        return view('reportes.index', compact('planos'));
    }

    /**
     * Mostrar el reporte en pantalla (con layout de la app).
     */
    public function materiales(Plano $plano)
    {
        $proyecto = $plano->proyecto;

        $calculos = CalculoMaterial::where('plano_id', $plano->id)
            ->with('material')
            ->latest('id')
            ->take(5)
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
        $proyecto = $plano->proyecto;

        $calculos = CalculoMaterial::where('plano_id', $plano->id)
            ->with('material')
            ->latest('id')
            ->take(5)
            ->get();

        if ($calculos->isEmpty()) {
            return redirect()
                ->route('reportes.index')
                ->with('error', 'No existen cálculos para generar el PDF.');
        }

        $pdf = Pdf::loadView('reportes.pdf', compact('plano', 'proyecto', 'calculos'));

        return $pdf->download('reporte_materiales_' . $plano->id . '.pdf');
    }
}