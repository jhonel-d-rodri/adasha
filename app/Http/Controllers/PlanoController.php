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
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
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

        $nombreArchivo = time()
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
            'public'
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

        /*
        |--------------------------------------------------------------------------
        | Verificar archivo
        |--------------------------------------------------------------------------
        */

        if (
            !$plano->ruta_archivo ||
            !Storage::disk('public')->exists(
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

        $ruta = storage_path(
            'app/public/' . $plano->ruta_archivo
        );

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
            return redirect()
                ->route('planos.index')
                ->with(
                    'error',
                    'Error al ejecutar Python: '
                    . $process->getErrorOutput()
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
        | Obtener longitud detectada
        |--------------------------------------------------------------------------
        */

        $longitudTotal = floatval(
            $resultado['longitud_total'] ?? 0
        );

        /*
        |--------------------------------------------------------------------------
        | Verificar longitud
        |--------------------------------------------------------------------------
        */

        if ($longitudTotal <= 0) {
            return redirect()
                ->route('planos.index')
                ->with(
                    'error',
                    'No se pudo detectar una longitud válida en el plano.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Guardar resultado
        |--------------------------------------------------------------------------
        */

        $plano->update([
            'longitud_total' => $longitudTotal,
            'procesado' => true,
            'datos_extraidos' => json_encode(
                $resultado,
                JSON_UNESCAPED_UNICODE
            ),
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
     * Calcular materiales del plano analizado
     */
    public function calcularMateriales(Plano $plano)
    {
        /*
        |--------------------------------------------------------------------------
        | Verificar acceso
        |--------------------------------------------------------------------------
        */

        $this->verificarAcceso($plano);

        /*
        |--------------------------------------------------------------------------
        | Actualizar información
        |--------------------------------------------------------------------------
        */

        $plano->refresh();

        $plano->load('proyecto');

        /*
        |--------------------------------------------------------------------------
        | Verificar análisis
        |--------------------------------------------------------------------------
        */

        if (
            !$plano->longitud_total ||
            $plano->longitud_total <= 0
        ) {
            return redirect()
                ->route('planos.index')
                ->with(
                    'error',
                    'Primero debe analizar este plano antes de calcular los materiales.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Verificar proyecto
        |--------------------------------------------------------------------------
        */

        if (!$plano->proyecto) {
            return redirect()
                ->route('planos.index')
                ->with(
                    'error',
                    'El plano no está asociado a ningún proyecto.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Ejecutar cálculo
        |--------------------------------------------------------------------------
        */

        $controlador = app(
            \App\Http\Controllers\CalculoMaterialController::class
        );

        return $controlador->calcular($plano);
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
            Storage::disk('public')->exists(
                $plano->ruta_archivo
            )
        ) {
            Storage::disk('public')->delete(
                $plano->ruta_archivo
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Eliminar archivo analizado
        |--------------------------------------------------------------------------
        */

        if ($plano->ruta_archivo) {

            $analizado = str_replace(
                '.',
                '_analizado.',
                $plano->ruta_archivo
            );

            if (
                Storage::disk('public')->exists(
                    $analizado
                )
            ) {
                Storage::disk('public')->delete(
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