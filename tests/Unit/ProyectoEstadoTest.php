<?php

namespace Tests\Unit;

use App\Models\Proyecto;
use Carbon\Carbon;
use Tests\TestCase;

class ProyectoEstadoTest extends TestCase
{
    public function test_devuelve_registrado_cuando_el_proyecto_acaba_de_iniciar(): void
    {
        $fechaInicio = Carbon::today()->toDateString();

        $this->assertSame('registrado', Proyecto::calcularEstadoDesdeFechas($fechaInicio));
    }

    public function test_devuelve_comienzo_antes_de_la_mitad_del_tiempo(): void
    {
        $inicio = Carbon::today()->subDays(4);
        $fin = $inicio->copy()->addDays(10);

        $this->assertSame(
            'registrado',
            Proyecto::calcularEstadoDesdeFechas($inicio->toDateString(), $fin->toDateString())
        );
    }

    public function test_devuelve_finalizado_cuando_llega_la_fecha_de_finalizacion(): void
    {
        $fechaInicio = Carbon::today()->subDays(40)->toDateString();
        $fechaFinalizacion = Carbon::today()->toDateString();

        $this->assertSame(
            'finalizado',
            Proyecto::calcularEstadoDesdeFechas($fechaInicio, $fechaFinalizacion)
        );
    }

    public function test_devuelve_en_proceso_si_la_fecha_finalizacion_aun_no_llega_y_ya_hubo_avance(): void
    {
        $inicio = Carbon::today()->subDays(60);
        $fin = $inicio->copy()->addDays(120);

        $this->assertSame(
            'en_proceso',
            Proyecto::calcularEstadoDesdeFechas($inicio->toDateString(), $fin->toDateString())
        );
    }
}
