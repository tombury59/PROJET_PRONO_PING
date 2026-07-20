<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchGame extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'phase_id', 'joueur_1', 'joueur_2', 'date_heure',
        'score_j1', 'score_j2', 'resultat_saisi',
    ];

    protected function casts(): array
    {
        return [
            'date_heure' => 'datetime',
            'resultat_saisi' => 'boolean',
        ];
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }

    public function pronostics(): HasMany
    {
        return $this->hasMany(Pronostic::class, 'match_id');
    }

    public function questionsBonus(): HasMany
    {
        return $this->hasMany(QuestionBonus::class, 'match_id');
    }

    public function isVerrouille(): bool
    {
        return now()->greaterThanOrEqualTo($this->date_heure->copy()->subHour());
    }

    public function vainqueur(): ?int
    {
        if (! $this->resultat_saisi) {
            return null;
        }

        return $this->score_j1 > $this->score_j2 ? 1 : 2;
    }
}
