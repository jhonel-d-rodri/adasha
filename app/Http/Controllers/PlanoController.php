<?php

namespace App\Http\Controllers;

use App\Models\Plano;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class PlanoController extends Controller
{
    /**
     * Verificar que el usuario tenga permiso sobre el plano.
     *
     * ADMIN:
     * Puede acceder a cualquier plano.
     *
     * USUARIO:
     * Solo puede acceder a planos de sus propios proyectos.
     */
    private function verificarAcceso(Plano $plano)
    {
        $usuario = auth()->user();

        $plano->loadMissing('proyecto');

        // El administrador puede acceder a todo
        if ($usuario->role === 'admin') {
            return true;
        }

        // El usuario solo puede acceder a sus propios proyectos
        if (
            $usuario->role === 'usuario' &&
            $plano->proyecto &&
            $plano->proyecto->user_id == $usuario->id
        ) {
            return true;
        }

        abort(
            403,
            'No tienes permiso para acceder a este plano.'
        );
    }


    /**
     * Mostrar todos los planos registrados
     */
    public function archivo(Plano $plano, Request $request)
    {
        $this->verificarAcceso($plano);
        $ruta = $plano->ruta_archivo;
        if ($request->boolean('analizado')) {
            $ruta = pathinfo($ruta, PATHINFO_DIRNAME) . '/' . pathinfo($ruta, PATHINFO_FILENAME) . '_analizado.png';
        }
        abort_unless($ruta && Storage::disk('planos')->exists($ruta), 404);
        return Storage::disk('planos')->response($ruta, null, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function index()
    {
        $usuario = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        | Puede ver todos los planos y todos los proyectos.
        */

        if ($usuario->role === 'admin') {

            $planos = Plano::with('proyecto')
                ->latest()
                ->get();

            $proyectos = Proyecto::latest()->get();

        }

        /*
        |--------------------------------------------------------------------------
        | USUARIO
        |--------------------------------------------------------------------------
        | Solo puede ver los planos de sus propios proyectos.
        */

        else {

            $planos = Plano::with('proyecto')
                ->whereHas('proyecto', function ($query) use ($usuario) {
                    $query->where('user_id', $usuario->id);
                })
                ->latest()
                ->get();

            $proyectos = Proyecto::where(
                'user_id',
                $usuario->id
            )
            ->latest()
            ->get();
        }

        return view(
            'planos.index',
            compact('planos', 'proyectos')
        );
    }


    /**
     * Registrar un nuevo plano
     */
    public function store(Request $request)
    {
        $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'nombre' => 'required|string|max:255',
            'archivo' => 'required|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Buscar proyecto
        |--------------------------------------------------------------------------
        */

        $proyecto = Proyecto::findOrFail(
            $request->proyecto_id
        );

        /*
        |--------------------------------------------------------------------------
        | Verificar propietario del proyecto
        |--------------------------------------------------------------------------
        */

        $usuario = auth()->user();

        if (
            $usuario->role !== 'admin' &&
            $proyecto->user_id != $usuario->id
        ) {
            abort(
                403,
                'No tienes permiso para agregar un plano a este proyecto.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Obtener archivo
        |--------------------------------------------------------------------------
        */

        $archivo = $request->file('archivo');

        /*
        |--------------------------------------------------------------------------
        | Generar nombre único
        |--------------------------------------------------------------------------
        */

        $nombreArchivo = \Illuminate\Support\Str::uuid()->toString()
            . '_'
            . $archivo->getClientOriginalName();

        /*
        |--------------------------------------------------------------------------
        | Guardar archivo
        |--------------------------------------------------------------------------
        */

        $ruta = $archivo->storeAs(
            'planos',
            $nombreArchivo,
            'planos'
        );

        /*
        |--------------------------------------------------------------------------
        | Registrar plano
        |--------------------------------------------------------------------------
        */

        Plano::create([
            'proyecto_id' => $proyecto->id,
            'nombre' => $request->nombre,
            'nombre_archivo' => $archivo->getClientOriginalName(),
            'ruta_archivo' => $ruta,
            'tipo_plano' => 'otro',
            'procesado' => false,
            'datos_extraidos' => null,
            'longitud_total' => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Redireccionar
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('planos.index')
            ->with(
                'success',
                'Plano registrado correctamente en el proyecto: '
                . $proyecto->nombre
            );
    }


    /**
     * Analizar plano utilizando Python + OpenCV
     */
    public function analizar(Plano $plano)
    {
        /*
        |--------------------------------------------------------------------------
        | Verificar acceso
        |--------------------------------------------------------------------------
        */

        $this->verificarAcceso($plano);
        if (!$plano->esImagen()) {
            return back()->with('error', 'Para analizar un plano, utilice una imagen JPG o PNG.');
        }

        /*
        |--------------------------------------------------------------------------
        | Verificar archivo
        |--------------------------------------------------------------------------
        */

        if (
            !$plano->ruta_archivo ||
            !Storage::disk('planos')->exists(
                $plano->ruta_archivo
            )
        ) {
            return redirect()
                ->route('planos.index')
                ->with(
                    'error',
                    'No se encontró el archivo del plano.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Obtener ruta física
        |--------------------------------------------------------------------------
        */

        $ruta = Storage::disk('planos')->path($plano->ruta_archivo);

        if (!is_file($ruta) || !is_readable($ruta)) {
            return redirect()
                ->route('planos.index')
                ->with(
                    'error',
                    'El archivo del plano no existe o no se puede leer: '
                    . $plano->nombre_archivo
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Configuración de Python
        |--------------------------------------------------------------------------
        */

        $python = env(
            'PYTHON_PATH',
            'python'
        );

        $script = base_path(
            'python/analizar_plano.py'
        );

        /*
        |--------------------------------------------------------------------------
        | Verificar script
        |--------------------------------------------------------------------------
        */

        if (!file_exists($script)) {
            return redirect()
                ->route('planos.index')
                ->with(
                    'error',
                    'No se encontró el archivo Python: python/analizar_plano.py'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Ejecutar Python
        |--------------------------------------------------------------------------
        */

        $process = new Process([
            $python,
            $script,
            $ruta
        ]);

        $process->setTimeout(120);

        $process->run();

        /*
        |--------------------------------------------------------------------------
        | Verificar ejecución
        |--------------------------------------------------------------------------
        */

        if (!$process->isSuccessful()) {
            $detalle = trim($process->getErrorOutput());

            if ($detalle === '') {
                $detalle = trim($process->getOutput());
            }

            return redirect()
                ->route('planos.index')
                ->with(
                    'error',
                    'Error al ejecutar Python: '
                    . ($detalle ?: 'el proceso no devolvió detalles.')
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Obtener salida
        |--------------------------------------------------------------------------
        */

        $salida = trim(
            $process->getOutput()
        );

        /*
        |--------------------------------------------------------------------------
        | Convertir JSON
        |--------------------------------------------------------------------------
        */

        $resultado = json_decode(
            $salida,
            true
        );

        /*
        |--------------------------------------------------------------------------
        | Verificar JSON
        |--------------------------------------------------------------------------
        */

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            !is_array($resultado)
        ) {
            return redirect()
                ->route('planos.index')
                ->with(
                    'error',
                    'Python no devolvió un JSON válido. Respuesta: '
                    . $salida
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Verificar error de Python
        |--------------------------------------------------------------------------
        */

        if (
            isset($resultado['estado']) &&
            $resultado['estado'] === 'error'
        ) {
            return redirect()
                ->route('planos.index')
                ->with(
                    'error',
                    $resultado['mensaje']
                    ?? 'Python no pudo analizar el plano.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Guardar análisis visual
        |--------------------------------------------------------------------------
        */

        $plano->update([
            'procesado' => true,
            'datos_extraidos' => json_encode($resultado, JSON_UNESCAPED_UNICODE),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Cargar proyecto
        |--------------------------------------------------------------------------
        */

        $plano->load('proyecto');

        /*
        |--------------------------------------------------------------------------
        | Mostrar resultado
        |--------------------------------------------------------------------------
        */

        return view(
            'planos.resultado',
            compact(
                'resultado',
                'plano'
            )
        );
    }


    /**
     * Eliminar plano
     */
    public function destroy(Plano $plano)
    {
        /*
        |--------------------------------------------------------------------------
        | Verificar acceso
        |--------------------------------------------------------------------------
        */

        $this->verificarAcceso($plano);

        /*
        |--------------------------------------------------------------------------
        | Eliminar archivo original
        |--------------------------------------------------------------------------
        */

        if (
            $plano->ruta_archivo &&
            Storage::disk('planos')->exists(
                $plano->ruta_archivo
            )
        ) {
            Storage::disk('planos')->delete(
                $plano->ruta_archivo
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Eliminar archivo analizado
        |--------------------------------------------------------------------------
        */

        if ($plano->ruta_archivo) {

            $analizado = pathinfo($plano->ruta_archivo, PATHINFO_DIRNAME) . '/' . pathinfo($plano->ruta_archivo, PATHINFO_FILENAME) . '_analizado.png';

            if (
                Storage::disk('planos')->exists(
                    $analizado
                )
            ) {
                Storage::disk('planos')->delete(
                    $analizado
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Eliminar cálculos relacionados
        |--------------------------------------------------------------------------
        */

        if (method_exists($plano, 'calculos')) {
            $plano->calculos()->delete();
        }

        /*
        |--------------------------------------------------------------------------
        | Eliminar registro
        |--------------------------------------------------------------------------
        */

        $plano->delete();

        /*
        |--------------------------------------------------------------------------
        | Regresar
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('planos.index')
            ->with(
                'success',
                'Plano eliminado correctamente.'
            );
    }
}