<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class InitialAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('deployment.initial_admin.email');
        $password = config('deployment.initial_admin.password');

        if (! is_string($email) || trim($email) === '' || ! is_string($password) || strlen($password) < 12) {
            $this->command?->warn('Administrador inicial omitido: configure INITIAL_ADMIN_EMAIL y una INITIAL_ADMIN_PASSWORD de al menos 12 caracteres.');

            return;
        }

        User::firstOrCreate(
            ['email' => mb_strtolower(trim($email))],
            [
                'name' => config('deployment.initial_admin.name', 'Administrador ADASHA'),
                'password' => $password,
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
        );
    }
}
