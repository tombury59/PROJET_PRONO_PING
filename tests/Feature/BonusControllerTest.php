<?php

namespace Tests\Feature;

use App\Models\Phase;
use App\Models\QuestionBonus;
use App\Models\ReponseBonus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BonusControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_bonus(): void
    {
        $response = $this->get('/bonus');

        $response->assertRedirect('/login');
    }

    public function test_index_shows_open_questions_of_current_phase(): void
    {
        $user = User::factory()->create();
        $phase = Phase::factory()->create([
            'date_debut' => now()->subDays(5),
            'date_fin' => now()->addDays(5),
        ]);
        QuestionBonus::factory()->create([
            'phase_id' => $phase->id,
            'question' => 'Qui va gagner le tournoi ?',
        ]);

        $response = $this->actingAs($user)->get('/bonus');

        $response->assertOk();
        $response->assertSee('Qui va gagner le tournoi ?');
    }

    public function test_several_questions_can_be_open_simultaneously_for_the_same_phase(): void
    {
        $user = User::factory()->create();
        $phase = Phase::factory()->create([
            'date_debut' => now()->subDays(5),
            'date_fin' => now()->addDays(5),
        ]);
        $question1 = QuestionBonus::factory()->create(['phase_id' => $phase->id, 'question' => 'Question A ?']);
        $question2 = QuestionBonus::factory()->create(['phase_id' => $phase->id, 'question' => 'Question B ?']);

        $response = $this->actingAs($user)->get('/bonus');

        $response->assertOk();
        $response->assertSee('Question A ?');
        $response->assertSee('Question B ?');

        // Chaque question doit pouvoir être répondue indépendamment.
        $this->actingAs($user)->post("/bonus/{$question1->id}", ['reponse' => 'Alice'])->assertRedirect();
        $this->actingAs($user)->post("/bonus/{$question2->id}", ['reponse' => 'Bob'])->assertRedirect();

        $this->assertDatabaseHas('reponses_bonus', ['question_bonus_id' => $question1->id, 'reponse' => 'Alice']);
        $this->assertDatabaseHas('reponses_bonus', ['question_bonus_id' => $question2->id, 'reponse' => 'Bob']);
    }

    public function test_user_can_submit_an_answer(): void
    {
        $user = User::factory()->create();
        $question = QuestionBonus::factory()->create();

        $response = $this->actingAs($user)->post("/bonus/{$question->id}", [
            'reponse' => 'Julien',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reponses_bonus', [
            'user_id' => $user->id,
            'question_bonus_id' => $question->id,
            'reponse' => 'Julien',
        ]);
    }

    public function test_user_can_update_their_answer_before_resolution(): void
    {
        $user = User::factory()->create();
        $question = QuestionBonus::factory()->create();
        ReponseBonus::factory()->for($user)->for($question, 'questionBonus')->create(['reponse' => 'Marc']);

        $response = $this->actingAs($user)->post("/bonus/{$question->id}", [
            'reponse' => 'Julien',
        ]);

        $response->assertRedirect();
        $this->assertSame(1, ReponseBonus::where('user_id', $user->id)->count());
        $this->assertSame('Julien', ReponseBonus::where('user_id', $user->id)->first()->reponse);
    }

    public function test_user_cannot_answer_a_resolved_question(): void
    {
        $user = User::factory()->create();
        $question = QuestionBonus::factory()->create(['reponse_correcte' => 'Julien']);

        $response = $this->actingAs($user)->post("/bonus/{$question->id}", [
            'reponse' => 'Marc',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('reponses_bonus', ['user_id' => $user->id]);
    }
}
