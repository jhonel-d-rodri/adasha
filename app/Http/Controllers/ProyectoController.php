<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Plano;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ProyectoController extends Controller
{
    /**
     * Verificar si el usuario puede acceder al proyecto.
     *
     * ADMIN:
     * Puede acceder a cualquier proyecto.
     *
     * USUARIO:
     * Solo puede acceder a sus propios proyectos.
     */
    private function verificarAcceso(Proyecto $proyecto)
    {
        $usuario = auth()->user();

        // Administrador puede acceder a todo
        if ($usuario->role === 'admin') {
            return true;
        }

        // Usuario solamente puede acceder a sus propios proyectos
        if (
            $usuario->role === 'usuario' &&
            $proyecto->user_id == $usuario->id
        ) {
            return true;
        }

        abort(403, 'No tienes permiso para acceder a este proyecto.');
    }


    /**
     * Mostrar proyectos.
     *
     * ADMIN:
     * Muestra todos los proyectos.
     *
     * USUARIO:
     * Muestra solamente sus proyectos.
     */
    public function index()
    {
        $usuario = auth()->user();

        if ($usuario->role === 'admin') {

            // Administrador ve todos
            $proyectos = Proyecto::with('planos')
                ->latest()
                ->get();

        } else {

            // Usuario ve solamente sus proyectos
            $proyectos = Proyecto::with('planos')
                ->where('user_id', $usuario->id)
                ->latest()
                ->get();
        }

        return view('proyectos.index', compact('proyectos'));
    }


    /**
     * Mostrar formulario para crear proyecto.
     */
    public function create()
    {
        return view('proyectos.create');
    }


    /**
     * Geocodificar una dirección usando Nominatim / OpenStreetMap API.
     */
    public function geocodificar(Request $request)
    {
        $request->validate([
            'ubicacion' => 'required|string|max:255',
        ]);

        try {

            $ubicacion = trim($request->ubicacion);

            // Si no escribió Bolivia, la agregamos
            if (
                stripos($ubicacion, 'bolivia') === false
            ) {
                $ubicacion .= ', Cochabamba, Bolivia';
            }

            // Consultar Nominatim
            $respuesta = Http::withHeaders([
                'User-Agent' => 'Constructora-ADASHA/1.0 contacto@adasha.local',
                'Accept' => 'application/json',
                'Accept-Language' => 'es',
            ])
            ->timeout(15)
            ->get(
                'https://nominatim.openstreetmap.org/search',
                [
                    'q' => $ubicacion,
                    'format' => 'json',
                    'limit' => 5,
                    'addressdetails' => 1,
                    'countrycodes' => 'bo',
                ]
            );

            // Verificar respuesta
            if (!$respuesta->successful()) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'No fue posible consultar el servicio de geocodificación.',
                ], 500);
            }

            $resultados = $respuesta->json();

            // No encontró ubicación
            if (empty($resultados)) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'No se encontró la ubicación indicada. Pruebe escribiendo la dirección con ciudad y país, por ejemplo: Av. América, Cochabamba, Bolivia.',
                ], 404);
            }

            $resultado = $resultados[0];

            // Verificar coordenadas
            if (
                !isset($resultado['lat']) ||
                !isset($resultado['lon'])
            ) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'La ubicación encontrada no contiene coordenadas válidas.',
                ], 404);
            }

            return response()->json([

                'success' => true,

                'latitud' =>
                    (float) $resultado['lat'],

                'longitud' =>
                    (float) $resultado['lon'],

                'direccion' =>
                    $resultado['display_name']
                    ?? $ubicacion,

            ]);

        } catch (\Exception $e) {

            \Log::error(
                'Error de geocodificación ADASHA: ' .
                $e->getMessage()
            );

            return response()->json([
                'success' => false,
                'message' =>
                    'Ocurrió un error al consultar la ubicación.',
            ], 500);
        }
    }


    /**
     * Guardar un nuevo proyecto.
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Debe iniciar sesión para registrar un proyecto.'
                );
        }


        // Validar datos
        $request->validate([

            'nombre' =>
                'required|string|max:255',

            'cliente' =>
                'required|string|max:255',

            'ubicacion' =>
                'required|string|max:255',

            'latitud' =>
                'nullable|numeric',

            'longitud' =>
                'nullable|numeric',

            'fecha_inicio' =>
                'required|date',

            'estado' =>
                'required|string|in:Activo,En_proceso,Finalizado',

            'descripcion' =>
                'nullable|string',

            'altura_muro' =>
                'required|numeric|min:0.01',

            'espesor_muro' =>
                'required|numeric|min:0.001',

            'ladrillo_largo' =>
                'required|numeric|min:0.001',

            'ladrillo_ancho' =>
                'required|numeric|min:0.001',

            'ladrillo_alto' =>
                'required|numeric|min:0.001',

            'nombre_plano' =>
                'required|string|max:255',

            'archivo' =>
                'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);


        // Convertir estado
        $estadoMap = [

            'Activo' =>
                'registrado',

            'En_proceso' =>
                'en_proceso',

            'Finalizado' =>
                'finalizado',
        ];


        $estadoBD =
            $estadoMap[$request->estado]
            ?? 'registrado';


        // Usuario actualmente autenticado
        $usuarioId = auth()->id();


        if (!$usuarioId) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'No se pudo identificar al usuario. Inicie sesión nuevamente.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CREAR PROYECTO
        |--------------------------------------------------------------------------
        */

        $proyecto = Proyecto::create([

            'nombre' =>
                $request->nombre,

            'cliente' =>
                $request->cliente,

            'ubicacion' =>
                $request->ubicacion,

            'latitud' =>
                $request->latitud,

            'longitud' =>
                $request->longitud,

            'fecha_inicio' =>
                $request->fecha_inicio,

            'estado' =>
                $estadoBD,

            'descripcion' =>
                $request->descripcion,

            'altura_muro' =>
                $request->altura_muro,

            'espesor_muro' =>
                $request->espesor_muro,

            'largo_ladrillo' =>
                $request->ladrillo_largo,

            'ancho_ladrillo' =>
                $request->ladrillo_ancho,

            'alto_ladrillo' =>
                $request->ladrillo_alto,

            // IMPORTANTE:
            // El proyecto queda asociado al usuario que lo creó
            'user_id' =>
                $usuarioId,
        ]);


        /*
        |--------------------------------------------------------------------------
        | GUARDAR PLANO
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('archivo')) {

            $archivo =
                $request->file('archivo');

            $nombreArchivo =
                time() .
                '_' .
                $archivo->getClientOriginalName();

            $ruta =
                $archivo->storeAs(
                    'planos',
                    $nombreArchivo,
                    'public'
                );

            Plano::create([

                'proyecto_id' =>
                    $proyecto->id,

                'nombre' =>
                    $request->nombre_plano,

                'nombre_archivo' =>
                    $archivo->getClientOriginalName(),

                'ruta_archivo' =>
                    $ruta,

                'tipo_plano' =>
                    'otro',

                'procesado' =>
                    false,

                'datos_extraidos' =>
                    null,

                'longitud_total' =>
                    null,
            ]);
        }


        return redirect()
            ->route('proyectos.index')
            ->with(
                'success',
                'Proyecto y plano registrados correctamente.'
            );
    }


    /**
     * Mostrar un proyecto específico.
     */
    public function show(Proyecto $proyecto)
    {
        // Verificar propietario o administrador
        $this->verificarAcceso($proyecto);

        $proyecto->load('planos');

        return view(
            'proyectos.show',
            compact('proyecto')
        );
    }


    /**
     * Mostrar formulario para editar proyecto.
     */
    public function edit(Proyecto $proyecto)
    {
        // Verificar propietario o administrador
        $this->verificarAcceso($proyecto);

        $proyecto->load('planos');

        return view(
            'proyectos.edit',
            compact('proyecto')
        );
    }


    /**
     * Actualizar proyecto.
     */
    public function update(
        Request $request,
        Proyecto $proyecto
    ) {

        // Verificar propietario o administrador
        $this->verificarAcceso($proyecto);


        if (!auth()->check()) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Debe iniciar sesión para actualizar un proyecto.'
                );
        }


        // Validar datos
        $request->validate([

            'nombre' =>
                'required|string|max:255',

            'cliente' =>
                'required|string|max:255',

            'ubicacion' =>
                'required|string|max:255',

            'latitud' =>
                'nullable|numeric',

            'longitud' =>
                'nullable|numeric',

            'fecha_inicio' =>
                'required|date',

            'estado' =>
                'required|string|in:Activo,En_proceso,Finalizado',

            'descripcion' =>
                'nullable|string',

            'altura_muro' =>
                'required|numeric|min:0.01',

            'espesor_muro' =>
                'required|numeric|min:0.001',

            'ladrillo_largo' =>
                'required|numeric|min:0.001',

            'ladrillo_ancho' =>
                'required|numeric|min:0.001',

            'ladrillo_alto' =>
                'required|numeric|min:0.001',

            'archivo' =>
                'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);


        // Convertir estado
        $estadoMap = [

            'Activo' =>
                'registrado',

            'En_proceso' =>
                'en_proceso',

            'Finalizado' =>
                'finalizado',
        ];


        $estadoBD =
            $estadoMap[$request->estado]
            ?? 'registrado';


        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR PROYECTO
        |--------------------------------------------------------------------------
        */

        $proyecto->update([

            'nombre' =>
                $request->nombre,

            'cliente' =>
                $request->cliente,

            'ubicacion' =>
                $request->ubicacion,

            'latitud' =>
                $request->filled('latitud')
                    ? $request->latitud
                    : $proyecto->latitud,

            'longitud' =>
                $request->filled('longitud')
                    ? $request->longitud
                    : $proyecto->longitud,

            'fecha_inicio' =>
                $request->fecha_inicio,

            'estado' =>
                $estadoBD,

            'descripcion' =>
                $request->descripcion,

            'altura_muro' =>
                $request->altura_muro,

            'espesor_muro' =>
                $request->espesor_muro,

            'largo_ladrillo' =>
                $request->ladrillo_largo,

            'ancho_ladrillo' =>
                $request->ladrillo_ancho,

            'alto_ladrillo' =>
                $request->ladrillo_alto,
        ]);


        /*
        |--------------------------------------------------------------------------
        | NUEVO PLANO
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('archivo')) {

            $proyecto->load('planos');

            foreach (
                $proyecto->planos as $planoAnterior
            ) {

                if (
                    $planoAnterior->ruta_archivo &&
                    Storage::disk('public')->exists(
                        $planoAnterior->ruta_archivo
                    )
                ) {

                    Storage::disk('public')->delete(
                        $planoAnterior->ruta_archivo
                    );
                }

                $planoAnterior->delete();
            }


            $archivo =
                $request->file('archivo');

            $nombreArchivo =
                time() .
                '_' .
                $archivo->getClientOriginalName();

            $ruta =
                $archivo->storeAs(
                    'planos',
                    $nombreArchivo,
                    'public'
                );


            Plano::create([

                'proyecto_id' =>
                    $proyecto->id,

                'nombre' =>
                    $archivo->getClientOriginalName(),

                'nombre_archivo' =>
                    $archivo->getClientOriginalName(),

                'ruta_archivo' =>
                    $ruta,

                'tipo_plano' =>
                    'otro',

                'procesado' =>
                    false,

                'datos_extraidos' =>
                    null,

                'longitud_total' =>
                    null,
            ]);
        }


        return redirect()
            ->route('proyectos.index')
            ->with(
                'success',
                'Proyecto actualizado correctamente.'
            );
    }


    /**
     * Eliminar proyecto.
     */
    public function destroy(Proyecto $proyecto)
    {
        // Verificar propietario o administrador
        $this->verificarAcceso($proyecto);


        if (!auth()->check()) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Debe iniciar sesión para eliminar un proyecto.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | ELIMINAR PLANOS
        |--------------------------------------------------------------------------
        */

        $proyecto->load('planos');

        foreach (
            $proyecto->planos as $plano
        ) {

            if (
                $plano->ruta_archivo &&
                Storage::disk('public')->exists(
                    $plano->ruta_archivo
                )
            ) {

                Storage::disk('public')->delete(
                    $plano->ruta_archivo
                );
            }

            $plano->delete();
        }


        /*
        |--------------------------------------------------------------------------
        | ELIMINAR PROYECTO
        |--------------------------------------------------------------------------
        */

        $proyecto->delete();


        return redirect()
            ->route('proyectos.index')
            ->with(
                'success',
                'Proyecto eliminado correctamente.'
            );
    }
}