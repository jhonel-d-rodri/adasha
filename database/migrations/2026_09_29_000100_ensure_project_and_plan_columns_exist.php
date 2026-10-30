<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Completa columnas usadas por el sistema que no estaban declaradas en
     * las migraciones iniciales. No modifica ni elimina datos existentes.
     */
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            if (! Schema::hasColumn('proyectos', 'latitud')) {
                $table->decimal('latitud', 10, 7)->nullable();
            }

            if (! Schema::hasColumn('proyectos', 'longitud')) {
                $table->decimal('longitud', 10, 7)->nullable();
            }
        });

        if (! Schema::hasColumn('planos', 'nombre')) {
            Schema::table('planos', function (Blueprint $table) {
                $table->string('nombre')->nullable()->after('proyecto_id');
            });
        }
    }

    public function down(): void
    {
        // This repair migration must not remove columns that may pre-exist.
    }
};
