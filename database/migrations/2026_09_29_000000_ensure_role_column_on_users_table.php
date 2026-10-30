<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The application authorizes users with the `role` column. Older
     * installations used `rol`, so keep the data and make fresh installs work.
     */
    public function up(): void
    {
        $roleWasAdded = ! Schema::hasColumn('users', 'role');

        if ($roleWasAdded) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('usuario');
            });
        }

        if ($roleWasAdded && Schema::hasColumn('users', 'rol')) {
            DB::table('users')
                ->whereRaw('LOWER(rol) IN (?, ?)', ['administrador', 'admin'])
                ->update(['role' => 'admin']);
        }
    }

    public function down(): void
    {
        // This repair migration must not remove a pre-existing role column.
    }
};
