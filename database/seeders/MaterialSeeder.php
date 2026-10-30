<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('materials')->insert([
            [
                'nombre' => 'Ladrillo Gambote',
                'unidad' => 'unidad',
                'stock' => 0,
                'descripcion' => 'Ladrillo utilizado para muros de obra gruesa.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'nombre' => 'Cemento',
                'unidad' => 'bolsa',
                'stock' => 0,
                'descripcion' => 'Cemento para trabajos de construcción.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'nombre' => 'Arena',
                'unidad' => 'm3',
                'stock' => 0,
                'descripcion' => 'Arena utilizada para mortero y concreto.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'nombre' => 'Grava',
                'unidad' => 'm3',
                'stock' => 0,
                'descripcion' => 'Grava utilizada para la elaboración de concreto.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'nombre' => 'Fierro',
                'unidad' => 'kg',
                'stock' => 0,
                'descripcion' => 'Acero de refuerzo utilizado en elementos estructurales.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'nombre' => 'Piedra para cimiento',
                'unidad' => 'm3',
                'stock' => 0,
                'descripcion' => 'Piedra para cimentación.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Alambre de amarre',
                'unidad' => 'kg',
                'stock' => 0,
                'descripcion' => 'Alambre para armado.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Madera de encofrado',
                'unidad' => 'm3',
                'stock' => 0,
                'descripcion' => 'Madera para encofrado o cubierta liviana.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Clavos',
                'unidad' => 'kg',
                'stock' => 0,
                'descripcion' => 'Clavos para encofrado o cubierta.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Agua',
                'unidad' => 'm3',
                'stock' => 0,
                'descripcion' => 'Agua para mortero y concreto.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
