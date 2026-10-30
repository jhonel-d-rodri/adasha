<?php

namespace Tests\Feature;

use App\Models\CalculoMaterial;
use App\Models\Material;
use App\Models\MovimientoMaterial;
use App\Models\Plano;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Sprint4ReportesInventarioTest extends TestCase
{
    use RefreshDatabase;

    private function planoConCalculo(User $propietario): array
    {
        $proyecto = Proyecto::create([
            'user_id' => $propietario->id,
            'nombre' => 'Proyecto de prueba Sprint 4',
            'cliente' => 'Cliente Prueba',
        ]);

        $plano = Plano::create([
            'proyecto_id' => $proyecto->id,
            'nombre' => 'Plano de prueba',
            'nombre_archivo' => 'plano-prueba.png',
            'ruta_archivo' => 'planos/plano-prueba.png',
            'tipo_plano' => 'planta',
        ]);

        $material = Material::create([
            'nombre' => 'Cemento de prueba',
            'unidad' => 'bolsa',
            'stock' => 20,
            'descripcion' => 'Material para prueba',
        ]);

        CalculoMaterial::create([
            'proyecto_id' => $proyecto->id,
            'plano_id' => $plano->id,
            'material_id' => $material->id,
            'cantidad' => 12,
        ]);

        return [$proyecto, $plano, $material];
    }

    public function test_administrador_puede_descargar_pdf_de_reporte(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [, $plano] = $this->planoConCalculo($admin);

        $response = $this->actingAs($admin)
            ->get(route('reportes.descargar', $plano));

        $response->assertOk()
            ->assertDownload('reporte_materiales_Cliente_Prueba_'.$plano->id.'.pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_administrador_puede_registrar_entrada_y_salida_de_inventario(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $material = Material::create([
            'nombre' => 'Arena',
            'unidad' => 'm3',
            'stock' => 10,
        ]);

        $this->actingAs($admin)
            ->post(route('materials.movimientos.store', $material), [
                'tipo' => 'entrada', 'cantidad' => 5, 'motivo' => 'Compra',
            ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('materials', ['id' => $material->id, 'stock' => 15]);
        $this->assertDatabaseHas('movimientos_materiales', [
            'material_id' => $material->id,
            'user_id' => $admin->id,
            'tipo' => 'entrada',
            'cantidad' => 5,
            'motivo' => 'Compra',
        ]);

        $this->actingAs($admin)
            ->post(route('materials.movimientos.store', $material), [
                'tipo' => 'salida', 'cantidad' => 3, 'motivo' => 'Uso en obra',
            ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('materials', ['id' => $material->id, 'stock' => 12]);
        $this->assertSame(2, MovimientoMaterial::where('material_id', $material->id)->count());
    }

    public function test_salida_mayor_al_stock_no_actualiza_inventario(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $material = Material::create([
            'nombre' => 'Grava',
            'unidad' => 'm3',
            'stock' => 2,
        ]);

        $this->actingAs($admin)
            ->post(route('materials.movimientos.store', $material), [
                'tipo' => 'salida', 'cantidad' => 3,
            ])->assertStatus(422);

        $this->assertDatabaseHas('materials', ['id' => $material->id, 'stock' => 2]);
        $this->assertDatabaseMissing('movimientos_materiales', ['material_id' => $material->id]);
    }

    public function test_usuario_no_administrador_no_puede_gestionar_inventario(): void
    {
        $usuario = User::factory()->create(['role' => 'usuario']);
        $material = Material::create([
            'nombre' => 'Ladrillo',
            'unidad' => 'unidad',
            'stock' => 100,
        ]);

        $this->actingAs($usuario)
            ->get(route('materials.index'))
            ->assertForbidden();

        $this->actingAs($usuario)
            ->post(route('materials.movimientos.store', $material), [
                'tipo' => 'entrada', 'cantidad' => 10,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('materials', ['id' => $material->id, 'stock' => 100]);
        $this->assertDatabaseMissing('movimientos_materiales', ['material_id' => $material->id]);
    }

    public function test_usuario_propietario_puede_ver_reporte_de_su_proyecto(): void
    {
        $usuario = User::factory()->create(['role' => 'usuario']);
        [, $plano] = $this->planoConCalculo($usuario);

        $this->actingAs($usuario)
            ->get(route('reportes.materiales', $plano))
            ->assertOk()
            ->assertSee('Cemento de prueba');
    }
}
