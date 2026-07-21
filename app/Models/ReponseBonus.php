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

        return self::correspond($this->reponse, $reponseCorrecte) ? 5 : 0;
    }

    /**
     * Compare deux réponses en tolérant les petites fautes de frappe
     * (accents, casse, espaces, et une poignée de caractères d'écart
     * proportionnelle à la longueur du mot attendu).
     */
    public static function correspond(string $reponse, string $attendue): bool
    {
        $normaliseeReponse = self::normaliser($reponse);
        $normaliseeAttendue = self::normaliser($attendue);

        if ($normaliseeReponse === $normaliseeAttendue) {
            return true;
        }

        $toleranceMax = match (true) {
            mb_strlen($normaliseeAttendue) <= 3 => 0,
            mb_strlen($normaliseeAttendue) <= 6 => 1,
            default => 2,
        };

        return $toleranceMax > 0 && levenshtein($normaliseeReponse, $normaliseeAttendue) <= $toleranceMax;
    }

    private static function normaliser(string $valeur): string
    {
        $valeur = mb_strtolower(trim($valeur));
        $valeur = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $valeur) ?: $valeur;
        $valeur = preg_replace('/[^a-z0-9]+/', ' ', $valeur);

        return trim(preg_replace('/\s+/', ' ', $valeur));
    }
}
