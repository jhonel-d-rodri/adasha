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
        'descripcion'
    ];

    public function calculos()
    {
        return $this->hasMany(CalculoMaterial::class);
    }
}