<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';

    protected $fillable = [
        'nombre',
        'unidad',
        'stock',
        'stock_minimo',
        'precio_unitario',
        'descripcion'
    ];

    public function calculos()
    {
        return $this->hasMany(CalculoMaterial::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoMaterial::class);
    }
}
