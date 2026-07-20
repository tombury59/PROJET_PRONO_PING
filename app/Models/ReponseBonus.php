<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReponseBonus extends Model
{
    use HasFactory;

    protected $table = 'reponses_bonus';

    protected $fillable = ['user_id', 'question_bonus_id', 'reponse', 'points_obtenus'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questionBonus(): BelongsTo
    {
        return $this->belongsTo(QuestionBonus::class, 'question_bonus_id');
    }

    public function calculerPoints(): int
    {
        $reponseCorrecte = $this->questionBonus->reponse_correcte;

        if ($reponseCorrecte === null) {
            return 0;
        }

        return mb_strtolower(trim($this->reponse)) === mb_strtolower(trim($reponseCorrecte)) ? 5 : 0;
    }
}
