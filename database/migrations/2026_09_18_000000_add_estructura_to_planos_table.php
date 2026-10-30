<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planos', function (Blueprint $table) {
            $table->unsignedTinyInteger('plantas')->default(1);
            $table->string('tipo_cubierta')->default('liviana');
            $table->boolean('cimentacion')->default(true);
            $table->boolean('columnas')->default(true);
            $table->boolean('vigas')->default(true);
            $table->decimal('superficie_construida', 10, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('planos', function (Blueprint $table) {
            $table->dropColumn([
                'plantas',
                'tipo_cubierta',
                'cimentacion',
                'columnas',
                'vigas',
                'superficie_construida',
            ]);
        });
    }
};