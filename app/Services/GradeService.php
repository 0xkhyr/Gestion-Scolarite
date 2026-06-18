<?php

namespace App\Services;

use InvalidArgumentException;
use Exception;
use App\Models\Note;
use App\Models\Etudiant;
use App\Models\Evaluation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class GradeService
{
    /**
     * Create or update a grade with activity logging
     *
     * @param int $etudiantId
     * @param int $evaluationId
     * @param float $note
     * @param string|null $commentaire
     * @return Note
     * @throws Exception
     */
    public function saveGrade(int $etudiantId, int $evaluationId, float $note, ?string $commentaire = null): Note
    {
        try {
            DB::beginTransaction();

            // Validate student exists
            $etudiant = Etudiant::findOrFail($etudiantId);
            
            // Validate evaluation exists
            $evaluation = Evaluation::findOrFail($evaluationId);
            
            // Validate grade is within range
            $noteMax = $evaluation->note_max ?? 20;
            if ($note < 0 || $note > $noteMax) {
                throw new InvalidArgumentException("Note must be between 0 and {$noteMax}");
            }

            // Find or create grade
            $grade = Note::firstOrNew([
                'id_etudiant' => $etudiantId,
                'id_evaluation' => $evaluationId,
            ]);

            $oldNote = $grade->exists ? $grade->note : null;
            
            // Update grade
            $grade->note = $note;
            $grade->commentaire = $commentaire;
            $grade->save();

            // Log activity
            $action = $oldNote === null ? 'created' : 'updated';
            $description = $action === 'created' 
                ? "Grade created: {$note}/{$noteMax} for {$etudiant->nom} {$etudiant->prenom} in evaluation #{$evaluationId}"
                : "Grade updated from {$oldNote} to {$note} for {$etudiant->nom} {$etudiant->prenom} in evaluation #{$evaluationId}";

            activity()
                ->performedOn($grade)
                ->causedBy(Auth::user())
                ->withProperties([
                    'student_id' => $etudiantId,
                    'evaluation_id' => $evaluationId,
                    'old_grade' => $oldNote,
                    'new_grade' => $note,
                    'max_grade' => $noteMax,
                ])
                ->log($description);

            DB::commit();

            return $grade;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Grade save failed', [
                'student_id' => $etudiantId,
                'evaluation_id' => $evaluationId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a grade with activity logging
     *
     * @param int $gradeId
     * @return bool
     * @throws Exception
     */
    public function deleteGrade(int $gradeId): bool
    {
        try {
            DB::beginTransaction();

            $grade = Note::findOrFail($gradeId);
            $etudiant = $grade->etudiant;
            $evaluation = $grade->evaluation;

            // Log activity before deletion
            activity()
                ->performedOn($grade)
                ->causedBy(Auth::user())
                ->withProperties([
                    'student_id' => $grade->id_etudiant,
                    'evaluation_id' => $grade->id_evaluation,
                    'deleted_grade' => $grade->note,
                ])
                ->log("Grade deleted: {$grade->note} for {$etudiant->nom} {$etudiant->prenom} in evaluation #{$evaluation->id}");

            $grade->delete();

            DB::commit();

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Grade deletion failed', [
                'grade_id' => $gradeId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Bulk save grades for an evaluation
     *
     * @param int $evaluationId
     * @param array $grades - Array of ['student_id' => grade_value]
     * @return array
     */
    public function bulkSaveGrades(int $evaluationId, array $grades): array
    {
        $results = [
            'success' => [],
            'failed' => [],
        ];

        foreach ($grades as $studentId => $gradeValue) {
            try {
                $grade = $this->saveGrade($studentId, $evaluationId, $gradeValue);
                $results['success'][] = [
                    'student_id' => $studentId,
                    'grade_id' => $grade->id,
                ];
            } catch (Exception $e) {
                $results['failed'][] = [
                    'student_id' => $studentId,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Calculate student average for a course
     *
     * @param int $etudiantId
     * @param int $coursId
     * @return float|null
     */
    public function calculateCourseAverage(int $etudiantId, int $coursId): ?float
    {
        $grades = Note::where('id_etudiant', $etudiantId)
            ->whereHas('evaluation', function ($query) use ($coursId) {
                $query->where('id_cours', $coursId);
            })
            ->with('evaluation')
            ->get();

        if ($grades->isEmpty()) {
            return null;
        }

        // Weighted average based on coefficients
        $totalWeighted = 0;
        $totalCoefficient = 0;

        foreach ($grades as $grade) {
            $coefficient = $grade->evaluation->coefficient ?? 1;
            $noteMax = $grade->evaluation->note_max ?? 20;
            
            // Normalize to /20 scale
            $normalizedGrade = ($grade->note / $noteMax) * 20;
            
            $totalWeighted += $normalizedGrade * $coefficient;
            $totalCoefficient += $coefficient;
        }

        return $totalCoefficient > 0 ? round($totalWeighted / $totalCoefficient, 2) : null;
    }

    /**
     * Calculate student overall average
     *
     * @param int $etudiantId
     * @return float|null
     */
    public function calculateOverallAverage(int $etudiantId): ?float
    {
        $grades = Note::where('id_etudiant', $etudiantId)
            ->with('evaluation')
            ->get();

        if ($grades->isEmpty()) {
            return null;
        }

        // Simple average (can be enhanced with weights)
        $totalWeighted = 0;
        $totalCoefficient = 0;

        foreach ($grades as $grade) {
            $coefficient = $grade->evaluation->coefficient ?? 1;
            $noteMax = $grade->evaluation->note_max ?? 20;
            
            // Normalize to /20 scale
            $normalizedGrade = ($grade->note / $noteMax) * 20;
            
            $totalWeighted += $normalizedGrade * $coefficient;
            $totalCoefficient += $coefficient;
        }

        return $totalCoefficient > 0 ? round($totalWeighted / $totalCoefficient, 2) : null;
    }

    /**
     * Get grade statistics for an evaluation
     *
     * @param int $evaluationId
     * @return array
     */
    public function getEvaluationStatistics(int $evaluationId): array
    {
        $grades = Note::where('id_evaluation', $evaluationId)->pluck('note');

        if ($grades->isEmpty()) {
            return [
                'count' => 0,
                'average' => null,
                'min' => null,
                'max' => null,
                'pass_rate' => null,
            ];
        }

        $evaluation = Evaluation::find($evaluationId);
        $maxGrade = $evaluation->note_max ?? 20;
        
        // Get passing grade from settings
        $passingGradePercentage = setting('academic.passing_grade', 50); // Default 50%
        $passingGrade = ($maxGrade * $passingGradePercentage) / 100;

        return [
            'count' => $grades->count(),
            'average' => round($grades->average(), 2),
            'min' => $grades->min(),
            'max' => $grades->max(),
            'pass_rate' => round(($grades->filter(fn($g) => $g >= $passingGrade)->count() / $grades->count()) * 100, 2),
            'passing_grade' => $passingGrade,
        ];
    }
}
