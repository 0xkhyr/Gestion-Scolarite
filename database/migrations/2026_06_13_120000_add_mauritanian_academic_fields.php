<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mauritanian academic model:
     *  - classes get a nullable `serie` (track C/D/LM/LO), used only at lycée.
     *  - matieres get a per-subject `note_max` (only meaningful for fondamental;
     *    secondary is always /20) and `serie_coefficients` for per-(subject×série)
     *    coefficients at lycée, falling back to the global `coefficient`.
     */
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->string('serie', 8)->nullable()->after('niveau');
            $table->index('serie');
        });

        Schema::table('matieres', function (Blueprint $table) {
            $table->decimal('note_max', 5, 2)->default(20)->after('coefficient');
            $table->json('serie_coefficients')->nullable()->after('note_max');
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropIndex(['serie']);
            $table->dropColumn('serie');
        });

        Schema::table('matieres', function (Blueprint $table) {
            $table->dropColumn(['note_max', 'serie_coefficients']);
        });
    }
};
