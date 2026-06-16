<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Attendance is recorded per session (a timetabled `cours` on a given date),
     * binary present/absent. id_classe is denormalised from the cours for fast
     * role-based scoping and reporting. Part of the optional attendance module
     * (feature('attendance')).
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_etudiant');
            $table->unsignedBigInteger('id_cours');
            $table->unsignedBigInteger('id_classe');
            $table->date('date');
            $table->enum('status', ['present', 'absent'])->default('present');
            $table->unsignedBigInteger('marked_by')->nullable();
            $table->timestamps();

            $table->foreign('id_etudiant')->references('id_etudiant')->on('etudiants')->cascadeOnDelete();
            $table->foreign('id_cours')->references('id_cours')->on('cours')->cascadeOnDelete();
            $table->foreign('id_classe')->references('id_classe')->on('classes')->cascadeOnDelete();
            $table->foreign('marked_by')->references('id')->on('users')->nullOnDelete();

            // One status per student, per session, per date.
            $table->unique(['id_etudiant', 'id_cours', 'date']);
            $table->index(['id_classe', 'date']);
            $table->index(['id_cours', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
