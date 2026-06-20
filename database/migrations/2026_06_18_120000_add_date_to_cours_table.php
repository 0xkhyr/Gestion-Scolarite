<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A nullable `date` turns a Cours into a one-off session (rattrapage):
     *   - date NULL  → recurring weekly slot (uses `jour`).
     *   - date set   → single session on that exact date.
     */
    public function up(): void
    {
        Schema::table('cours', function (Blueprint $table) {
            $table->date('date')->nullable()->after('jour');
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::table('cours', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropColumn('date');
        });
    }
};
