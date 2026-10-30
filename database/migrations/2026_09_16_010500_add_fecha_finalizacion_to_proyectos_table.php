<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the scheduled completion date to existing project tables.
     *
     * Some databases recorded the earlier migration as run before this
     * column was present, so this migration is intentionally idempotent.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('proyectos', 'fecha_finalizacion')) {
            Schema::table('proyectos', function (Blueprint $table) {
                $table->date('fecha_finalizacion')
                    ->nullable()
                    ->after('fecha_inicio');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('proyectos', 'fecha_finalizacion')) {
            Schema::table('proyectos', function (Blueprint $table) {
                $table->dropColumn('fecha_finalizacion');
            });
        }
    }
};
