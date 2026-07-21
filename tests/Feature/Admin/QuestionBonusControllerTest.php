<?php

namespace Tests\Feature\Admin;

use App\Models\Phase;
use App\Models\QuestionBonus;
use App\Models\ReponseBonus;
use App\Models\User;
use App\Notifications\BonusResolu;
use App\Notifications\NouvelleQuestionBonus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class QuestionBonusControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_questions_bonus_admin(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/questions-bonus');

        $response->assertForbidden();
    }

    public function test_admin_can_view_questions_bonus_index(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();
        QuestionBonus::factory()->create([
            'phase_id' => $phase->id,
            'question' => 'Qui va gagner ?',
        ]);

        $response = $this->actingAs($admin)->get('/admin/questions-bonus');

        $response->assertOk();
        $response->assertSee('Qui va gagner ?');
    }

    public function test_admin_can_create_a_question_bonus_and_joueurs_are_notified(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $joueur = User::factory()->create();
        $phase = Phase::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/questions-bonus', [
            'phase_id' => $phase->id,
            'question' => 'Qui remportera le tournoi ?',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('questions_bonus', [
            'phase_id' => $phase->id,
            'question' => 'Qui remportera le tournoi ?',
            'reponse_correcte' => null,
        ]);

        Notification::assertSentTo($joueur, NouvelleQuestionBonus::class);
    }

    public function test_setting_the_correct_answer_calculates_points_and_notifies_responders(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();
        $question = QuestionBonus::factory()->create([
            'phase_id' => $phase->id,
            'reponse_correcte' => null,
        ]);

        $bonneReponse = User::factory()->create();
        $mauvaiseReponse = User::factory()->create();

        ReponseBonus::factory()->for($bonneReponse)->for($question, 'questionBonus')->create(['reponse' => 'Julien']);
        ReponseBonus::factory()->for($mauvaiseReponse)->for($question, 'questionBonus')->create(['reponse' => 'Marc']);

        $response = $this->actingAs($admin)->put("/admin/questions-bonus/{$question->id}", [
            'phase_id' => $phase->id,
            'question' => $question->question,
            'reponse_correcte' => 'Julien',
        ]);

        $response->assertRedirect();

        $this->assertSame(5, ReponseBonus::where('user_id', $bonneReponse->id)->first()->points_obtenus);
        $this->assertSame(0, ReponseBonus::where('user_id', $mauvaiseReponse->id)->first()->points_obtenus);

        Notification::assertSentTo($bonneReponse, BonusResolu::class);
        Notification::assertSentTo($mauvaiseReponse, BonusResolu::class);
    }

    public function test_updating_without_changing_the_answer_does_not_renotify(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();
        $question = QuestionBonus::factory()->create([
            'phase_id' => $phase->id,
            'reponse_correcte' => 'Julien',
        ]);

        $response = $this->actingAs($admin)->put("/admin/questions-bonus/{$question->id}", [
            'phase_id' => $phase->id,
            'question' => 'Question modifiée',
            'reponse_correcte' => 'Julien',
        ]);

        $response->assertRedirect();
        Notification::assertNothingSent();
    }

    public function test_admin_cannot_delete_a_question_with_responses(): void
    {
        $admin = User::factory()->admin()->create();
        $question = QuestionBonus::factory()->create();
        ReponseBonus::factory()->for($question, 'questionBonus')->create();

        $response = $this->actingAs($admin)->delete("/admin/questions-bonus/{$question->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('questions_bonus', ['id' => $question->id]);
    }
}
