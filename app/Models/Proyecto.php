<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $fillable = [
        'nombre',
        'cliente',
        'ubicacion',
        'latitud',
        'longitud',
        'fecha_inicio',
        'estado',
        'descripcion',
        'altura_muro',
        'espesor_muro',
        'largo_ladrillo',
        'ancho_ladrillo',
        'alto_ladrillo',
        'user_id',
    ];

    protected $casts = [
        'latitud' => 'float',
        'longitud' => 'float',
    ];

    public function planos()
    {
        return $this->hasMany(Plano::class);
    }

    public function calculos()
    {
        return $this->hasMany(CalculoMaterial::class);
    }
}