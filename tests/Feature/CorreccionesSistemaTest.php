<?php

namespace Tests\Feature;

use App\Models\{User, Proyecto, Plano, Material, CalculoMaterial};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CorreccionesSistemaTest extends TestCase
{
    use RefreshDatabase;

    private function plano(): Plano
    {
        $user = User::factory()->create(['role' => 'usuario']);
        $this->actingAs($user);
        $proyecto = Proyecto::create(['user_id' => $user->id, 'nombre' => 'Prueba', 'cliente' => 'Cliente']);
        return Plano::create(['proyecto_id' => $proyecto->id, 'nombre' => 'Plano', 'nombre_archivo' => 'plano.png', 'ruta_archivo' => 'planos/plano.png', 'procesado' => true, 'longitud_total' => 12]);
    }

    private function datos(): array
    {
        return ['longitud_total' => 20, 'plantas' => 1, 'tipo_cubierta' => 'liviana', 'area_vanos' => 0];
    }

    public function test_archivos_solo_para_propietario_y_admin(): void
    {
        Storage::fake('planos');
        $plano = $this->plano();
        Storage::disk('planos')->put($plano->ruta_archivo, 'contenido');
        $this->get(route('planos.archivo', $plano))->assertOk();
        $this->actingAs(User::factory()->create(['role' => 'usuario']))
            ->get(route('planos.archivo', $plano))->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('planos.archivo', $plano))->assertOk();
        auth()->logout();
        $this->get(route('planos.archivo', $plano))->assertRedirect(route('login'));
    }

    public function test_vanos_invalidos_no_modifican_plano(): void
    {
        $plano = $this->plano();
        $this->post(route('calculos.calcular', $plano), array_replace($this->datos(), ['area_vanos' => 999]))
            ->assertSessionHasErrors('area_vanos');
        $this->assertSame(12.0, $plano->fresh()->longitud_total);
        $this->assertDatabaseCount('calculo_materials', 0);
    }

    public function test_calculo_requiere_longitud_real_y_funciona_sin_estimacion(): void
    {
        $plano = $this->plano();
        $plano->update(['longitud_total' => null]);
        $this->get(route('planos.calcular-materiales', $plano))->assertOk();
        $datos = $this->datos();
        unset($datos['longitud_total']);
        $this->post(route('calculos.calcular', $plano), $datos)->assertSessionHasErrors('longitud_total');
        $this->post(route('calculos.calcular', $plano), $this->datos())->assertSessionHasNoErrors();
        $this->assertSame(20.0, $plano->fresh()->longitud_total);
        $this->assertGreaterThan(0, $plano->calculos()->count());
    }

    public function test_fallo_al_recalcular_conserva_datos_anteriores(): void
    {
        $plano = $this->plano();
        $material = Material::create(['nombre' => 'Anterior', 'unidad' => 'kg', 'stock' => 0]);
        $anterior = CalculoMaterial::create(['plano_id' => $plano->id, 'proyecto_id' => $plano->proyecto_id, 'material_id' => $material->id, 'cantidad' => 7]);
        CalculoMaterial::creating(function () { throw new \RuntimeException('Fallo simulado'); });
        $this->withoutExceptionHandling();
        try {
            $this->post(route('calculos.calcular', $plano), $this->datos());
            $this->fail('Se esperaba el fallo simulado');
        } catch (\RuntimeException $e) {
            $this->assertSame('Fallo simulado', $e->getMessage());
        } finally {
            CalculoMaterial::flushEventListeners();
        }
        $this->assertSame(12.0, $plano->fresh()->longitud_total);
        $this->assertDatabaseHas('calculo_materials', ['id' => $anterior->id, 'cantidad' => 7]);
    }

    public function test_pdf_rechazado_y_imagen_guardada_en_privado(): void
    {
        Storage::fake('planos');
        $plano = $this->plano();
        $datos = ['proyecto_id' => $plano->proyecto_id, 'nombre' => 'Nuevo'];
        $this->post(route('planos.store'), $datos + ['archivo' => UploadedFile::fake()->create('plano.pdf', 10, 'application/pdf')])
            ->assertSessionHasErrors('archivo');
        $imagen = UploadedFile::fake()->createWithContent('nuevo.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aY9sAAAAASUVORK5CYII='));
        $this->post(route('planos.store'), $datos + ['archivo' => $imagen])
            ->assertSessionHasNoErrors();
        Storage::disk('planos')->assertExists(Plano::latest('id')->first()->ruta_archivo);
    }

    public function test_crear_proyecto_redirige_al_formulario_existente(): void
    {
        $this->plano();
        $this->get(route('proyectos.create'))->assertRedirect(route('proyectos.index', ['crear' => 1]));
        $this->get(route('proyectos.index', ['crear' => 1]))->assertOk();
    }

    public function test_login_limita_intentos(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.process'), ['email' => 'limite@example.com', 'password' => 'incorrecta'])->assertRedirect();
        }
        $this->post(route('login.process'), ['email' => 'limite@example.com', 'password' => 'incorrecta'])->assertStatus(429);
    }

    public function test_google_rechaza_callback_sin_estado(): void
    {
        config(['services.google.client_id' => 'prueba', 'services.google.client_secret' => 'prueba', 'services.google.redirect' => 'http://localhost/auth/google/callback']);
        $this->get(route('google.callback', ['code' => 'invalido']))->assertRedirect(route('login'))->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_ejemplo_dos_plantas_descuenta_vanos_y_calcula_ambas_cubiertas(): void
    {
        $plano = $this->plano();
        $plano->proyecto->update([
            'altura_muro' => 2.40, 'espesor_muro' => 0.15,
            'largo_ladrillo' => 0.24, 'ancho_ladrillo' => 0.12, 'alto_ladrillo' => 0.09,
        ]);
        // Vanos: una entrada, ocho puertas interiores y ocho ventanas.
        $vanos = (0.90 * 2.10) + (8 * 0.80 * 2.10) + (8 * 1.20 * 1.20);
        $datos = [
            'longitud_total' => 46, 'plantas' => 2, 'area_vanos' => round($vanos, 2),
            'superficie_construida' => 48, 'tipo_cubierta' => 'losa',
            'cimentacion' => 1, 'columnas' => 1, 'vigas' => 1,
        ];
        $this->post(route('calculos.calcular', $plano), $datos)->assertRedirect()->assertSessionHasNoErrors();
        // 46 * 2.40 * 2 - 26.85 = 193.95 m2 netos; modulo 0.25 * 0.10 y merma 10%.
        $cantidades = $plano->calculos()->with('material')->get()->mapWithKeys(fn ($c) => [$c->material->nombre => (float) $c->cantidad]);
        $this->assertSame(8534.0, $cantidades['Ladrillo Gambote']);
        $this->assertCount(8, $cantidades);
        $this->assertArrayNotHasKey('Madera de encofrado', $cantidades);
        $this->get(route('reportes.materiales', $plano))->assertOk()->assertSee('Ladrillo Gambote');

        $datos['tipo_cubierta'] = 'liviana';
        $this->post(route('calculos.calcular', $plano), $datos)->assertRedirect()->assertSessionHasNoErrors();
        $liviana = $plano->calculos()->with('material')->get()->mapWithKeys(fn ($c) => [$c->material->nombre => (float) $c->cantidad]);
        $this->assertCount(10, $liviana);
        $this->assertSame(8534.0, $liviana['Ladrillo Gambote']);
        $this->assertSame(1.44, $liviana['Madera de encofrado']);
        $this->assertSame(4.32, $liviana['Clavos']);
        $this->assertLessThan($cantidades['Cemento'], $liviana['Cemento']);
    }

    public function test_rotulo_ocr_se_muestra_y_propone_sin_guardar_como_medida_confirmada(): void
    {
        $plano = $this->plano();
        $resultado = ['estado' => 'ok', 'medidas_sugeridas' => [
            'origen' => 'rotulo_ocr', 'longitud_por_planta' => 46, 'plantas' => 2, 'area_vanos' => 26.85,
        ]];
        $plano->update(['longitud_total' => null, 'datos_extraidos' => json_encode($resultado)]);
        $this->view('planos.resultado', compact('plano', 'resultado'))
            ->assertSee('46,00 m')->assertDontSee('Pendiente de confirmar');
        $this->get(route('planos.calcular-materiales', $plano))->assertOk()
            ->assertSee('value="46"', false)->assertSee('value="26.85"', false)
            ->assertSee('value="2" selected', false);
        $this->assertNull($plano->fresh()->longitud_total);
    }

    public function test_analisis_sin_rotulo_no_propone_longitud_antigua(): void
    {
        $plano = $this->plano();
        $resultado = ['estado' => 'ok', 'longitud_total' => 80, 'cotas_detectadas' => []];
        $plano->update(['datos_extraidos' => json_encode($resultado)]);
        $this->assertSame([], $plano->medidasSugeridas());
        $this->view('planos.resultado', compact('plano', 'resultado'))->assertSee('No determinada');
        $this->get(route('planos.calcular-materiales', $plano))->assertOk()
            ->assertSee('value="" required', false);
    }
}
