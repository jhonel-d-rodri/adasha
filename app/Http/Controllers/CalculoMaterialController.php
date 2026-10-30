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
     * Verificar que el usuario tenga acceso a un proyecto.
     */
    private function verificarAccesoProyecto(Proyecto $proyecto): void
    {
        $usuario = auth()->user();

        if ($usuario->role === 'admin' || $proyecto->user_id == $usuario->id) {
            return;
        }

        abort(403, 'No tienes permiso para acceder a los cálculos de este proyecto.');
    }


    /**
     * Mostrar los cálculos de materiales.
     */
    public function index(Request $request)
    {
        $usuario = auth()->user();
        $proyectoId = $request->route('proyecto_id');

        if ($proyectoId) {
            $proyectoSeleccionado = Proyecto::findOrFail($proyectoId);
            $this->verificarAccesoProyecto($proyectoSeleccionado);
        }

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

            if ($proyectoId) {
                $query->where('proyecto_id', $proyectoId);
            }

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

            if ($proyectoId) {
                $query->where('proyecto_id', $proyectoId);
            }

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
     * Mostrar la configuración estructural del plano.
     */
    public function configurar(Plano $plano)
    {
        $this->verificarAccesoPlano($plano);

        if (!$plano->procesado) {
            return redirect()->route('planos.index')->with(
                'error',
                'Primero debe analizar este plano antes de configurar los materiales.'
            );
        }

        return view('planos.configurar-materiales', compact('plano'));
    }

    /**
     * Calcular materiales referenciales de obra gruesa.
     */
    public function calcular(Request $request, Plano $plano)
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

        if (!$plano->procesado) {

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

        $datos = $request->validate([
            'longitud_total' => 'required|numeric|min:0.01',
            'plantas' => 'required|integer|min:1|max:2',
            'tipo_cubierta' => 'required|in:liviana,losa',
            'cimentacion' => 'nullable|boolean',
            'columnas' => 'nullable|boolean',
            'vigas' => 'nullable|boolean',
            'superficie_construida' => 'nullable|numeric|min:1',
            'area_vanos' => 'nullable|numeric|min:0',
        ]);

        if ($datos['tipo_cubierta'] === 'losa' && empty($datos['superficie_construida'])) {
            return back()->withErrors([
                'superficie_construida' => 'Indique el área construida para calcular la losa.'
            ])->withInput();
        }

        $areaBruta = (float) $datos['longitud_total'] * ($proyecto->altura_muro ?? 2.40) * $datos['plantas'];
        if (($datos['area_vanos'] ?? 0) >= $areaBruta) {
            return back()->withErrors(['area_vanos' => 'El área de puertas y ventanas debe ser menor al área total de muros.'])->withInput();
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $plano, $proyecto, $datos) {
            Plano::whereKey($plano->id)->lockForUpdate()->firstOrFail();
            $longitud = (float) $datos['longitud_total'];

            $plano->update([
                    'longitud_total' => $datos['longitud_total'],
                'plantas' => $datos['plantas'],
                'tipo_cubierta' => $datos['tipo_cubierta'],
                'cimentacion' => $request->boolean('cimentacion'),
                'columnas' => $request->boolean('columnas'),
                'vigas' => $request->boolean('vigas'),
                'superficie_construida' => $datos['superficie_construida'] ?? null,
                'area_vanos' => $datos['area_vanos'] ?? 0,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Características del proyecto
            |--------------------------------------------------------------------------
            */

            $altura =
                $proyecto->altura_muro ?? 2.40;

            $espesor =
                $proyecto->espesor_muro ?? 0.15;

            // Los cálculos trabajan internamente en metros. Corrige proyectos
            // antiguos que almacenaron el espesor en centímetros (ej. 15).
            $espesorFueConvertido = $espesor > 1;
            if ($espesorFueConvertido) {
                $espesor /= 100;
            }

            $largoLadrillo =
                $proyecto->largo_ladrillo ?? 0.24;

            $anchoLadrillo =
                $proyecto->ancho_ladrillo ?? 0.12;

            $altoLadrillo =
                $proyecto->alto_ladrillo ?? 0.09;

            $plantas = max(1, (int) $plano->plantas);

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

            if ($espesorFueConvertido) {

                $proyecto->espesor_muro = $espesor;

                $cambios[] = 'espesor del muro convertido de centímetros a metros';

            } elseif (is_null($proyecto->espesor_muro)) {

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

            // Descuenta los vanos confirmados por el usuario (puertas y ventanas).
            $areaMuroBruta =
                $longitud *
                $altura *
                $plantas;

            $areaVanos = (float) ($plano->area_vanos ?? 0);
            $areaMuro = $areaMuroBruta - $areaVanos;

            $volumenMuro =
                $areaMuro *
                $espesor;

            // Volumen de un ladrillo
            $volumenLadrillo =
                $largoLadrillo *
                $anchoLadrillo *
                $altoLadrillo;

            // Cada ladrillo considera una junta de mortero de 1 cm en largo y alto.
            // El desperdicio se aplica solo a la compra de ladrillos.
            $juntaMortero = 0.01;
            $areaModuloLadrillo =
                ($largoLadrillo + $juntaMortero) *
                ($altoLadrillo + $juntaMortero);

            $ladrillosSinDesperdicio =
                $areaMuro / $areaModuloLadrillo;

            $ladrillos =
                $ladrillosSinDesperdicio *
                1.10;

            // Mortero que ocupa las juntas, calculado como el volumen restante
            // después de descontar los ladrillos instalados (sin desperdicio).
            $volumenMortero =
                max(0, $volumenMuro - ($ladrillosSinDesperdicio * $volumenLadrillo));

            // Dosificación de mortero 1:4 (cemento:arena) y factor de volumen
            // seco de 1.33. La bolsa de cemento se aproxima a 0.035 m³.
            $volumenSecoMortero = $volumenMortero * 1.33;

            $cemento =
                (($volumenSecoMortero / 5) / 0.035) *
                1.10;

            // Arena para la misma dosificación, con 10% de merma.
            $arena =
                ($volumenSecoMortero * (4 / 5)) * 1.10;

            $volumenConcretoEstructural = 0;
            $volumenMorteroCimiento = 0;
            $piedra = 0;
            if ($plano->cimentacion) {
                $volumenCimiento = $longitud * 0.40 * 0.40 * 1.10;
                $volumenMorteroCimiento = $volumenCimiento * 0.20;
                $piedra = $volumenCimiento * 0.80;
                $cemento += (($volumenMorteroCimiento * 1.33 / 5) / 0.035) * 1.10;
                $arena += ($volumenMorteroCimiento * 1.33 * 4 / 5) * 1.10;
            }

            if ($plano->columnas) {
                $cantidadColumnas = max(4, (int) ceil($longitud / 4));
                $volumenConcretoEstructural += $cantidadColumnas * 0.20 * 0.20 * $altura * $plantas * 1.10;
            }

            if ($plano->vigas) {
                $volumenConcretoEstructural += $longitud * 0.20 * 0.20 * $plantas * 1.10;
            }

            $areaConstruida = (float) ($plano->superficie_construida ?? 0);
            if ($plano->tipo_cubierta === 'losa' && $areaConstruida > 0) {
                $volumenConcretoEstructural += $areaConstruida * 0.12 * 1.10;
            }

            $cemento += (($volumenConcretoEstructural * 1.54 / 6) / 0.035) * 1.10;
            $arena += ($volumenConcretoEstructural * 1.54 * 2 / 6) * 1.10;
            $grava = ($volumenConcretoEstructural * 1.54 * 3 / 6) * 1.10;
            $fierro = $volumenConcretoEstructural * 80;
            $alambre = $fierro * 0.02;
            $madera = $plano->tipo_cubierta === 'liviana'
                ? max(0, $areaConstruida ?: ($longitud * $longitud / 16)) * 0.03
                : 0;
            $clavos = $madera * 3;
            // El consumo se convierte de litros a metros cúbicos para coincidir
            // con la unidad almacenada del material Agua.
            $agua = (($volumenConcretoEstructural * 180) + (($volumenMortero + $volumenMorteroCimiento) * 180)) / 1000;

            /*
            |--------------------------------------------------------------------------
            | Buscar materiales
            |--------------------------------------------------------------------------
            */

            $obtenerMaterial = function (string $nombre, string $unidad, string $descripcion) {
                return Material::firstOrCreate(
                    ['nombre' => $nombre],
                    ['unidad' => $unidad, 'stock' => 0, 'descripcion' => $descripcion]
                );
            };

            $materiales = [
                $obtenerMaterial('Ladrillo Gambote', 'unidad', 'Ladrillo para mampostería.'),
                $obtenerMaterial('Cemento', 'bolsa', 'Cemento para mortero y concreto.'),
                $obtenerMaterial('Arena', 'm3', 'Arena para mortero y concreto.'),
                $obtenerMaterial('Grava', 'm3', 'Grava para concreto.'),
                $obtenerMaterial('Piedra para cimiento', 'm3', 'Piedra para cimentación.'),
                $obtenerMaterial('Fierro corrugado', 'kg', 'Acero de refuerzo referencial.'),
                $obtenerMaterial('Alambre de amarre', 'kg', 'Alambre para armado.'),
                $obtenerMaterial('Madera de encofrado', 'm3', 'Madera para encofrado o cubierta liviana.'),
                $obtenerMaterial('Clavos', 'kg', 'Clavos para encofrado o cubierta.'),
                $obtenerMaterial('Agua', 'm3', 'Agua para mortero y concreto.'),
            ];

            /*
            |--------------------------------------------------------------------------
            | Eliminar cálculos anteriores
            |--------------------------------------------------------------------------
            */

            CalculoMaterial::where(
                'plano_id',
                $plano->id
            )->delete();

            $cantidades = [
                $ladrillos, $cemento, $arena, $grava, $piedra, $fierro,
                $alambre, $madera, $clavos, $agua,
            ];

            foreach ($cantidades as $indice => $cantidad) {
                if ($cantidad <= 0) {
                    continue;
                }

                CalculoMaterial::create([
                    'proyecto_id' => $proyecto->id,
                    'plano_id' => $plano->id,
                    'material_id' => $materiales[$indice]->id,
                    'cantidad' => round($cantidad, $indice < 2 ? 0 : 2),
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Mensaje
            |--------------------------------------------------------------------------
            */

            $mensajeExito =
                'Los materiales referenciales de obra gruesa fueron calculados correctamente para el proyecto: '
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
        });
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
