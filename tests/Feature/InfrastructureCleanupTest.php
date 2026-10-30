<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class InfrastructureCleanupTest extends TestCase
{
    use RefreshDatabase;

    private function cleanup(): \Illuminate\Database\Migrations\Migration
    {
        return require database_path('migrations/2026_10_13_000000_remove_unused_infrastructure_tables.php');
    }

    public function test_cleanup_can_restore_and_remove_only_unused_tables(): void
    {
        $migration = $this->cleanup();
        $migration->down();
        $migration->down(); // Restoration must preserve tables already present.

        foreach (['sessions', 'jobs', 'job_batches', 'failed_jobs'] as $table) {
            $this->assertTrue(Schema::hasTable($table));
        }

        $migration->up();

        foreach (['sessions', 'jobs', 'job_batches', 'failed_jobs'] as $table) {
            $this->assertFalse(Schema::hasTable($table));
        }

        foreach (['users', 'proyectos', 'planos', 'materials', 'calculo_materials',
            'movimientos_materiales', 'cache', 'cache_locks', 'password_reset_tokens', 'migrations'] as $table) {
            $this->assertTrue(Schema::hasTable($table));
        }
    }

    public function test_cleanup_refuses_nonempty_tables_before_dropping_any_table(): void
    {
        $migration = $this->cleanup();
        $migration->down();
        DB::table('jobs')->insert([
            'queue' => 'default', 'payload' => '{}', 'attempts' => 0,
            'available_at' => time(), 'created_at' => time(),
        ]);

        try {
            $migration->up();
            $this->fail('La migracion debe rechazar tablas con datos.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('contiene registros', $exception->getMessage());
        }

        $this->assertTrue(Schema::hasTable('sessions'));
        $this->assertSame(1, DB::table('jobs')->count());
    }

    public function test_cleanup_refuses_an_active_database_queue(): void
    {
        $migration = $this->cleanup();
        $migration->down();
        config(['queue.default' => 'database']);

        try {
            $migration->up();
            $this->fail('La migracion debe rechazar una cola activa en base de datos.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('Configure sesiones', $exception->getMessage());
        }

        $this->assertTrue(Schema::hasTable('jobs'));
    }
}
