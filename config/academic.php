<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mauritanian Academic Structure (canonical reference)
    |--------------------------------------------------------------------------
    |
    | This is structural reference data, not user settings. Admin-tunable values
    | (passing grade, grading system, terms per year, per-subject maxes and
    | coefficients) live in the `settings` table and on the `matieres` rows.
    |
    | Levels (`niveau`) are stable identifier codes; their human labels are
    | translated via __('app.<code>') so the same code renders in fr/ar/en.
    |
    */

    // Grading is /20 nationwide; a student passes at 10/20.
    'max_grade' => 20,
    'passing_grade' => 10,
    'terms_per_year' => 3,

    /*
    | Cycles and the levels they contain, in order.
    |
    |  - has_series     : whether classes at this cycle carry a série (track).
    |  - locked_note_max: when true, every subject is /20 and the admin cannot
    |                     change it. Only fondamental allows a per-subject max.
    */
    'cycles' => [
        'fondamental' => [
            'label_key' => 'cycle_fondamental',
            'levels' => ['1AF', '2AF', '3AF', '4AF', '5AF', '6AF'],
            'has_series' => false,
            'locked_note_max' => false,
        ],
        'college' => [
            'label_key' => 'cycle_college',
            'levels' => ['1AS', '2AS', '3AS', '4AS'],
            'has_series' => false,
            'locked_note_max' => true,
        ],
        'lycee' => [
            'label_key' => 'cycle_lycee',
            'levels' => ['5AS', '6AS', '7AS'],
            'has_series' => true,
            'locked_note_max' => true,
        ],
    ],

    /*
    | Séries (tracks) — lycée only. Labels translated via __('app.serie_<key>').
    |   C  = Sciences mathématiques
    |   D  = Sciences naturelles
    |   LM = Lettres modernes
    |   LO = Lettres originelles (Arabic-Islamic)
    */
    'series' => ['C', 'D', 'LM', 'LO'],

    /*
    | Mentions on /20 (highest threshold first). Labels via __('app.mention_<key>').
    */
    'mentions' => [
        ['min' => 16, 'key' => 'tres_bien'],
        ['min' => 14, 'key' => 'bien'],
        ['min' => 12, 'key' => 'assez_bien'],
        ['min' => 10, 'key' => 'passable'],
        ['min' => 0,  'key' => 'insuffisant'],
    ],
];
