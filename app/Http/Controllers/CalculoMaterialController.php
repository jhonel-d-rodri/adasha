<?php

namespace App\Http\Controllers;

use App\Models\CalculoMaterial;
use App\Models\Material;
use App\Models\Plano;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CalculoMaterialController extends Controller
{
    /**
     * Verificar que el usuario tenga acceso al plano.
     *
     * ADMIN:
     * Puede acceder a cualquier plano.
     *
     * USUARIO:
     * Solo puede acceder a planos de sus propios proyectos.
     */
    private function verificarAccesoPlano(Plano $plano)
    {
        $usuario = auth()->user();

        $plano->loadMissing('proyecto');

        if ($usuario->role === 'admin') {
            return true;
        }

        if (
            $usuario->role === 'usuario' &&
            $plano->proyecto &&
            $plano->proyecto->user_id == $usuario->id
        ) {
            return true;
        }

        abort(
            403,
            'No tienes permiso para acceder a los cálculos de este plano.'
        );
    }


    /**
     * Mostrar los cálculos de materiales.
     */
    public function index(Request $request)
    {
        $usuario = auth()->user();

        $query = CalculoMaterial::with([
            'proyecto',
            'plano',
            'material'
        ]);

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        | Puede ver todos los cálculos.
        */

        if ($usuario->role === 'admin') {

            if ($request->filled('plano_id')) {
                $query->where(
                    'plano_id',
                    $request->plano_id
                );
            }

            $calculos = $query
                ->latest('id')
                ->get();

            $proyectos = Proyecto::latest()->get();

            $planos = Plano::with('proyecto')
                ->latest()
                ->get();

        }

        /*
        |--------------------------------------------------------------------------
        | USUARIO
        |--------------------------------------------------------------------------
        | Solo puede ver cálculos de sus propios proyectos.
        */

        else {

            $query->whereHas(
                'proyecto',
                function ($q) use ($usuario) {
                    $q->where(
                        'user_id',
                        $usuario->id
                    );
                }
            );

            if ($request->filled('plano_id')) {

                $plano = Plano::findOrFail(
                    $request->plano_id
                );

                $this->verificarAccesoPlano($plano);

                $query->where(
                    'plano_id',
                    $request->plano_id
                );
            }

            $calculos = $query
                ->latest('id')
                ->get();

            $proyectos = Proyecto::where(
                'user_id',
                $usuario->id
            )
            ->latest()
            ->get();

            $planos = Plano::with('proyecto')
                ->whereHas(
                    'proyecto',
                    function ($q) use ($usuario) {
                        $q->where(
                            'user_id',
                            $usuario->id
                        );
                    }
                )
                ->latest()
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Formatear resultados
        |--------------------------------------------------------------------------
        */

        $calculos = $this->formatearResultados(
            $calculos
        );

        /*
        |--------------------------------------------------------------------------
        | Materiales
        |--------------------------------------------------------------------------
        */

        $materials = Material::all();

        return view(
            'calculos.index',
            compact(
                'calculos',
                'proyectos',
                'planos',
                'materials'
            )
        );
    }


    /**
     * Calcular automáticamente los 5 materiales.
     */
    public function calcular(Plano $plano)
    {
        /*
        |--------------------------------------------------------------------------
        | Verificar acceso
        |--------------------------------------------------------------------------
        */

        $this->verificarAccesoPlano($plano);

        /*
        |--------------------------------------------------------------------------
        | Obtener longitud detectada
        |--------------------------------------------------------------------------
        */

        $longitud = $plano->longitud_total;

        if (!$longitud || $longitud <= 0) {

            return redirect()
                ->route('planos.index')
                ->with(
                    'error',
                    'Primero debe analizar el plano antes de calcular los materiales.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Obtener proyecto asociado
        |--------------------------------------------------------------------------
        */

        $proyecto = $plano->proyecto;

        if (!$proyecto) {

            return redirect()
                ->route('planos.index')
                ->with(
                    'error',
                    'El plano no tiene un proyecto asociado.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Características del proyecto
        |--------------------------------------------------------------------------
        */

        $altura =
            $proyecto->altura_muro ?? 2.40;

        $espesor =
            $proyecto->espesor_muro ?? 0.15;

        $largoLadrillo =
            $proyecto->largo_ladrillo ?? 0.24;

        $anchoLadrillo =
            $proyecto->ancho_ladrillo ?? 0.12;

        $altoLadrillo =
            $proyecto->alto_ladrillo ?? 0.09;

        /*
        |--------------------------------------------------------------------------
        | Guardar valores predeterminados
        |--------------------------------------------------------------------------
        */

        $cambios = [];

        if (is_null($proyecto->altura_muro)) {

            $proyecto->altura_muro = $altura;

            $cambios[] = 'altura del muro';
        }

        if (is_null($proyecto->espesor_muro)) {

            $proyecto->espesor_muro = $espesor;

            $cambios[] = 'espesor del muro';
        }

        if (is_null($proyecto->largo_ladrillo)) {

            $proyecto->largo_ladrillo = $largoLadrillo;

            $cambios[] = 'largo del ladrillo';
        }

        if (is_null($proyecto->ancho_ladrillo)) {

            $proyecto->ancho_ladrillo = $anchoLadrillo;

            $cambios[] = 'ancho del ladrillo';
        }

        if (is_null($proyecto->alto_ladrillo)) {

            $proyecto->alto_ladrillo = $altoLadrillo;

            $cambios[] = 'alto del ladrillo';
        }

        if (!empty($cambios)) {

            $proyecto->save();

            $mensajePredeterminado =
                'Se asignaron valores predeterminados para: '
                . implode(', ', $cambios)
                . '.';

        } else {

            $mensajePredeterminado = null;
        }

        /*
        |--------------------------------------------------------------------------
        | CÁLCULOS
        |--------------------------------------------------------------------------
        */

        // Volumen total del muro
        $volumenMuro =
            $longitud *
            $altura *
            $espesor;

        // Volumen de un ladrillo
        $volumenLadrillo =
            $largoLadrillo *
            $anchoLadrillo *
            $altoLadrillo;

        // Cantidad de ladrillos + 10% desperdicio
        $ladrillos =
            ($volumenMuro / $volumenLadrillo) *
            1.10;

        // Volumen estimado de mortero
        $volumenMortero =
            $volumenMuro *
            0.20;

        // Cemento
        $cemento =
            ($volumenMortero / 0.035) *
            1.10;

        // Arena
        $arena =
            $volumenMortero *
            1.20;

        // Grava
        $grava =
            $volumenMuro *
            0.08;

        // Fierro
        $fierro =
            $longitud *
            8;

        /*
        |--------------------------------------------------------------------------
        | Buscar materiales
        |--------------------------------------------------------------------------
        */

        $materialLadrillo = Material::where(
            'nombre',
            'LIKE',
            '%Ladrillo%'
        )->first();

        $materialCemento = Material::where(
            'nombre',
            'LIKE',
            '%Cemento%'
        )->first();

        $materialArena = Material::where(
            'nombre',
            'LIKE',
            '%Arena%'
        )->first();

        $materialGrava = Material::where(
            'nombre',
            'LIKE',
            '%Grava%'
        )->first();

        $materialFierro = Material::where(
            'nombre',
            'LIKE',
            '%Fierro%'
        )->first();

        /*
        |--------------------------------------------------------------------------
        | Crear materiales si no existen
        |--------------------------------------------------------------------------
        */

        if (!$materialLadrillo) {

            $materialLadrillo = Material::create([
                'nombre' => 'Ladrillo Común',
                'unidad' => 'unidades',
                'stock' => 0,
            ]);
        }

        if (!$materialCemento) {

            $materialCemento = Material::create([
                'nombre' => 'Cemento Portland',
                'unidad' => 'bolsas',
                'stock' => 0,
            ]);
        }

        if (!$materialArena) {

            $materialArena = Material::create([
                'nombre' => 'Arena Fina',
                'unidad' => 'm3',
                'stock' => 0,
            ]);
        }

        if (!$materialGrava) {

            $materialGrava = Material::create([
                'nombre' => 'Grava',
                'unidad' => 'm3',
                'stock' => 0,
            ]);
        }

        if (!$materialFierro) {

            $materialFierro = Material::create([
                'nombre' => 'Fierro Corrugado',
                'unidad' => 'kg',
                'stock' => 0,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Eliminar cálculos anteriores
        |--------------------------------------------------------------------------
        */

        CalculoMaterial::where(
            'plano_id',
            $plano->id
        )->delete();

        /*
        |--------------------------------------------------------------------------
        | Guardar ladrillos
        |--------------------------------------------------------------------------
        */

        CalculoMaterial::create([
            'proyecto_id' => $proyecto->id,
            'plano_id' => $plano->id,
            'material_id' => $materialLadrillo->id,
            'cantidad' => round($ladrillos, 0)
        ]);

        /*
        |--------------------------------------------------------------------------
        | Guardar cemento
        |--------------------------------------------------------------------------
        */

        CalculoMaterial::create([
            'proyecto_id' => $proyecto->id,
            'plano_id' => $plano->id,
            'material_id' => $materialCemento->id,
            'cantidad' => round($cemento, 0)
        ]);

        /*
        |--------------------------------------------------------------------------
        | Guardar arena
        |--------------------------------------------------------------------------
        */

        CalculoMaterial::create([
            'proyecto_id' => $proyecto->id,
            'plano_id' => $plano->id,
            'material_id' => $materialArena->id,
            'cantidad' => round($arena, 2)
        ]);

        /*
        |--------------------------------------------------------------------------
        | Guardar grava
        |--------------------------------------------------------------------------
        */

        CalculoMaterial::create([
            'proyecto_id' => $proyecto->id,
            'plano_id' => $plano->id,
            'material_id' => $materialGrava->id,
            'cantidad' => round($grava, 2)
        ]);

        /*
        |--------------------------------------------------------------------------
        | Guardar fierro
        |--------------------------------------------------------------------------
        */

        CalculoMaterial::create([
            'proyecto_id' => $proyecto->id,
            'plano_id' => $plano->id,
            'material_id' => $materialFierro->id,
            'cantidad' => round($fierro, 2)
        ]);

        /*
        |--------------------------------------------------------------------------
        | Mensaje
        |--------------------------------------------------------------------------
        */

        $mensajeExito =
            'Los 5 materiales fueron calculados correctamente para el proyecto: '
            . $proyecto->nombre;

        if ($mensajePredeterminado) {

            $mensajeExito .= ' '
                . $mensajePredeterminado;
        }

        /*
        |--------------------------------------------------------------------------
        | Regresar
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'calculos.index',
                ['plano_id' => $plano->id]
            )
            ->with(
                'success',
                $mensajeExito
            );
    }


    /**
     * Registrar cálculo manualmente.
     */
    public function store(Request $request)
    {
        $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'plano_id' => 'required|exists:planos,id',
            'material_id' => 'required|exists:materials,id',
            'cantidad' => 'required|numeric|min:0'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Verificar proyecto
        |--------------------------------------------------------------------------
        */

        $proyecto = Proyecto::findOrFail(
            $request->proyecto_id
        );

        $usuario = auth()->user();

        if (
            $usuario->role !== 'admin' &&
            $proyecto->user_id != $usuario->id
        ) {
            abort(
                403,
                'No tienes permiso para registrar cálculos en este proyecto.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Verificar plano
        |--------------------------------------------------------------------------
        */

        $plano = Plano::findOrFail(
            $request->plano_id
        );

        $this->verificarAccesoPlano($plano);

        /*
        |--------------------------------------------------------------------------
        | Verificar que el plano pertenezca al proyecto
        |--------------------------------------------------------------------------
        */

        if ($plano->proyecto_id != $proyecto->id) {

            abort(
                403,
                'El plano no pertenece al proyecto seleccionado.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Registrar cálculo
        |--------------------------------------------------------------------------
        */

        CalculoMaterial::create([
            'proyecto_id' => $proyecto->id,
            'plano_id' => $plano->id,
            'material_id' => $request->material_id,
            'cantidad' => $request->cantidad
        ]);

        return redirect()
            ->route('calculos.index')
            ->with(
                'success',
                'Cálculo registrado correctamente.'
            );
    }


    /**
     * Eliminar un cálculo.
     */
    public function destroy($id)
    {
        $calculo = CalculoMaterial::with([
            'proyecto',
            'plano'
        ])->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Verificar acceso
        |--------------------------------------------------------------------------
        */

        if ($calculo->plano) {

            $this->verificarAccesoPlano(
                $calculo->plano
            );

        } elseif ($calculo->proyecto) {

            $usuario = auth()->user();

            if (
                $usuario->role !== 'admin' &&
                $calculo->proyecto->user_id != $usuario->id
            ) {
                abort(
                    403,
                    'No tienes permiso para eliminar este cálculo.'
                );
            }

        } else {

            abort(
                403,
                'No se puede verificar la propiedad del cálculo.'
            );
        }

        $calculo->delete();

        return redirect()
            ->route('calculos.index')
            ->with(
                'success',
                'Material eliminado correctamente.'
            );
    }


    /**
     * Mostrar vista previa del reporte.
     */
    public function reporte(Plano $plano)
    {
        /*
        |--------------------------------------------------------------------------
        | Verificar acceso
        |--------------------------------------------------------------------------
        */

        $this->verificarAccesoPlano($plano);

        /*
        |--------------------------------------------------------------------------
        | Obtener cálculos
        |--------------------------------------------------------------------------
        */

        $calculos = CalculoMaterial::with([
            'proyecto',
            'plano',
            'material'
        ])
            ->where(
                'plano_id',
                $plano->id
            )
            ->latest('id')
            ->take(5)
            ->get()
            ->sortBy('id')
            ->values();

        if ($calculos->isEmpty()) {

            return redirect()
                ->route('calculos.index')
                ->with(
                    'error',
                    'No existen resultados para generar el reporte.'
                );
        }

        $calculos =
            $this->formatearResultados(
                $calculos
            );

        return view(
            'calculos.reporte',
            compact(
                'calculos',
                'plano'
            )
        );
    }


    /**
     * Descargar reporte PDF.
     */
    public function descargarReporte(Plano $plano)
    {
        /*
        |--------------------------------------------------------------------------
        | Verificar acceso
        |--------------------------------------------------------------------------
        */

        $this->verificarAccesoPlano($plano);

        /*
        |--------------------------------------------------------------------------
        | Obtener cálculos
        |--------------------------------------------------------------------------
        */

        $calculos = CalculoMaterial::with([
            'proyecto',
            'plano',
            'material'
        ])
            ->where(
                'plano_id',
                $plano->id
            )
            ->latest('id')
            ->take(5)
            ->get()
            ->sortBy('id')
            ->values();

        if ($calculos->isEmpty()) {

            return redirect()
                ->route('calculos.index')
                ->with(
                    'error',
                    'No existen resultados para generar el reporte.'
                );
        }

        $calculos =
            $this->formatearResultados(
                $calculos
            );

        $pdf = Pdf::loadView(
            'calculos.reporte',
            compact(
                'calculos',
                'plano'
            )
        );

        return $pdf->download(
            'reporte_materiales_'
            . $plano->id
            . '.pdf'
        );
    }


    /**
     * Formatear unidades y cantidades.
     */
    private function formatearResultados($calculos)
    {
        return $calculos->map(function ($calculo) {

            $unidad =
                $calculo->material->unidad ?? '';

            /*
            |--------------------------------------------------------------------------
            | Formato de unidades
            |--------------------------------------------------------------------------
            */

            $unidad = match ($unidad) {

                'm3' => 'm³',

                'kg' => 'kg',

                'bolsa' => 'bolsas',

                'bolsas' => 'bolsas',

                'unidad' => 'unidades',

                'unidades' => 'unidades',

                default => $unidad
            };

            $calculo->material->unidad =
                $unidad;

            /*
            |--------------------------------------------------------------------------
            | Formato de cantidades
            |--------------------------------------------------------------------------
            */

            $nombreMaterial =
                $calculo->material->nombre ?? '';

            $cantidad =
                $calculo->cantidad;

            if (
                str_contains(
                    $nombreMaterial,
                    'Ladrillo'
                ) ||
                str_contains(
                    $nombreMaterial,
                    'Cemento'
                )
            ) {

                $calculo->cantidad_formateada =
                    number_format(
                        $cantidad,
                        0,
                        ',',
                        '.'
                    );

            } else {

                $calculo->cantidad_formateada =
                    number_format(
                        $cantidad,
                        2,
                        ',',
                        '.'
                    );
            }

            return $calculo;
        });
    }
}