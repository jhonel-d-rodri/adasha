<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            if (! Schema::hasColumn('proyectos', 'ladrillo_largo')) {
                $table->decimal('ladrillo_largo', 8, 3)->nullable();
            }

            if (! Schema::hasColumn('proyectos', 'ladrillo_ancho')) {
                $table->decimal('ladrillo_ancho', 8, 3)->nullable();
            }

            if (! Schema::hasColumn('proyectos', 'ladrillo_alto')) {
                $table->decimal('ladrillo_alto', 8, 3)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {

            $table->dropColumn([
                'ladrillo_largo',
                'ladrillo_ancho',
                'ladrillo_alto'
            ]);

        });
    }
};
