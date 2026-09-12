<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agregar características necesarias para
     * el cálculo de materiales de obra gruesa.
     */
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {

            $table->decimal('altura_muro', 5, 2)
                ->nullable();

            $table->decimal('espesor_muro', 5, 3)
                ->nullable();

            $table->decimal('largo_ladrillo', 5, 3)
                ->nullable();

            $table->decimal('ancho_ladrillo', 5, 3)
                ->nullable();

            $table->decimal('alto_ladrillo', 5, 3)
                ->nullable();
        });
    }

    /**
     * Eliminar las columnas agregadas.
     */
    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {

            $table->dropColumn([
                'altura_muro',
                'espesor_muro',
                'largo_ladrillo',
                'ancho_ladrillo',
                'alto_ladrillo'
            ]);
        });
    }
};
