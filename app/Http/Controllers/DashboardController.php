<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Plano;
use App\Models\Material;
use App\Models\CalculoMaterial;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | ADMINISTRADOR
        |--------------------------------------------------------------------------
        */

        if ($usuario->role === 'admin') {

            $proyectosCount = Proyecto::count();

            $planosCount = Plano::count();

            $materialesCount = Material::count();

            $reportesCount = CalculoMaterial::count();

            $proyectosRecientes = Proyecto::latest()
                ->take(5)
                ->get();

            $materialesRecientes = Material::latest()
                ->take(10)
                ->get();

        }

        /*
        |--------------------------------------------------------------------------
        | USUARIO
        |--------------------------------------------------------------------------
        */

        else {

            $proyectosCount = Proyecto::where(
                'user_id',
                $usuario->id
            )->count();

            $planosCount = Plano::whereHas('proyecto', function ($query) use ($usuario) {
                $query->where('user_id', $usuario->id);
            })->count();

            $materialesCount = Material::count();

            $reportesCount = CalculoMaterial::whereHas(
                'plano.proyecto',
                function ($query) use ($usuario) {
                    $query->where('user_id', $usuario->id);
                }
            )->count();

            $proyectosRecientes = Proyecto::where(
                'user_id',
                $usuario->id
            )
                ->latest()
                ->take(5)
                ->get();

            $materialesRecientes = Material::latest()
                ->take(10)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | ACTIVIDAD DEL GRÁFICO
        |--------------------------------------------------------------------------
        */

        $inicioSemana = now()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $finSemana = $inicioSemana->copy()->addWeek();
        $periodoActividad = $inicioSemana->format('d/m/Y') . ' - '
            . $finSemana->copy()->subDay()->format('d/m/Y');
        $actividad = array_fill(0, 7, 0);

        $consultas = [
            Proyecto::query(),
            Plano::query(),
            CalculoMaterial::query(),
        ];

        if ($usuario->role !== 'admin') {
            $consultas[0]->where('user_id', $usuario->id);
            $consultas[1]->whereHas('proyecto', function ($query) use ($usuario) {
                $query->where('user_id', $usuario->id);
            });
            $consultas[2]->whereHas('plano.proyecto', function ($query) use ($usuario) {
                $query->where('user_id', $usuario->id);
            });
        }

        foreach ($consultas as $consulta) {
            $registrosPorDia = $consulta
                ->where('created_at', '>=', $inicioSemana)
                ->where('created_at', '<', $finSemana)
                ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
                ->groupByRaw('DATE(created_at)')
                ->get();

            foreach ($registrosPorDia as $registro) {
                $indice = Carbon::parse($registro->fecha)->dayOfWeekIso - 1;
                $actividad[$indice] += (int) $registro->total;
            }
        }

        return view('dashboard', compact(
            'proyectosCount',
            'planosCount',
            'materialesCount',
            'reportesCount',
            'proyectosRecientes',
            'materialesRecientes',
            'actividad',
            'periodoActividad'
        ));
    }
}
