<?php

namespace Database\Seeders;

use App\Models\Phase;
use Illuminate\Database\Seeder;

class PhaseSeeder extends Seeder
{
    /**
     * Les dates sont relatives à "maintenant" pour que la phase 3 reste
     * toujours la phase en cours, quel que soit le moment où le seeder
     * est exécuté.
     */
    public function run(): void
    {
        Phase::create([
            'nom' => 'Phase 1 (Septembre - Décembre)',
            'date_debut' => now()->subMonths(10),
            'date_fin' => now()->subMonths(7),
            'reset_classement' => true,
        ]);

        Phase::create([
            'nom' => 'Phase 2 (Janvier - Mai)',
            'date_debut' => now()->subMonths(6),
            'date_fin' => now()->subMonths(2),
            'reset_classement' => true,
        ]);

        Phase::create([
            'nom' => 'Phase 3 (en cours)',
            'date_debut' => now()->subMonth(),
            'date_fin' => now()->addMonths(2),
            'reset_classement' => true,
        ]);
    }
}
