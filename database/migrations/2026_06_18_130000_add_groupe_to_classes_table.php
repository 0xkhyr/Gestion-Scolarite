<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A `niveau` (level, e.g. 1AS) can be split into several class groups when
     * there are too many students for one room — 1AS-G1, 1AS-G2. `groupe` is
     * that group number; the (niveau, serie, groupe) trio is unique.
     */
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->unsignedTinyInteger('groupe')->default(1)->after('serie');
            $table->unique(['niveau', 'serie', 'groupe'], 'classes_niveau_serie_groupe_unique');
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropUnique('classes_niveau_serie_groupe_unique');
            $table->dropColumn('groupe');
        });
    }
};
