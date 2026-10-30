<?php

namespace Tests\Feature;

use App\Models\CalculoMaterial;
use App\Models\Material;
use App\Models\Plano;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardActividadTest extends TestCase
{
    use RefreshDatabase;

    public function test_actividad_semanal_cuenta_registros_y_respeta_el_propietario(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 18)->setTime(12, 0));
        $usuario = User::factory()->create(['role' => 'usuario']);
        $otro = User::factory()->create(['role' => 'usuario']);
        $admin = User::factory()->create(['role' => 'admin']);
        $material = Material::create(['nombre' => 'Ladrillo', 'unidad' => 'unidad', 'stock' => 0]);

        foreach ([$usuario, $otro] as $propietario) {
            $proyecto = Proyecto::create([
                'user_id' => $propietario->id, 'nombre' => 'Proyecto de prueba',
                'cliente' => 'Cliente',
            ]);
            $proyecto->forceFill(['created_at' => '2026-10-12 00:00:00'])->save();
            $plano = Plano::create([
                'proyecto_id' => $proyecto->id, 'nombre' => 'Plano de prueba',
                'nombre_archivo' => 'prueba.png', 'ruta_archivo' => 'planos/prueba.png',
            ]);
            $plano->forceFill(['created_at' => '2026-10-18 12:00:00'])->save();
            CalculoMaterial::create([
                'proyecto_id' => $proyecto->id, 'plano_id' => $plano->id,
                'material_id' => $material->id, 'cantidad' => 10,
            ]);
        }

        // Los registros fuera de esta semana no deben aparecer en el gráfico.
        foreach (['2026-10-11 23:59:59', '2026-10-19 00:00:00'] as $fecha) {
            $proyecto = Proyecto::create([
                'user_id' => $usuario->id, 'nombre' => 'Fuera de semana', 'cliente' => 'Cliente',
            ]);
            $proyecto->forceFill(['created_at' => $fecha])->save();
        }

        $this->actingAs($usuario)->get(route('dashboard'))->assertOk()
            ->assertViewHas('actividad', [1, 0, 0, 0, 0, 0, 2]);
        $this->actingAs($admin)->get(route('dashboard'))->assertOk()
            ->assertViewHas('actividad', [2, 0, 0, 0, 0, 0, 4]);
    }
}
