<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Repair existing rows where two_factor_enabled drifted from the source of
 * truth (two_factor_confirmed_at). From now on the User model keeps them in
 * sync on save; this fixes the historical data once.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereNotNull('two_factor_confirmed_at')
            ->update(['two_factor_enabled' => true]);

        DB::table('users')
            ->whereNull('two_factor_confirmed_at')
            ->update(['two_factor_enabled' => false]);
    }

    public function down(): void
    {
        // No-op: the column is a derived mirror, nothing meaningful to revert.
    }
};
