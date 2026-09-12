<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('proyectos', function (Blueprint $table) {
            // Verificar si la columna existe antes de agregarla
            if (!Schema::hasColumn('proyectos', 'cliente')) {
                $table->string('cliente')->nullable()->after('nombre');
            }
            if (!Schema::hasColumn('proyectos', 'ubicacion')) {
                $table->string('ubicacion')->nullable()->after('cliente');
            }
            if (!Schema::hasColumn('proyectos', 'fecha_inicio')) {
                $table->date('fecha_inicio')->nullable()->after('ubicacion');
            }
            if (!Schema::hasColumn('proyectos', 'estado')) {
                $table->string('estado')->default('Activo')->after('fecha_inicio');
            }
            if (!Schema::hasColumn('proyectos', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('estado');
            }
            if (!Schema::hasColumn('proyectos', 'altura_muro')) {
                $table->decimal('altura_muro', 8, 2)->nullable()->after('descripcion');
            }
            if (!Schema::hasColumn('proyectos', 'espesor_muro')) {
                $table->decimal('espesor_muro', 8, 3)->nullable()->after('altura_muro');
            }
            if (!Schema::hasColumn('proyectos', 'ladrillo_largo')) {
                $table->decimal('ladrillo_largo', 8, 3)->nullable()->after('espesor_muro');
            }
            if (!Schema::hasColumn('proyectos', 'ladrillo_ancho')) {
                $table->decimal('ladrillo_ancho', 8, 3)->nullable()->after('ladrillo_largo');
            }
            if (!Schema::hasColumn('proyectos', 'ladrillo_alto')) {
                $table->decimal('ladrillo_alto', 8, 3)->nullable()->after('ladrillo_ancho');
            }
        });
    }

    public function down()
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropColumn([
                'cliente',
                'ubicacion',
                'fecha_inicio',
                'estado',
                'descripcion',
                'altura_muro',
                'espesor_muro',
                'ladrillo_largo',
                'ladrillo_ancho',
                'ladrillo_alto'
            ]);
        });
    }
};