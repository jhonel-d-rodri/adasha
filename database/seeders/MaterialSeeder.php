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
        ]);
    }
}
