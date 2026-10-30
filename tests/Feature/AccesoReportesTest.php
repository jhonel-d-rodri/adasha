<?php

namespace Tests\Feature;

use App\Models\CalculoMaterial;
use App\Models\Material;
use App\Models\Plano;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccesoReportesTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_no_puede_ver_reportes_de_otro_usuario(): void
    {
        $propietario = User::factory()->create(['role' => 'usuario']);
        $otroUsuario = User::factory()->create(['role' => 'usuario']);

        $proyecto = Proyecto::create([
            'user_id' => $propietario->id,
            'nombre' => 'Proyecto privado',
            'cliente' => 'Cliente privado',
        ]);

        $plano = Plano::create([
            'proyecto_id' => $proyecto->id,
            'nombre' => 'Plano privado',
            'nombre_archivo' => 'plano-privado.png',
            'ruta_archivo' => 'planos/plano-privado.png',
        ]);

        $material = Material::create([
            'nombre' => 'Material privado',
            'unidad' => 'unidad',
            'stock' => 0,
        ]);

        CalculoMaterial::create([
            'proyecto_id' => $proyecto->id,
            'plano_id' => $plano->id,
            'material_id' => $material->id,
            'cantidad' => 1,
        ]);

        $this->actingAs($otroUsuario)
            ->get(route('reportes.materiales', $plano))
            ->assertForbidden();

        $this->actingAs($otroUsuario)
            ->get(route('reportes.index'))
            ->assertOk()
            ->assertDontSee('Proyecto privado');
    }

    public function test_calculos_por_proyecto_solo_muestra_el_proyecto_solicitado(): void
    {
        $administrador = User::factory()->create(['role' => 'admin']);
        $usuario = User::factory()->create(['role' => 'usuario']);
        $material = Material::create([
            'nombre' => 'Cemento de prueba',
            'unidad' => 'bolsa',
            'stock' => 0,
        ]);

        $proyectoSeleccionado = Proyecto::create([
            'user_id' => $usuario->id,
            'nombre' => 'Proyecto seleccionado',
            'cliente' => 'Cliente A',
        ]);
        $otroProyecto = Proyecto::create([
            'user_id' => $usuario->id,
            'nombre' => 'Otro proyecto',
            'cliente' => 'Cliente B',
        ]);

        $planoSeleccionado = Plano::create([
            'proyecto_id' => $proyectoSeleccionado->id,
            'nombre' => 'Plano A',
            'nombre_archivo' => 'plano-a.png',
            'ruta_archivo' => 'planos/plano-a.png',
        ]);
        $otroPlano = Plano::create([
            'proyecto_id' => $otroProyecto->id,
            'nombre' => 'Plano B',
            'nombre_archivo' => 'plano-b.png',
            'ruta_archivo' => 'planos/plano-b.png',
        ]);

        $calculoSeleccionado = CalculoMaterial::create([
            'proyecto_id' => $proyectoSeleccionado->id,
            'plano_id' => $planoSeleccionado->id,
            'material_id' => $material->id,
            'cantidad' => 10,
        ]);
        CalculoMaterial::create([
            'proyecto_id' => $otroProyecto->id,
            'plano_id' => $otroPlano->id,
            'material_id' => $material->id,
            'cantidad' => 20,
        ]);

        $this->actingAs($administrador)
            ->get(route('calculos.index.proyecto', $proyectoSeleccionado->id))
            ->assertOk()
            ->assertViewHas('calculos', function ($calculos) use ($calculoSeleccionado) {
                return $calculos->count() === 1
                    && $calculos->first()->id === $calculoSeleccionado->id;
            });
    }
}
