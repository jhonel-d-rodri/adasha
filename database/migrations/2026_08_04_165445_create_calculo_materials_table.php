<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calculo_materials', function (Blueprint $table) {

            $table->id();

            $table->foreignId('proyecto_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('plano_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('material_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->decimal('cantidad',10,2);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculo_materials');

        // Ejecuta esto en php artisan tinker o en una migración
Schema::create('calculo_materials', function (Blueprint $table) {
    $table->id();
    $table->foreignId('proyecto_id')->constrained()->onDelete('cascade');
    $table->foreignId('plano_id')->constrained()->onDelete('cascade');
    $table->foreignId('material_id')->constrained()->onDelete('cascade');
    $table->decimal('cantidad', 10, 2);
    $table->timestamps();
});
    }
};