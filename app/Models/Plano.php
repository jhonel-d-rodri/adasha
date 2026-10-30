<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CalculoMaterial;

class Plano extends Model
{
    protected $fillable = [
        'proyecto_id',
        'nombre',
        'nombre_archivo',
        'ruta_archivo',
        'tipo_plano',
        'procesado',
        'datos_extraidos',
        'longitud_total',
        'plantas',
        'tipo_cubierta',
        'cimentacion',
        'columnas',
        'vigas',
        'superficie_construida',
        'area_vanos',
    ];

    protected $casts = [
        'procesado' => 'boolean',
        'longitud_total' => 'float',
        'plantas' => 'integer',
        'cimentacion' => 'boolean',
        'columnas' => 'boolean',
        'vigas' => 'boolean',
        'superficie_construida' => 'float',
        'area_vanos' => 'float',
    ];

    /**
     * Relación con proyecto
     */
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    /**
     * Relación con cálculos de materiales
     */
    public function calculos()
    {
        return $this->hasMany(CalculoMaterial::class);
    }

    /**
     * Verificar si el archivo es una imagen
     */
    public function esImagen()
    {
        return in_array(
            strtolower(pathinfo($this->nombre_archivo, PATHINFO_EXTENSION)),
            ['jpg', 'jpeg', 'png']
        );
    }

    /**
     * Verificar si el plano ya fue analizado
     */
    public function estaAnalizado()
    {
        return $this->longitud_total !== null
            && $this->longitud_total > 0;
    }

    public function medidasSugeridas(): array
    {
        $datos = is_array($this->datos_extraidos)
            ? $this->datos_extraidos
            : json_decode($this->datos_extraidos ?? '{}', true);
        $medidas = $datos['medidas_sugeridas'] ?? [];
        if (!is_array($medidas) || ($medidas['origen'] ?? null) !== 'rotulo_ocr'
            || !is_numeric($medidas['longitud_por_planta'] ?? null)
            || $medidas['longitud_por_planta'] <= 0) {
            return [];
        }

        return $medidas;
    }
}
