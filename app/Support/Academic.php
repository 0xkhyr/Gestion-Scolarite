<?php

namespace App\Support;

/**
 * Thin helper over config/academic.php — the canonical Mauritanian academic
 * structure (levels, cycles, séries, mentions). Keeps Resources and services
 * free of structural lookups.
 */
class Academic
{
    /**
     * All levels grouped by cycle, ready for a Filament grouped Select:
     * [ 'Fondamental' => ['1AF' => '1AF (translated)', ...], ... ].
     */
    public static function levelOptionsGrouped(): array
    {
        $groups = [];

        foreach (config('academic.cycles', []) as $cycle) {
            $label = __('app.' . $cycle['label_key']);
            $groups[$label] = [];

            foreach ($cycle['levels'] as $code) {
                $groups[$label][$code] = self::levelLabel($code);
            }
        }

        return $groups;
    }

    /**
     * Flat list of every level code, in canonical order.
     */
    public static function allLevels(): array
    {
        return collect(config('academic.cycles', []))
            ->flatMap(fn ($cycle) => $cycle['levels'])
            ->values()
            ->all();
    }

    /**
     * Translated label for a level code (falls back to the code itself).
     */
    public static function levelLabel(string $code): string
    {
        $key = 'app.' . $code;
        $translated = __($key);

        return $translated === $key ? $code : $translated;
    }

    /**
     * Cycle key ('fondamental'|'college'|'lycee') for a level code, or null.
     */
    public static function cycleForLevel(?string $code): ?string
    {
        if (! $code) {
            return null;
        }

        foreach (config('academic.cycles', []) as $key => $cycle) {
            if (in_array($code, $cycle['levels'], true)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Does this level carry a série (lycée only)?
     */
    public static function levelHasSeries(?string $code): bool
    {
        $cycle = self::cycleForLevel($code);

        return $cycle ? (bool) config("academic.cycles.{$cycle}.has_series") : false;
    }

    /**
     * Is the per-subject max grade locked at /20 for this level?
     * (true for secondary: collège + lycée; false for fondamental.)
     */
    public static function levelNoteMaxLocked(?string $code): bool
    {
        $cycle = self::cycleForLevel($code);

        // Default to locked when the level is unknown — /20 is the safe norm.
        return $cycle ? (bool) config("academic.cycles.{$cycle}.locked_note_max") : true;
    }

    /**
     * Série select options: ['C' => 'C — Sciences mathématiques', ...].
     */
    public static function serieOptions(): array
    {
        $options = [];

        foreach (config('academic.series', []) as $code) {
            $options[$code] = $code . ' — ' . __('app.serie_' . strtolower($code));
        }

        return $options;
    }

    public static function maxGrade(): int
    {
        return (int) config('academic.max_grade', 20);
    }

    public static function passingGrade(): float
    {
        return (float) setting('academic.passing_grade', config('academic.passing_grade', 10));
    }

    /**
     * Mention key ('tres_bien'|'bien'|'assez_bien'|'passable'|'insuffisant')
     * for a /20 average.
     */
    public static function mentionKey(float $average): string
    {
        foreach (config('academic.mentions', []) as $tier) {
            if ($average >= $tier['min']) {
                return $tier['key'];
            }
        }

        return 'insuffisant';
    }
}
