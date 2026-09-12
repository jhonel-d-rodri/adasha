<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalculoMaterial extends Model
{
    protected $table = 'calculo_materials';
    
    protected $fillable = [
        'proyecto_id',
        'plano_id',
        'material_id',
        'cantidad'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function plano()
    {
        return $this->belongsTo(Plano::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}