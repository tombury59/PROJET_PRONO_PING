<?php

namespace Tests\Unit;

use App\Models\ReponseBonus;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ReponseBonusTest extends TestCase
{
    #[DataProvider('reponsesValides')]
    public function test_correspond_tolere_les_petites_fautes_de_frappe(string $reponse, string $attendue): void
    {
        $this->assertTrue(ReponseBonus::correspond($reponse, $attendue));
    }

    public static function reponsesValides(): array
    {
        return [
            'exact' => ['Julien', 'Julien'],
            'casse différente' => ['julien', 'Julien'],
            'espaces superflus' => ['  Julien  ', 'Julien'],
            'accent manquant' => ['equipe de france', 'Équipe de France'],
            'une lettre en trop' => ['Julienn', 'Julien'],
            'une lettre manquante' => ['Julie', 'Julien'],
            'une lettre substituée' => ['Julirn', 'Julien'],
            'deux fautes sur un mot long' => ['Alexandre', 'Alexandra'],
        ];
    }

    #[DataProvider('reponsesInvalides')]
    public function test_correspond_rejette_les_reponses_trop_differentes(string $reponse, string $attendue): void
    {
        $this->assertFalse(ReponseBonus::correspond($reponse, $attendue));
    }

    public static function reponsesInvalides(): array
    {
        return [
            'mot totalement différent' => ['Marc', 'Julien'],
            'réponse vide' => ['', 'Julien'],
            'court : aucune tolérance' => ['OL', 'OM'],
            'trop de fautes' => ['Julio', 'Julien'],
        ];
    }

    public function test_calculer_points_utilise_la_tolerance(): void
    {
        $reponse = ReponseBonus::factory()->make(['reponse' => 'julien']);
        $reponse->setRelation('questionBonus', new class
        {
            public $reponse_correcte = 'Julien';
        });

        $this->assertSame(5, $reponse->calculerPoints());
    }

    public function test_calculer_points_retourne_zero_si_la_question_nest_pas_resolue(): void
    {
        $reponse = ReponseBonus::factory()->make(['reponse' => 'Julien']);
        $reponse->setRelation('questionBonus', new class
        {
            public $reponse_correcte = null;
        });

        $this->assertSame(0, $reponse->calculerPoints());
    }
}
