<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionBonus extends Model
{
    use HasFactory;

    protected $table = 'questions_bonus';

    protected $fillable = ['phase_id', 'match_id', 'question', 'reponse_correcte'];

    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    public function reponses(): HasMany
    {
        return $this->hasMany(ReponseBonus::class, 'question_bonus_id');
    }
}
