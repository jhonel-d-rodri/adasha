<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoMaterial extends Model
{
    protected $table = 'movimientos_materiales';

    protected $fillable = ['material_id', 'user_id', 'tipo', 'cantidad', 'motivo'];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
