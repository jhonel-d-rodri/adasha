<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained()->onDelete('cascade');
            $table->string('nombre_archivo');
            $table->string('ruta_archivo');
            $table->enum('tipo_plano', ['planta', 'elevacion', 'corte', 'otro'])->default('planta');
            $table->boolean('procesado')->default(false);
            // Aquí se guarda el JSON que devuelve el módulo de visión artificial
            // (muros detectados, longitudes, áreas, espacios, etc.)
            $table->json('datos_extraidos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planos');
    }
};