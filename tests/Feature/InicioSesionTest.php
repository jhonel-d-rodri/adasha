<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InicioSesionTest extends TestCase
{
    use RefreshDatabase;

    public function test_formulario_disponible(): void
    {
        $this->get('/login')->assertOk()->assertSee('password')->assertSee('email');
    }

    public function test_credenciales_validas_permiten_ingresar(): void
    {
        $user = User::factory()->create(['role' => 'usuario', 'password' => 'ClavePrueba123!']);
        $this->post('/login', ['email' => $user->email, 'password' => 'ClavePrueba123!'])
            ->assertRedirect(route('dashboard'))->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
    }

    public function test_contrasena_incorrecta_rechaza_acceso(): void
    {
        $user = User::factory()->create(['password' => 'ClavePrueba123!']);
        $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'Incorrecta'])
            ->assertRedirect('/login')->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_usuario_inexistente_rechaza_acceso(): void
    {
        $this->from('/login')->post('/login', ['email' => 'inexistente@example.test', 'password' => 'Incorrecta'])
            ->assertRedirect('/login')->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_campos_vacios_son_obligatorios(): void
    {
        $this->post('/login', [])->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_correo_es_obligatorio(): void
    {
        $this->post('/login', ['password' => 'ClavePrueba123!'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_contrasena_es_obligatoria(): void
    {
        $this->post('/login', ['email' => 'prueba@example.test'])->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_dashboard_requiere_sesion(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
