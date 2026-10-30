<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = ['sessions', 'jobs', 'job_batches', 'failed_jobs'];

    public function up(): void
    {
        if (config('session.driver') === 'database'
            || config('queue.default') === 'database'
            || in_array(config('queue.failed.driver'), ['database', 'database-uuids'], true)) {
            throw new RuntimeException('Configure sesiones en archivos y colas sin base de datos antes de retirar estas tablas.');
        }

        // Check every table before the first DROP: never discard existing data.
        foreach (self::TABLES as $table) {
            if (Schema::hasTable($table) && DB::table($table)->exists()) {
                throw new RuntimeException("No se elimina {$table}: contiene registros. Respalde y revise esos datos primero.");
            }
        }

        // MySQL DDL cannot be rolled back transactionally. Save the exact schema
        // before any DROP; down() also provides a portable restoration.
        if (DB::connection()->getDriverName() === 'mysql') {
            $statements = [];
            foreach (self::TABLES as $table) {
                if (Schema::hasTable($table)) {
                    $definition = (array) DB::selectOne("SHOW CREATE TABLE `{$table}`");
                    $statements[] = $definition['Create Table'] . ';';
                }
            }

            if ($statements !== []) {
                $directory = storage_path('app/private/db-backups');
                File::ensureDirectoryExists($directory);
                $path = $directory . '/unused-infrastructure-' . now()->format('Ymd-His-u') . '.sql';
                $sql = "-- ADASHA: respaldo de cuatro tablas verificadas como vacias.\n"
                    . "-- Importar solo si las tablas no existen.\n\n"
                    . implode("\n\n", $statements) . "\n";
                if (File::put($path, $sql) === false) {
                    throw new RuntimeException('No se pudo crear el respaldo; no se eliminaran las tablas.');
                }
            }
        }

        foreach (self::TABLES as $table) {
            Schema::dropIfExists($table);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        if (! Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->id();
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }

        if (! Schema::hasTable('job_batches')) {
            Schema::create('job_batches', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('name');
                $table->integer('total_jobs');
                $table->integer('pending_jobs');
                $table->integer('failed_jobs');
                $table->longText('failed_job_ids');
                $table->mediumText('options')->nullable();
                $table->integer('cancelled_at')->nullable();
                $table->integer('created_at');
                $table->integer('finished_at')->nullable();
            });
        }

        if (! Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }
    }
};
