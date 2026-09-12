<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('direccion')->nullable();
            $table->string('tipo_vivienda')->default('unifamiliar');
            $table->decimal('superficie_terreno', 10, 2)->nullable();
            $table->enum('estado', ['registrado', 'en_proceso', 'analizado', 'finalizado'])
                  ->default('registrado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};