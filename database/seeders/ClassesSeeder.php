<?php

namespace Database\Seeders;

use App\Models\Classe;
use App\Support\Academic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassesSeeder extends Seeder
{
    /**
     * Mauritanian academic structure (see config/academic.php):
     *   - Fondamental 1AF–6AF (no série)
     *   - Collège     1AS–4AS (no série)
     *   - Lycée       5AS–7AS (séries C / D / LM / LO)
     *
     * A level may be split into several groups (1AS-G1, 1AS-G2) when there are
     * too many students for one room. `code` = niveau [+ série] -G{groupe}.
     */
    public function run(): void
    {
        $structure = [
            // [niveau, séries (null = none), number of groups]
            ['niveau' => '1AF', 'series' => [null], 'groupes' => 2],
            ['niveau' => '2AF', 'series' => [null], 'groupes' => 2],
            ['niveau' => '3AF', 'series' => [null], 'groupes' => 1],
            ['niveau' => '4AF', 'series' => [null], 'groupes' => 1],
            ['niveau' => '5AF', 'series' => [null], 'groupes' => 1],
            ['niveau' => '6AF', 'series' => [null], 'groupes' => 1],

            ['niveau' => '1AS', 'series' => [null], 'groupes' => 2],
            ['niveau' => '2AS', 'series' => [null], 'groupes' => 2],
            ['niveau' => '3AS', 'series' => [null], 'groupes' => 2],
            ['niveau' => '4AS', 'series' => [null], 'groupes' => 2],

            ['niveau' => '5AS', 'series' => ['C', 'D', 'LM', 'LO'], 'groupes' => 1],
            ['niveau' => '6AS', 'series' => ['C', 'D', 'LM', 'LO'], 'groupes' => 1],
            ['niveau' => '7AS', 'series' => ['C', 'D', 'LM', 'LO'], 'groupes' => 1],
        ];

        foreach ($structure as $row) {
            foreach ($row['series'] as $serie) {
                for ($groupe = 1; $groupe <= $row['groupes']; $groupe++) {
                    $label = Academic::levelLabel($row['niveau'])
                        . ($serie ? ' ' . $serie : '')
                        . ' — G' . $groupe;

                    Classe::create([
                        'nom_classe' => $label,
                        'niveau' => $row['niveau'],
                        'serie' => $serie,
                        'groupe' => $groupe,
                    ]);
                }
            }
        }

        $this->command->info(Classe::count() . ' classes seeded (Mauritanian levels + groups).');
    }
}
