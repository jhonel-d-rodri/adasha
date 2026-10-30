<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class FotoPerfilTest extends TestCase
{
    public function test_muestra_avatar_guardado_sin_depender_del_identificador_google(): void
    {
        $this->actingAs(User::factory()->make([
            'name' => 'Usuario de prueba',
            'avatar' => 'https://lh3.googleusercontent.com/foto-prueba',
            'google_id' => null,
        ]));

        $this->view('partials.navbar')
            ->assertSee('src="https://lh3.googleusercontent.com/foto-prueba"', false)
            ->assertSee('referrerpolicy="no-referrer"', false)
            ->assertSee('this.onerror = null', false);
    }

    public function test_avatar_vacio_usa_imagen_local(): void
    {
        $this->actingAs(User::factory()->make(['avatar' => '', 'google_id' => 'google-prueba']));

        $this->view('partials.navbar')
            ->assertSee('src="'.asset('images/avatar-default.svg').'"', false)
            ->assertDontSee('flaticon.com');
    }
}
