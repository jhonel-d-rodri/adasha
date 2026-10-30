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
        'fecha_finalizacion',
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
        'fecha_inicio' => 'date',
        'fecha_finalizacion' => 'date',
    ];

    public static function calcularEstadoDesdeFechas($fechaInicio, $fechaFinalizacion = null): string
    {
        $hoy = now()->startOfDay();

        if (!$fechaInicio) {
            return 'registrado';
        }

        $inicio = \Carbon\Carbon::parse($fechaInicio)->startOfDay();

        if ($hoy->lt($inicio)) {
            return 'registrado';
        }

        if ($fechaFinalizacion) {
            $fin = \Carbon\Carbon::parse($fechaFinalizacion)->startOfDay();

            if ($hoy->greaterThanOrEqualTo($fin)) {
                return 'finalizado';
            }

            $duracionDias = max(1, $inicio->diffInDays($fin, false));
            $diasTranscurridos = max(0, $inicio->diffInDays($hoy, false));
            $umbralProceso = (int) ceil($duracionDias * 0.50);

            if ($diasTranscurridos >= $umbralProceso) {
                return 'en_proceso';
            }

            return 'registrado';
        }

        return 'registrado';
    }

    public function getEstadoActualAttribute(): string
    {
        return self::calcularEstadoDesdeFechas(
            $this->fecha_inicio,
            $this->fecha_finalizacion
        );
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function planos()
    {
        return $this->hasMany(Plano::class);
    }

    public function calculos()
    {
        return $this->hasMany(CalculoMaterial::class);
    }
}