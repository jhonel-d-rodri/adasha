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
    ];

    protected $casts = [
        'procesado' => 'boolean',
        'longitud_total' => 'float',
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
}