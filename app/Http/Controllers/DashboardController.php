<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Plano;
use App\Models\Material;
use App\Models\CalculoMaterial;
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

        $actividad = [
            0,
            0,
            0,
            0,
            0,
            0,
            0
        ];

        return view('dashboard', compact(
            'proyectosCount',
            'planosCount',
            'materialesCount',
            'reportesCount',
            'proyectosRecientes',
            'materialesRecientes',
            'actividad'
        ));
    }
}